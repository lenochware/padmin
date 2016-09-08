<?php
include 'BaseController.php';
include PCLIB_DIR.'extensions/AuthManager.php';

class ConsoleController extends BaseController {

private $form;

function init() {
  $this->form = new PCForm ('tpl/console.tpl');
  if ($_SESSION['termbuf']) {
    $this->form->_TERM = implode("\n", $_SESSION['termbuf']);
    $this->form->_CMDHIST = $this->gethistory();
  }
  else
    $this->form->_TERM = '= aterm v0.3 =<br>Type `help` for list of commands.';
}

function indexAction() {
  $this->title(1, 'Autentizační konzole');
  return $this->form;
}

function submitAction() {
  $authMng = new AuthManager;
  $termbuf = $_SESSION['termbuf'];
  $cmdline = $this->form->values['CMDLINE'];
  if (get_magic_quotes_gpc()) $cmdline = stripslashes($cmdline);

  $termbuf[] = '';
  $termbuf[] = "<span class=\"console-cmd\">authc> $cmdline</span>";

  //$cmdline is console command line
  //$termbuf is console history
  //we execute console command with $authMng->execute($cmdline);
  switch ($cmdline) {
    case 'cls' : $termbuf = $welcome; break;
    case 'help': $termbuf[] = file_get_contents('tpl/aterm.hlp'); break;
    default: $authMng->execute($cmdline); break;
  }

  //print authMng messages and errors
  if ($authMng->messages) $termbuf = array_merge($termbuf, $authMng->messages);
  if ($authMng->errors)
    $termbuf = array_merge($termbuf,
      array_map(create_function(
        '$a','return "<span class=\"console-error\">$a</span>";'),$authMng->errors
      )
    );

  if (count($termbuf) > 30) $termbuf = array_slice($termbuf, -30);
  $_SESSION['termbuf'] = $termbuf;
  $this->redirect('console');
}

function gethistory() {
  $termbuf = $_SESSION['termbuf'];
  if (!$termbuf) return '';
  
  foreach($termbuf as $cmd) {
    $cmd = strip_tags($cmd);
    if (substr($cmd,0,6) == 'authc>')
      $cmds[] = addslashes(substr($cmd,7));
  }
  
  return implode('","', $cmds);
}

}

?>
