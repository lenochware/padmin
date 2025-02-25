<?php
include 'BaseController.php';

use pclib\extensions\AuthManager;
use pclib\extensions\AuthConsole;

class InstallController extends BaseController {

function createdbAction()
{
  if ($this->app->isPclibDbInstalled()) {
    $this->app->error('Instalace už byla dokončena!');
  }

  $this->installPclib();
}

function installPclib()
{
  $this->db->codepage('utf8');
  $this->createTables();
  $this->addMenu();
  $this->addAccounts();  
}

function serviceJobsAction()
{
  $n = $this->db->runDump('_install/service_jobs.sql');
  $this->app->message("Ok, vykonáno $n dotazů.");
}

protected function createTables()
{
  $dname = str_replace('pdo_', '', $this->db->drv->extension);
  $dumpfile = '_install/pclib_'.$dname.'.sql';

  $n = $this->db->runDump($dumpfile);

  $this->app->message("Ok, vykonáno $n dotazů.");
}

protected function addMenu()
{
  $menu = new PCTree();
  
  $text = file_get_contents('_install/menu.txt');
  $menu->importText($text);

  $menu->save(/*PADMIN_MENU_ID*/ 1);

  $li = ['ID' => 1, 'APP' => 'padmin', 'CNAME' => 'tree', 'LABEL' => 'pAdmin menu'];
  $this->db->insert('LOOKUPS', $li);

  $this->app->message("Menu aplikace vytvořeno.");
}

protected function addAccounts()
{
  $authCon = new AuthConsole(new AuthManager);

  $authCon->executefile('_install/auth.txt');
  if ($authCon->errors) $this->app->error(implode('<br>', $authCon->errors));

  $this->app->message("Konfigurace uživatelských účtů dokončena.");
}

}

?>