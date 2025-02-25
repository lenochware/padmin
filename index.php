<?php
/**
 * \file
 * Aplikace padmin.
 * \author -dk- <lenochware@gmail.com>
 */
include 'vendor/autoload.php';
include 'libs/func.php';

safe_session_start();
$pclib->autoloader->addDirectory('libs');

$app = new App('padmin');
$app->addConfig('./config.php');

$app->layout->_VERSION = '2.6.0';

$app->initDatabase();

if ($_POST) {
  $app->log('action', 'padmin/' . $app->router->action->path, null,  isset($_GET['id'])? (int)$_GET['id'] : null);
}

if ($app->auth->loggedUser)
{
  if (!$app->auth->hasright('padmin/enter')) {
    $app->message("Nemáte oprávnění ke vstupu.", 'warning');
    $app->auth->logout();
    $app->redirect("/");
  }

  $app->layout->enable('user');
  $user = $app->auth->getUser()->getValues();
  $user['superuser'] = in_array($user['USERNAME'], $app->config['superuser']);
  $app->layout->values['UNAME'] = $user['FULLNAME'] ?: $user['USERNAME'];

  $app->createMenu($user);
}

$app->run();
$app->out();

?>