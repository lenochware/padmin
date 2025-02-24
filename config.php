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

	/* Set connection to database */
	'service.db' => [
		//'dsn' => 'pdo_mysql://user:password@localhost/my_database' 
	],


	/* secret: Set auth secret string (at least 20 random characters) */
	'service.auth' => [
		'algo' => 'md5', 
		'secret' => 'write any random string!',
		'realm' => ''
	],

	/* 
	 - upload-dir:  path to directory with uploaded files 
	 - api-key: Set api-key for external applications access to padmin api (now used for jobs/run)
	 */
	'dbsync' => [],
	'jobs-dir' => 'jobs',
	'upload-dir' => '../uploaded',
	'api-key' => '',

	'superuser' => ['admin'],

	'protected' => [
		'jobs', 'console', 'sysinfo', 'db' ,'menu'
	],

	'hidden' => [],
];

$production = [
	'pclib.errors' => ['develop' => false, 'log' => true],
];