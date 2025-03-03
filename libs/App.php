<?php 

class App extends pclib\App {

	function initDatabase()
	{
		if (!$this->db->info) {
			$this->error('Není nastavené připojení k databázi.');
		}

		if (($this->db->info['driver'] ?? '') == 'pgsql') {
		  $this->db->drv->ucase = 1;
		  $this->db->drv->noquote = 1;
		}

		if (!$this->isPclibDbInstalled()) {
			if ($this->routestr == 'install/createdb') {
			  $this->run('install/createdb');
    		$this->redirect('users');
			}

			$this->error(
	      'Tabulky PCLIB v databázi neexistují!<br>'
	      .'<a href="?r=install/createdb">Nainstalovat PClib</a>'
    	);
		};
	}

	function addConfig($path)
	{
		try {
			parent::addConfig($path);
			
		} catch (Exception $e) {
			$this->error("Nepodařilo se inicializovat aplikaci. Chyba: " . $e->getMessage());
			
		}

	}

	function createMenu($user)
	{
	  if (in_array($this->routestr, $this->config['menu']['protected']) and !$user['superuser']) {
	    $this->error('Nemáte oprávnění ke vstupu.');
	  }

		$menu = new PCTree();
  
	  if (PCLIB_VERSION > '2.9.5') {
	    $menu->auth = $this->auth;
	  }

	  $menu->load(/*PADMIN_MENU_ID*/ 1);

	  $menu->map(function($node) use ($user) {
	    if (in_array($node['ROUTE'], $this->config['menu']['hidden'])) {
	      $node['ACTIVE'] = 0;
	    }

	    if (!$user['superuser'] and in_array($node['ROUTE'], $this->config['menu']['protected'])) {
	      $node['ACTIVE'] = 0;
	    }

	    return $node;

	  });

	  $menu->values['CSS_CLASS'] = 'menu';
	  $this->layout->_MENU = $menu;		
	}

	function isPclibDbInstalled()
	{
	  try {
	    $table = (get_class($this->db->drv) == 'pgsql')? 'auth_users':'AUTH_USERS';
	    $this->db->select($table);
	  } catch(Exception $e) {
	    return false;
	  }

	  return true;
	}

}

 ?>