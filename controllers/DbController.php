<?php
include 'BaseController.php';

use pclib\extensions\TemplateFactory;

class DbController extends BaseController
{
  protected $table = 'addresses';
  protected $clipboard = [];

function init()
{
  parent::init();
  $this->clipboard = $this->app->getSession('db.clipboard');
  $this->table = $this->app->getSession('db.table');
}

function indexAction()
{
  $this->title(1, 'Tabulky');

  $tables = $this->getTables();
  $grid = new PCGrid('tpl/db/tables.tpl');
  $grid->setArray(array_record($tables, 'table'));

  return $grid;
}

function tableAction()
{
  if ($_GET['table']) $this->setTable($_GET['table']);

  if (!$this->table) $this->app->error("Vyberte tabulku.");

  $this->title(2, $this->table);


  $pk = $this->getPrimary($this->table);
  $columns = $this->db->columns($this->table);
  $grid = TemplateFactory::create('tpl/db/grid.tpl', $columns);
  $grid->setQuery("select *, $pk as __primary from $this->table");
  $grid->_htitle = 'TABLE ' . $this->table;
  $this->setPager($grid);

  $form =  new PCForm('tpl/db/form.tpl');
  $form->values['pk'] = $pk;
  $grid->_copy_form = $form;
  return $grid;
}

/* @ajax */
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

function pasteCsvAction()
{
  $csv = new CsvFile;
  $csv->fromString($_POST['data']['csv-data']);
  $this->app->setSession('db.clipboard', $csv->toArray());
  $this->redirect('db/preview');
}

function previewAction()
{
  $columns = $this->db->columns($this->table);
  $rows = $this->getInsertDeleteRows();

  $grid = TemplateFactory::create('tpl/db/grid.tpl', $columns);
  $grid->setArray($rows);
  $grid->_htitle = 'Preview';
  $grid->enable('b_update');
  return $grid;
}

function updateAction()
{
  $tables = $this->getTables();
  if (!in_array($table, $tables)) {
    $this->app->error("Tabulka nenalezena.");
  }

  $rows = $this->getInsertDeleteRows();
  $pk = $this->getPrimary($this->table);

  foreach ($rows as $row) {
    $status = $row['__status'];
    unset($row['__status']);

    if ($status == 'del') $this->db->delete($this->table, [$pk => $row[$pk]]);
    if ($status == 'ins') $this->db->insert($this->table, $row);
  }

  $this->app->setSession('db.clipboard', []);
  $this->app->message('Tabulka byla aktualizována.');
  $this->redirect('db/table');

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

protected function getInsertDeleteRows()
{
  if (!$this->clipboard) $this->app->error("Nejsou vybrané žádné řádky.");

  $rows = [];
  $pk = $this->getPrimary($this->table);

  foreach ($this->clipboard as $row) {
    $previous = $this->db->select($this->table, [$pk => $row[$pk] ?? '']);
    if ($previous and $previous != $row) {
      $previous['__status'] = 'del';
      $rows[] = $previous;      
    }

    if ($previous and $previous == $row) continue;

    $row['__status'] = 'ins';
    $rows[] = $row;
  }

  return $rows;
}

protected function setTable($table)
{
  $tables = $this->getTables();
  if (!in_array($table, $tables)) {
    $this->app->error("Tabulka nenalezena.");
  }

  $this->table = $table;
  $this->app->setSession('db.table', $table);
}

protected function getTables()
{
  return $this->app->config['dbsync'] ?? [];
}

protected function setPager($grid)
{
  $pagerForm = new PCForm("tpl/db/pagerform.tpl", "db-pager");

  $grid->pager->setPageLen(array_get($pagerForm->values, 'pglen'));
  $page = array_get($pagerForm->values, 'page');
  $grid->pager->setPage(array_get($_GET, 'page', $page));

  $pagerForm->_page = $grid->pager->getValue('page');
  $pagerForm->_total = $grid->pager->getValue('total');
  $pagerForm->_first = $grid->pager->getHtml('first');
  $pagerForm->_last = $grid->pager->getHtml('last');
  $pagerForm->_pglen = $grid->pager->getValue('pglen');
  $pagerForm->_pages = $grid->pager->getHtml('pages');

  $grid->values['pager'] = $pagerForm;
}


}

?>