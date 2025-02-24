<?php
include 'BaseController.php';

class AccountController extends BaseController
{
private $auth;

function init()
{
  $this->auth = $this->app->auth;
}

function signinAction()
{
  $this->title(1, "Přihlášení");
  return new PCForm('tpl/loginform.tpl');
}

function loginAction()
{
  $lf = new PCForm('tpl/loginform.tpl');

  if (!$lf->validate()) {
    $this->app->error("Chybně vyplněný formulář.");
  }

  $this->auth->login($lf->values['username'], $lf->values['password']);

  if ($this->auth->errors) {
    $this->app->message(implode('<br>', $this->auth->errors), 'warning');
    $this->redirect('account/signin');
  }

  $this->app->log('auth', 'padmin/login');
  $this->loginNotifications();
  $this->redirectBack('users');
}

function logoutAction()
{
  $this->app->log('auth', 'padmin/logout');
  $this->auth->logout();
  $this->app->message('Byl jste odhlášen.');
  $this->redirect('account/signin');
}

function loginNotifications()
{
  $cfg = $this->app->config['service.auth'] ?? $this->app->config['pclib.auth'];
  
  if ($cfg['algo'] == 'md5' and (!$cfg['secret'] or $cfg['secret'] == 'write any random string!')) {
    $this->app->message(
      "Nastavte konfigurační parametr 'pclib.auth.secret', nebo použijte kryptograficky bezpečný algoritmus 'bcrypt'.", 
      'warning'
    );
  }

  $user = $this->auth->loggedUser;
  if ($user->hasDefaultPassword()) {
    $this->app->message("Uživatel má implicitní heslo!", 'warning');
  }
}

}

?>