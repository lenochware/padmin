<?php

$config = [
	'pclib.security' => [
		'tpl-escape' => true, 
		'csrf' => true, 
		'form-prevent-mass' => false
	],

	'pclib.app' => [
		'language' => 'cs',
		'default-route' => 'users',
		'layout' => 'tpl/website.tpl',
		'autostart' => ['db', 'auth', 'logger'],
	],

	'service.db' => [
		//'dsn' => 'pdo_mysql://user:password@localhost/my_database' // <-- Set connection to database.
	],


	'service.auth' => [
		'algo' => 'md5', 
		'secret' => 'write any random string!', // <-- Set auth secret string (at least 20 random characters)
		'realm' => ''
	],

	'dbsync' => [],
	'jobs-dir' => 'jobs',
	'upload-dir' => '../uploaded', // <-- path to directory with uploaded files
	'api-key' => '', // <-- Set api-key for external applications access to padmin api (now used for jobs/run)

];

$production = [
	'pclib.errors' => ['develop' => false, 'log' => true],
];