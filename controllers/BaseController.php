<?php

class BaseController extends PCController {

protected $db;

function __construct($app) {
  parent::__construct($app);
  $this->authorizeRedirect = 'account/signin';
  $this->db = $this->app->db;
}

function init() {
  $this->authorize('padmin/'.$this->app->controller);
}

function allowed($perm) {
  if ($perm == 'padmin/install') return true;
  return $this->app->auth->hasright($perm);
}

function title($level, $title) {
  $this->app->layout->bookmark($level, $title);
  $this->app->layout->_TITLE = $title;
}

function reload() {
  $this->app->redirect($this->app->controller);
}

/**
 * Redirect na predchozi url (backurl) nebo fallback url, kdyz neexistuje
 */
function redirectBack($fallback)
{
  $backUrl = $this->app->getSession('backurl');
  if ($backUrl) {
    $this->app->deleteSession('backurl');
    $this->app->redirect(['url' => $backUrl]);
  }
  else {
    $this->app->redirect($fallback);
  }
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

}

?>