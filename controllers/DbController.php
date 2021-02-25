<?php
include 'BaseController.php';

use pclib\extensions\TemplateFactory;

class DbController extends BaseController {

function indexAction()
{
   $form = $this->getForm();
   return $form;
}

function showAction()
{
   $form = $this->getForm();
   $table = $form->values['table'];
   $conf = $this->getConf($table);

   $changes = $this->compareTablesHtml($conf->src, $conf->dest);

   $columns = $this->db->columns($table);

   $grid = TemplateFactory::create('tpl/dbgrid.tpl', $columns);
   $grid->setArray($changes);
   $grid->_htitle = 'Rozdíl';
   $form->values['grid'] = $grid->html();

   return $form;
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

protected function getForm()
{
  $form = new PCForm('tpl/dbform.tpl', 'admin-dbform');
  $keys = array_keys($this->app->config['dbsync']);

  $items = [];
  foreach ($keys as $key) {
    $items[$key] = $key;
  }

  $form->_table->items = $items;
  return $form;

}

function syncTable($table, $changes, $selected)
{ 
  $pk = $this->getPrimary($table);

  $ins = $upd = $del = 0;

  foreach ($changes['insert'] as $data)
  {
    if (!in_array($data[$pk], $selected)) continue;
    $this->db->insert($table, $data);
    $ins++;
  }

  foreach ($changes['update'] as $data)
  {
    if (!in_array($data[$pk], $selected)) continue;
    $this->db->delete($table, [$pk => $data[$pk]]);
    $this->db->insert($table, $data);
    $upd++;
  }

  foreach ($changes['delete'] as $data)
  {
    if (!in_array($data[$pk], $selected)) continue;
    $this->db->delete($table, [$pk => $data[$pk]]);
    $del++;
  }

  return ['ins' => $ins, 'upd' => $upd, 'del' => $del];
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