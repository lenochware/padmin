<?php

class BaseController extends PCController {

protected $db;

function __construct($app) {
  parent::__construct($app);
  $this->db = $this->app->db;
}

function init() {
  $this->testPerm('padmin/'.$this->app->controller);
}

function allowed($perm) {
  if ($perm == 'padmin/install') return true;
  return $this->app->auth->hasright($perm);
}

function testPerm($perm) {
  if (!$this->allowed($perm)) {
    $this->app->error(
      "Nemáte oprávnění '%s'. Přístup zamítnut.", 
      null, $perm
    );  
  }
}

function title($level, $title) {
  $this->app->layout->bookmark($level, $title);
  $this->app->layout->_TITLE = $title;
}

function reload() {
  $this->app->redirect($this->app->controller);
}

function invalid($form) {
  $e = array();
  foreach($form->invalid as $id => $message) $e[] = $id.': '.$message;
  $this->app->error(implode('<br>',$e));
}

public function outputJson(array $data, $code = '')
{
  header('Content-Type: application/json; charset=utf-8');
  die(json_encode($data, JSON_UNESCAPED_UNICODE/*|JSON_PRETTY_PRINT*/));
}

function searchAction() {
  $this->setFilter($_POST['data']);
  $this->reload();
}

function showallAction() {
  $this->setFilter(null);
  $this->reload();
}

function setFilter($filter) {
  throw new Exception('Vyhledávání není nakonfigurováno.');
}

}

?>