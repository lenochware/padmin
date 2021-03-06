<?php
include 'BaseController.php';

class AccountController extends BaseController {

private $auth;

function init() {
  $this->auth = $this->app->auth;
}

function signinAction() {
  $this->title(1, "Přihlášení");
  return new PCForm('tpl/loginform.tpl');

}

function loginAction() {
  $lf = new PCForm('tpl/loginform.tpl');
  $this->auth->login($lf->values['username'], $lf->values['password']);
  if ($this->auth->errors) {
    $this->app->message(implode('<br>', $this->auth->errors), 'warning');
    $this->redirect('account/signin');
  }
  $this->redirect('users');
}

function logoutAction() {
  $this->auth->logout();
  $this->app->message('Byl jste odhlášen.');
  $this->redirect('account/signin');
}

}

?>
