<?php

//vrati aktualni datum v mysql formatu.
function now() {
  return date("Y-m-d H:i:s");
}

//test zda db obsahuje pclib tabulky
function is_installed($db)
{
  global $app;
  
  try {
    $table = (get_class($db->drv) == 'pgsql')? 'auth_users':'AUTH_USERS';
    return $db->columns($table)? true:false;
  } catch(Exception $e) {
    $app->error('Nepodařilo se připojit k databázi. Chyba: %s',null, $e->getMessage());
  }
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

function array_assoc(array $a, $key)
{
  $b = [];
  foreach ($a as $value) {
    $b[$value[$key]] = $value;
  }
  return $b;
}

function array_record(array $a, $key)
{
  $b = [];
  foreach ($a as $row) {
    $b[] = [$key => $row];
  }
  return $b;
}


?>