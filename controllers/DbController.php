<?php
include 'BaseController.php';

use pclib\extensions\TemplateFactory;

class DbController extends BaseController
{
  protected $table = 'addresses';

function indexAction()
{
  $columns = $this->db->columns($this->table);
  $grid = TemplateFactory::create('tpl/db/grid.tpl', $columns);
  $grid->setQuery("select *, id as __primary from $this->table");
  $grid->_htitle = 'TABLE ' . $this->table;
  $grid->_copy_form = new PCForm('tpl/db/form.tpl');
  return $grid;
}

function csvAction()
{
  $selected = explode(',', $_GET['selected']);
  if (!$selected) die('Nenalezeno.');
  
  $rows = $this->db->selectAll("select * from {table} where id in ({#selected})", [
    'selected' => $selected, 'table' => $this->table
  ]);

  $csv = new CsvFile;
  $csv->fromArray($rows);
  die($csv->toString());
}

function importCsvAction()
{
  $csv = new CsvFile;
  $csv->fromString($_POST['data']['csv-data']);
  dump($csv->toArray());
}

function syncAction()
{
  $form = $this->getForm();
  $table = $form->values['table'];
  $conf = $this->getConf($table);

  $sel = $form->values['__primary'];

  $changes = $this->compareTables($conf->src, $conf->dest);

  $ret = $this->syncTable($table, $changes, $sel);

  $this->app->message(paramStr('Ok. {ins} vloženo, {upd} aktualizováno, {del} smazáno.', $ret));
  $this->redirect('db');
}

protected function getPrimary($table)
{
  $indexes = $this->db->indexes($table);
  if (!$indexes['PRIMARY']) {
    $this->app->error('Tabulka nemá primární klíč.');
  }

  $col = $indexes['PRIMARY']['columns'];
  if (count($col) != 1) { 
    $this->app->error('Složený PK nepodoporován.');
  }

  return $col[0];
}

// from $s1 -> to $s2 (_sync/new -> prod/old)
function compareTablesHtml($from, $to, $showSame = false)
{
  $c1 = $this->db->columns($from);
  $c2 = $this->db->columns($to);

  if (!$c1 or !$c2 or $c1 != $c2) {
    $this->app->error('Tabulky nenalezeny, nebo mají odlišnou strukturu.');
  }

  $pk = $this->getPrimary($to);

  $s2 = [];
  foreach ($this->db->selectAll($to) as $row) {
    $s2[$row[$pk]] = $row;
  }

  $s1 = $this->db->selectAll($from);

  $ret = [];

  foreach ($s1 as $row)
  {
    $id = $row[$pk];

    if (!$s2[$id]) {
      $ret[] = $row + ['__status' => 'ins', '__primary' => $id];
      continue;
    }

    if ($s2[$id] != $row) 
    {
      //$row['__status']  = 'upd';

      foreach (array_diff($s2[$id], $row) as $key => $value) {
        $row['__'.$key.'_status'] = 'upd';
        $row['__'.$key.'_old'] = $s2[$id][$key];
        $row['__primary'] = $id;
      }

      $ret[] = $row;
    }
    elseif($showSame) {
       $row['__status']  = 'same';
       $row['__primary'] = $id;
       $ret[] = $row;
    } 

    unset($s2[$id]);
  }

  foreach ($s2 as $key => $value) {
    $ret[] = $value + ['__status' => 'del', '__primary' => $value[$pk]];
  }

  return $ret;
}


function compareTables($from, $to)
{
  $c1 = $this->db->columns($from);
  $c2 = $this->db->columns($to);

  if (!$c1 or !$c2 or $c1 != $c2) {
    $this->app->error('Tabulky nenalezeny, nebo mají odlišnou strukturu.');
  }

  $ret = [
    'insert' => [],
    'update' => [],
    'delete' => [],
  ];

  $pk = $this->getPrimary($to);

  $s2 = [];
  foreach ($this->db->selectAll($to) as $row) {
    $s2[$row[$pk]] = $row;
  }

  $s1 = $this->db->selectAll($from);

  foreach ($s1 as $row)
  {
    $id = $row[$pk];

    if (!$s2[$id]) {
      $ret['insert'][] = $row;
      continue;
    }

    if ($s2[$id] != $row) 
    {
      $ret['update'][] = $row;
    } 

    unset($s2[$id]);
  }

  $ret['delete'] = $s2;

  return $ret;
}

protected function getConf($table)
{
  $c = $this->app->config['dbsync'];

  if (!isset($c[$table])) {
    throw new Exception("Konfigurace pro '$table' nenalezena.");
  }

  return (object)$c[$table];
}


}

?>