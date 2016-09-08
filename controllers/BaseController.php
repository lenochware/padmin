<?php

class BaseController extends App_Controller {

protected $db;

function __construct($app) {
  parent::__construct($app);
  $this->db = $this->app->db;
}

function init() {
  if (!$this->allowed('padmin/'.$this->app->controller))
    $this->app->error(
      "You have not permission 'padmin/%s'. Access denied.",
      $this->name
    );
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

function invalid($form) {
  $e = array();
  foreach($form->invalid as $id => $message) $e[] = $id.': '.$message;
  $this->app->error($e);
}

}

?>