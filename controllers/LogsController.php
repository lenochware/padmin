<?php
include 'BaseController.php';

class LogsController extends BaseController {

private $logger;
private $MAXROWS = 500;

function init() {
  parent::init();
  $this->logger = new PCLogger;
}

function indexAction() {
  $this->title(1, 'Záznamy logu');
  $grid = new PCGrid('tpl/logs/list.tpl');
  $grid->setarray($this->logger->getlog($this->MAXROWS,
    $this->app->getsession('logfilter'))
  );

  $grid->values['size_mb'] = $this->logger->getSize();

  $search = new PCForm('tpl/logs/search.tpl', 'logsearch');
  return $search.$grid;
}

function cleanupAction() {
  return new PCForm ('tpl/logs/form.tpl');
}

function deleteAction($period) {
  if (!$period) $this->app->error('Nezadaná perioda.');
  
  set_time_limit(0);
  $this->logger->deleteLog($period, true);
  $this->app->message('Záznamy byly smazány.');
  $this->reload();
}

function setFilter($filter) {
  $this->app->setsession('logfilter', $filter);
  $this->app->setsession('logsearch.values', $filter);
}

}

?>
