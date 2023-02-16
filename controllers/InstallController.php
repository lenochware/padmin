<?php
include 'BaseController.php';

use pclib\extensions\AuthManager;
use pclib\extensions\AuthConsole;

class InstallController extends BaseController {

function indexAction()
{
  $this->installPclib();
}

function installPclib()
{
  if (is_installed($this->db)) {
    $this->app->error('Tabulky PCLIB už v databázi existují!');
  }

  $this->db->codepage('utf8');
  $this->createTables();
  $this->addMenu();
  $this->addAccounts();  
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

  $menu->save(PADMIN_MENU_ID);

  $li = ['ID' => PADMIN_MENU_ID, 'APP' => 'padmin', 'CNAME' => 'tree', 'LABEL' => 'pAdmin menu'];
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