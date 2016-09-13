<?php

$config = array(
'padmin.name' => 'padmin',
'padmin.version' => '1.0.0',
'padmin.debugmode' => false,
'padmin.logging' => false,

/* Setup connection to your database, for example 'pdo_mysql://user:password@localhost/my_database' */
'padmin.db' => '',

'pclib.auth' => array(
	'algo' => 'md5', 
	/* Auth secret should be some hard to guess random string. */
	'secret' => 'write any random string!',
	'realm' => ''
),
);

?>