<?php 

class App extends pclib\App {

	function initDatabase()
	{
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
	    return $this->db->columns($table)? true:false;
	  } catch(Exception $e) {
	    $this->error('Nepodařilo se připojit k databázi. Chyba: %s',null, $e->getMessage());
	  }
	}

}

 ?>