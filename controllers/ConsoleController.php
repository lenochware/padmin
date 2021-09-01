<?php
include 'BaseController.php';

use pclib\extensions\AuthManager;
use pclib\extensions\AuthConsole;

class ConsoleController extends BaseController {

private $form;

function init() {
  parent::init();
  $this->form = new PCForm ('tpl/system/console.tpl');
  if (isset($_SESSION['termbuf'])) {
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
  $authCon = new AuthConsole(new AuthManager);
  $termbuf = $_SESSION['termbuf'];
  $cmdline = $this->form->values['CMDLINE'];

  $termbuf[] = '';
  $termbuf[] = "<span class=\"console-cmd\">authc> $cmdline</span>";

  //$cmdline is console command line
  //$termbuf is console history
  //we execute console command with $authCon->execute($cmdline);
  switch ($cmdline) {
    case 'cls' : $termbuf = $welcome; break;
    case 'help': $termbuf[] = file_get_contents('tpl/system/aterm.hlp'); break;
    default: $authCon->execute($cmdline); break;
  }

  //print authCon messages and errors
  if ($authCon->messages) $termbuf = array_merge($termbuf, $authCon->messages);
  if ($authCon->errors)
    $termbuf = array_merge($termbuf,
      array_map(create_function(
        '$a','return "<span class=\"console-error\">$a</span>";'),$authCon->errors
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
