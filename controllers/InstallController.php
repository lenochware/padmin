<?php
include 'BaseController.php';
include PCLIB_DIR.'extensions/AuthManager.php';

class InstallController extends BaseController {

function indexAction() {
  $this->app->db->codepage('utf8');
  $this->install_tablesAction();
  $this->install_menuAction();
  $this->install_authAction();
}

function install_tablesAction() {
  if (is_installed($this->db))
    $this->app->error('Tabulky PCLIB už v databázi existují!');

  $dname = str_replace('pdo_', '', $this->db->drv->extension);
  $dumpfile = '_install/pclib_'.$dname.'.sql';

  $n = $this->db->runDump($dumpfile);

  $this->app->message("Ok, vykonáno $n dotazů.");
}

function install_menuAction() {
  if ($this->db->exists('TREE_LOOKUPS', "TREE_ID='{0}'", PADMIN_MENU_ID))
    $this->app->error("pAdmin menu již existuje.");

  $menu = new PCTree;
  $menu->load('_install/menu.txt');
  $menu->addtree(PADMIN_MENU_ID);

  $li = array('ID' => PADMIN_MENU_ID, 'APP' => 'padmin', 'CNAME' => 'tree', 'LABEL' => 'pAdmin menu');
  $this->db->insert('LOOKUPS', $li);

  $this->app->message("Menu aplikace vytvořeno.");
}

function install_authAction() {
  if ($this->db->exists('AUTH_USERS'))
    $this->app->error('Uživatelské účty už v databázi existují!');
  $authMng = new AuthManager;
  $authMng->executefile('_install/auth.txt');
  if ($authMng->errors) $this->app->error($authMng->errors);

  $this->app->message("Konfigurace uživatelských účtů dokončena.");
}

} //class

?>
