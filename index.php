<?php
/**
 * \file
 * Aplikace padmin.
 * \author -dk- <lenochware@gmail.com>
 */
define('PADMIN_MENU_ID', 1);

include 'libs/pclib/pclib.php';
include 'libs/func.php';

session_start();

$app = new app('padmin');
$app->addconfig('config.php');
$app->debugMode = $app->config['padmin.debugmode'];
$app->setlayout('tpl/website.tpl');
try {
  $app->db = new db($app->config['padmin.db']);
} catch (Exception $e) {
  $app->error(array('Nepodařilo se připojit k databázi.',$e->getMessage()));
}

if (get_class($app->db->drv) == 'pgsql') {
  $app->db->drv->ucase = 1;
  $app->db->drv->noquote = 1;
}

if (is_installed($app->db) and $app->config['padmin.lang']) {
  $app->language = $app->config['padmin.lang'];
}

if (!$app->config['pclib.auth.secret'])
  $app->error('V konfiguraci není nastavený klíč <b>pclib.auth.secret</b>.');

if (!is_installed($app->db)) {
  if ($app->controller == 'install') {
    $app->run('install');
    $app->redirect('users');
  }
  $app->error('Tabulky PCLIB v databázi neexistují! <b>Spusťte padmin/install</b>.');
}

$app->auth = new auth();

if (!$app->controller) $app->controller = 'users';

if ($app->config['padmin.logging']) {
  $app->logger = new logger();
  $app->log('get', $app->routestr, null, $_GET['id']);
}

if ($app->auth->islogged()) {
  $app->layout->enable('user');
  $user = $app->auth->getuser();
  $app->layout->_UNAME = $user['FULLNAME'];
  
  $menu = new tree('menu');
  $menu->gettree(PADMIN_MENU_ID);
  $app->layout->_MENU = $menu;
  $app->run();
}
else {
  if($app->controller == 'account') $app->run();
  else $app->run('account/signin');
}

$app->layout->_APPNAME = $app->config['padmin.name'];
$app->layout->_NAVIG = $app->layout->getnavig();
$app->layout->_VERSION = $app->config['padmin.version'];
$app->out();

?>