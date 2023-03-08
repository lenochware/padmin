<?php

//vrati aktualni datum v mysql formatu.
function now() {
  return date("Y-m-d H:i:s");
}

//test zda db obsahuje pclib tabulky
function is_installed($db) {
  $table = (get_class($db->drv) == 'pgsql')? 'auth_users':'AUTH_USERS';
  return $db->columns($table)? true:false;
}

function make_install($app)  
{
  if ($app->routestr == 'install/createdb') {
    $app->run();
    $app->redirect('users');
  }
  else {
    $app->error(
      'Tabulky PCLIB v databázi neexistují!<br>'
      .'<a href="?r=install/createdb">Nainstalovat PClib</a>'
    );
  }
}

//akce pristupne bez prihlaseni
function is_public($route)
{
	return in_array($route, ['jobs/run', 'account/login', 'account']);
}

?>
