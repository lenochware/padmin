<?php

//vrati aktualni datum v mysql formatu.
function now() {
  return date("Y-m-d H:i:s");
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