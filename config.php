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

	/* Nastavte databázové připojení */
	'service.db' => [
		//'dsn' => 'pdo_mysql://user:password@localhost/my_database' 
	],


	/* secret: Nastavte náhodný řetězec (nejméně 20 znaků) pro posílení zabezpečení */
	'service.auth' => [
		'algo' => 'md5', 
		'secret' => 'write any random string!',
		'realm' => ''
	],

	/* Cesta k adresáři s nahranými soubory */
	'service.fileStorage' => [
		'rootDir' => '../uploaded',
	],

	/* 
	 - api-key: Aplikační klíč pro přístup externích apikací k padmin-api (nyní pouze akce jobs/run)
	 */
	'dbsync' => [],
	'jobs-dir' => 'jobs',
	'api-key' => '',

	'superuser' => ['admin'],

	/* 
	 - protected: Položky, ke kterým má přístup pouze superuser (implicitně uživatel admin)
	 - hidden: Položky menu, které jsou skryté
	 */
	'menu' => [
			'protected' => [
				'jobs', 'console', 'sysinfo', 'db' ,'menu', 'logs/cleanup'
			],

			'hidden' => ['db'],
	],

];

$production = [
	'pclib.errors' => ['develop' => false, 'log' => true],
];