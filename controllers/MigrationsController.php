<?php
include 'BaseController.php';

class MigrationsController extends BaseController {

const MIGRATIONS_DIR = 'data/migrations/';


function indexAction() {
  $this->title(1, "Migrace");
  $rows = array();
  foreach(glob(self::MIGRATIONS_DIR.'*.json') as $name) {
    $rows[]['name'] = extractPath($name, "%f");
  }
  
  $grid = new PCGrid('tpl/migrations.tpl');
  $grid->_TITLE = "Migrace";
  $grid->setarray($rows);
  return $grid;

}

function show_migrationAction($fromFile, $toFile) {
  $this->title(2, "Tabulky");
  $data1 = $this->loadMigration($fromFile);
  $data2 = $this->loadMigration($toFile);
  
  $rows = array();
  foreach($data1 as $k => $v) {
    if (!$data2[$k]) $rows[] = array('name' => $k, 'status' => 'removed');
  
  }
  foreach($data2 as $k => $v) {
    $row = array('name' => $k);
    if (!$data1[$k]) $row['status'] = 'added';
    elseif($this->isColumnsDiff($data1[$k]['columns'], $data2[$k]['columns'])) $row['status'] = 'modified';
    else $row['status'] = 'same';
    $rows[] = $row;
  }
  $grid = new PCGrid('tpl/db_tables_diff.tpl');
  $grid->_TITLE = "Migrace $fromFile -> $toFile";
  $grid->setarray($rows);
  return $grid;
}

function show_columnsAction($fromFile, $toFile, $table) {
  $this->title(3, "Sloupce");
  $data1 = $this->loadMigration($fromFile);
  $data2 = $this->loadMigration($toFile);
  $rows = $this->getColumnsDiff($data1[$table]['columns'], $data2[$table]['columns']);

  $grid = new PCGrid('tpl/db_tables_diff.tpl');
  $grid->_TITLE = "Tabulka $table";
  $grid->setarray($rows);
  return $grid;
}

function createAction() {
  $this->createMigration($this->db->dbname());
  $this->reload();
}

function getColumnsDiff($table1, $table2) {
  $rows = array();
  foreach((array)$table1 as $k => $v) {
    if (!$table2[$k]) $rows[] = array('name' => $k, 'status' => 'removed');

  }
  foreach((array)$table2 as $k => $v) {
    $row = array('name' => $k);
    if (!$table1[$k]) $row['status'] = 'added';
    elseif(array_diff_assoc($table1[$k], $table2[$k])) $row['status'] = 'modified';
    else $row['status'] = 'same';
    $rows[] = $row;
  }
  return $rows;
}

function isColumnsDiff($table1, $table2) {
  foreach($this->getColumnsDiff($table1, $table2) as $diff) {
    if ($diff['status'] != 'same') return true;
  }
  return false;
}


function getTables($dbName) {
  $tables = $this->db->select_all(
    "select * from information_schema.TABLES T
    where TABLE_SCHEMA='{0}'", $dbName
  );
  $ret = array();
  foreach($tables as $t) { $ret[$t['TABLE_NAME']] = $t; }
  return $ret;
}

function createMigration($dbName) {
  foreach($this->getTables($dbName) as $table) {
    $name = $table['TABLE_NAME'];
    $data[$name] = array(
      'name' => $name,
      'columns' => $this->db->columns($dbName.'.'.$name)
    );
  }
  
  $this->saveJson($dbName.date("_Y_m_d").'.json', $data);
}

function loadMigration($name) {
  return json_decode(file_get_contents(self::MIGRATIONS_DIR.$name.'.json'), true);
}

function saveJson($fileName, $data) {
  $n = 1;
  $saveName = $fileName;
  while (file_exists(self::MIGRATIONS_DIR.$saveName)) {
    $saveName = extractPath($fileName, "%f_$n.%e");
    $n++;
  }
  
  if (version_compare(phpversion(), "5.4.0", "<")) {
    $s = json_encode($data);
  }
  else {
   $s = json_encode($data, JSON_PRETTY_PRINT);
  }
  
  file_put_contents(self::MIGRATIONS_DIR.$saveName, $s);
}

}

?>