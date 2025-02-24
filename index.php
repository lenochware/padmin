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

if ($app->auth->hasright('padmin/enter'))
{
  $app->layout->enable('user');
  $user = $app->auth->getUser()->getValues();
  $user['superuser'] = in_array($user['USERNAME'], $app->config['superuser']);
  $app->layout->_UNAME = $user['FULLNAME'];

  if (in_array($app->routestr, $app->config['protected']) and !$user['superuser']) {
    $app->error('Nemáte oprávnění ke vstupu.');
  }

  $menu = new PCTree();
  
  if (PCLIB_VERSION > '2.9.5') {
    $menu->auth = $app->auth;
  }

  $menu->load(/*PADMIN_MENU_ID*/ 1);

  $menu->map(function($node) use ($app, $user) {
    if (in_array($node['ROUTE'], $app->config['hidden'])) {
      $node['ACTIVE'] = 0;
    }

    if (!$user['superuser'] and in_array($node['ROUTE'], $app->config['protected'])) {
      $node['ACTIVE'] = 0;
    }

    return $node;

  });

  $menu->values['CSS_CLASS'] = 'menu';
  $app->layout->_MENU = $menu;
}

$app->run();
$app->out();

?>