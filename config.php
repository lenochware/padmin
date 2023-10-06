<?php

$config = array(
'padmin.version' => '2.2.2',
'padmin.logging' => false,

'pclib.security' => array(
	'tpl-escape' => true, 
	'csrf' => true, 
	'form-prevent-mass' => false
),

'pclib.auth' => array(
	'algo' => 'md5', 
	'secret' => 'write any random string!', // <-- Set auth secret string (at least 20 random characters)
	'realm' => ''
),

'padmin.db' => '',    // <-- Set connection to database. Example: 'pdo_mysql://user:password@localhost/my_database'
'jobs-dir' => 'jobs', // <-- path to directory with your cron-jobs
'api-key' => '',      // <-- Set api-key for external applications access to padmin api (now used for jobs/run)

'dbsync' => [	
],

);

$production = array(
	'pclib.errors' => array('log'),
);

?>