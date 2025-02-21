<?php
/**
 * \file
 * Aplikace padmin.
 * \author -dk- <lenochware@gmail.com>
 */
include 'vendor/autoload.php';
include 'libs/func.php';

safe_session_start();

$app = new PCApp('padmin');
$app->addConfig('./config.php');
$pclib->autoloader->addDirectory('libs');

$app->layout->_VERSION = '2.5.1';

if (($app->db->info['driver'] ?? '') == 'pgsql') {
  $app->db->drv->ucase = 1;
  $app->db->drv->noquote = 1;
}

if (!is_installed($app->db)) make_install($app);

if ($_POST) {
  $app->log('action', 'padmin/' . $app->router->action->path, null,  isset($_GET['id'])? (int)$_GET['id'] : null);
}

if ($app->auth->isLogged())
{
  $app->layout->enable('user');
  $user = $app->auth->getUser()->getValues();
  $app->layout->_UNAME = $user['FULLNAME'];

  if ($app->controller != 'account' and !$app->auth->hasright('padmin/enter')) {
    $app->error('Nemáte oprávnění ke vstupu.');
  }

  $menu = new PCTree();
  
  if (PCLIB_VERSION > '2.9.5') {
    $menu->auth = $app->auth;
  }

  $menu->load(/*PADMIN_MENU_ID*/ 1);

  $menu->values['CSS_CLASS'] = 'menu';
  $app->layout->_MENU = $menu;
  $app->run();
}
elseif(is_public($app->routestr)) {
  $app->run();
}
else {
  $app->run('account/signin');
}

$app->out();

?>