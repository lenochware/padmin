<?php 

/**
  * Deaktivuje účty neaktivní více než jeden rok.
  */
 class JobDisableInactiveUsers extends Job
 {
 	
 	function run()
 	{
 		$INTERVAL = '1 YEAR';

 		$users = $this->app->db->selectOne(
 			"select ID from AUTH_USERS where ACTIVE=1 and (LAST_LOGIN < NOW() - INTERVAL $INTERVAL or (LAST_LOGIN is null and DT < NOW() - INTERVAL $INTERVAL))"
 		);

 		$data = [
 			'ACTIVE' => 0,
 			'ANNOT' => 'disabled-inactive-user',
 		];

 		$this->app->db->update('AUTH_USERS', $data, ['ID' => $users]);

 		return "Deaktivováno ".count($users)." uživatelů.";
 	}
 }

 ?>