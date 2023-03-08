<?php
/**
 * \file
 * Aplikace padmin.
 * \author -dk- <lenochware@gmail.com>
 */
define('PADMIN_MENU_ID', 1);

include 'vendor/autoload.php';
include 'libs/func.php';

safe_session_start();
//error_reporting(E_ALL);

$app = new PCApp('padmin');
$app->addConfig('./config.php');
$pclib->autoloader->addDirectory('libs');

$app->setLayout('tpl/website.tpl');

try {
  $app->db = new PCDb($app->config['padmin.db']);
} catch (Exception $e) {
  $app->error('Nepodařilo se připojit k databázi. Chyba: %s',null, $e->getMessage());
}

if ($app->db->info['driver'] == 'pgsql') {
  $app->db->drv->ucase = 1;
  $app->db->drv->noquote = 1;
}

if (!is_installed($app->db))
{
  if ($app->routestr == 'install/createdb') {
    $app->run();
    $app->redirect('users');
  }
  else {
    $app->error('Tabulky PCLIB v databázi neexistují!<br><a href="?r=install/createdb">Nainstalovat PClib datastruktury</a>');
  }
}

$app->auth = new PCAuth();

if (!$app->controller) {
  $app->controller = 'users';
}

if ($app->config['padmin.logging']) {
  $app->logger = new PCLogger();
}

if ($_POST) {
  $a = $app->router->action;  //$app->routestr not work for posts - missing "/method"
  $app->log('post', 'padmin/' . $a->controller .'/'. $a->method, null,  array_get($_GET, 'id'));
}

if ($app->auth->isLogged()) {

  $app->layout->enable('user');
  $user = $app->auth->getUser()->getValues();
  $app->layout->_UNAME = $user['FULLNAME'];

  if ($app->controller != 'account' and !$app->auth->hasright('padmin/enter')) {
    $app->error('Nemáte oprávnění ke vstupu.');
  }

  $authConf = $app->config['pclib.auth'];
  if ($authConf['algo'] == 'md5' and $authConf['secret'] == 'write any random string!') {
    $app->message("Nastavte konfigurační parametr 'pclib.auth.secret', nebo použijte kryptograficky bezpečný algoritmus 'bcrypt'.", 'warning');
  }

  
  $menu = new PCTree();
  $menu->load(PADMIN_MENU_ID);
  $menu->values['CSS_CLASS'] = 'menu';
  $app->layout->_MENU = $menu;
  $app->run();
}
elseif(allow_public_access($app->routestr)) {
  $app->run();
}
else {
  if($app->controller == 'account') $app->run();
  else $app->run('account/signin');
}

$app->layout->_APPNAME = $app->config['padmin.name'];
$app->layout->_VERSION = $app->config['padmin.version'];
$app->out();

?>