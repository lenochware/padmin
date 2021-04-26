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
  $grid = new PCGrid('tpl/log.tpl');
  $grid->setarray($this->logger->getlog($this->MAXROWS,
    $this->app->getsession('logfilter'))
  );

  $grid->values['size_mb'] = $this->logger->getSize();

  $search = new PCForm('tpl/logsearch.tpl', 'logsearch');
  return $search.$grid;
}

function cleanupAction() {
  return new PCForm ('tpl/logform.tpl');
}

function deleteAction($period) {
  if (!$period) $this->app->error('Nezadaná perioda.');
  $start = date("Y-m-d H:i:s", strtotime("now - ".($period+0)." months"));
  $this->db->delete('LOGGER',"DT<'{0}'", $start);
  $this->db->delete('LOGGER_MESSAGES',"DT<'{0}'", $start);
  $this->app->message('Záznamy byly smazány.');
  $this->reload();
}

function searchAction() {
  $this->enablefilter();
  $this->reload();
}

function showallAction() {
  $this->enablefilter(false);
  $this->reload();
}

function enablefilter($enable = true) {
  $filter = $enable? $_POST['data'] : null;
  $this->app->setsession('logfilter', $filter);
  $this->app->setsession('logsearch.values', $filter);
}

}

?>
