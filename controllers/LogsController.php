<?php
include 'BaseController.php';

class LogsController extends BaseController {

private $logger;
private $MAXROWS = 500;

function init() {
  parent::init();
  $this->logger = new PCLogger;
}

function indexAction()
{
  if (isset($_GET['user'])) {
    $filter = ['USERNAME' => $_GET['user']];
    if (isset($_GET['action'])) $filter['ACTIONNAME'] = $_GET['action'];

    $this->setFilter($filter);
  }

  $this->title(1, 'Záznamy logu');
  $grid = new PCGrid('tpl/logs/list.tpl');
  $grid->setarray($this->logger->getlog($this->MAXROWS,
    $this->app->getSession('logfilter'))
  );

  $grid->values['size_mb'] = $this->logger->getSize();

  $search = new PCForm('tpl/logs/search.tpl', 'logsearch');
  return $search.$grid;
}

function cleanupAction() {
  $this->title(2, 'Vyčistit');
  return new PCForm ('tpl/logs/form.tpl');
}

function deleteAction($period) {
  if (!$period) $this->app->error('Nezadaná perioda.');
  
  set_time_limit(0);
  $this->logger->deleteLog($period, true);
  $this->app->message('Záznamy byly smazány.');
  $this->reload();
}

function searchAction() {
  $this->setFilter($_POST['data']);
  $this->app->router->reload();
}

function showallAction() {
  $this->setFilter(null);
  $this->app->router->reload();
}

function setFilter($filter) {
  $this->app->setsession('logfilter', $filter);
  $this->app->setsession('logsearch.values', $filter);
}

}

?>
