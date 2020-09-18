<?php

$config = array(
'padmin.name' => 'padmin',
'padmin.version' => '1.3.0',
'padmin.debugmode' => false,
'padmin.logging' => false,
'padmin.lang' => 'cs',

'pclib.security' => array(
	'tpl-escape' => true, 
	'csrf' => true, 
	'form-prevent-mass' => false
),

'pclib.auth' => array(
	'algo' => 'md5', 
	'secret' => '5dOPz4g$pQ+',
	'realm' => 'kurzy'
),

'padmin.db' => 'pdo_mysql://root@localhost/kurzy/utf8',
'api-key' => 'X0kY1urj',
'jobs-dir' => 'jobs',
'stk-kurzy.zk.api-url' => 'https://www.zk-pk.cz/zakladni/common/portalConnector',
'stk-kurzy.pk.api-url' => 'https://www.zk-pk.cz/prohlubovaci/common/portalConnector',

);

$production = array(
	'pclib.errors' => array('display', 'develop', 'log'),
	'padmin.db' => 'pdo_mysql://dekrakurzy:ZO5kPu2bYK@localhost/dekrakurzy/utf8',
    'stk-kurzy.zk.api-url' => 'https://www.zk-pk.cz/zakladni/common/portalConnector',
    'stk-kurzy.pk.api-url' => 'https://www.zk-pk.cz/prohlubovaci/common/portalConnector',
);

?>