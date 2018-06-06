<?php

$config = array(
'padmin.name' => 'padmin',
'padmin.version' => '1.2.0',
'padmin.debugmode' => false,
'padmin.logging' => false,
'padmin.lang' => 'cs',

'pclib.security' => array(
	'tpl-escape' => false, 
	'csrf' => true, 
	'form-prevent-mass' => false
),

'pclib.auth' => array(
	'algo' => 'md5', 
	'secret' => 'write any random string!', // <-- Set auth secret string (at least 10 random characters)
	'realm' => ''
),

'padmin.db' => '', // <-- Set connection to database. Example: 'pdo_mysql://user:password@localhost/my_database'

'jobs-dir' => 'jobs', // <-- path to directory with your cron-jobs
'api-key' => '' // <-- Set api-key for external applications access to padmin api (now used for jobs/run)

);

$production = array(
	'pclib.errors' => array('display', 'log'),
);

?>