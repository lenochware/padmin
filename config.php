<?php

$config = [
	'padmin.version' => '2.4.0',
	'padmin.logging' => true,

	'pclib.security' => [
		'tpl-escape' => true, 
		'csrf' => true, 
		'form-prevent-mass' => false
	],

	'pclib.auth' => [
		'algo' => 'md5', 
		'secret' => 'write any random string!', // <-- Set auth secret string (at least 20 random characters)
		'realm' => ''
	],

	'padmin.db' => '',    // <-- Set connection to database. Example: 'pdo_mysql://user:password@localhost/my_database'
	'jobs-dir' => 'jobs', // <-- path to directory with your cron-jobs
	'upload-dir' => '../uploaded', // <-- path to directory with uploaded files
	'api-key' => '',      // <-- Set api-key for external applications access to padmin api (now used for jobs/run)

	'dbsync' => [],
];

$production = [
	'pclib.errors' => ['develop' => false, 'log' => true],
];

?>