<?php 

/**
 * Převede hesla hashovaná pomocí md5 na bcrypt-md5. 
 * Před spuštěním zálohujte tabulku uživatelů. Po migraci přepněte v konfiguraci pclib.auth algo na 'bcrypt-md5'.
 */
class JobMigrateHash extends Job
{
	
	function run()
	{
		$am = new \pclib\extensions\AuthManager;

		$am->passwordAlgo = 'bcrypt';

		$n = 0; $i = 0;
		foreach ($this->app->db->selectAll('AUTH_USERS') as $user)
		{
			if (!$user['PASSW']) continue;
			if (!$this->isValidMd5($user['PASSW'])) {
				$i++;
				continue;
			}

			$this->app->db->update('AUTH_USERS', ['PASSW' => $am->passwordHash($user['PASSW'])], pri($user['ID']));
			$n++;
		}

		return "Opraveno $n uživatelů, $i nemá md5 hash.";
	}

	private function isValidMd5($md5 ='')
	{
			return preg_match('/^[a-f0-9]{32}$/', $md5);
	}

}

 ?>