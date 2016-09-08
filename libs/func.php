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

?>
