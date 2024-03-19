<?php 

/**
 * Vymaže plain-text heslo z databáze u všech uživatelů (pokud se používá, převede ho na password-hash).
 */
class JobFixDefaultPassword extends Job
{
 	
 	function run()
 	{
 		$am = new \pclib\extensions\AuthManager;
 		$users = $this->app->db->selectAll('AUTH_USERS');
 		$i = 0;

 		foreach ($users as $user) {
 			if ($user['PASSW'] == '') {
 				$am->setPassw($user['USERNAME'], $user['DPASSW']);
 				$i++;
 			}

 			if ($user['DPASSW']) {
 				$this->app->db->update('AUTH_USERS', ['DPASSW' => ''], ['ID' => $user['ID']]);
 			}
 		}

 		return "Opraveno $i uživatelů.";
 	}

}

 ?>