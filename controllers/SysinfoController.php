<?php
include 'BaseController.php';

class SysinfoController extends BaseController {

function phpAction()
{
  $pi = $this->getPhpInfo();

  unset($pi['PHP Variables']);

  foreach ($pi as $secName => $secData) {
    $output["<b>$secName</b>"] = "";

    foreach ($secData as $key => $value) {
      $output["<span class=\"hidden\">$secName</span> " . $key] = $value;
    }
  }

  //dump($pi);

  return $this->getTable('PHP '. phpversion(), $output);


}

function phpExportAction()
{
  print 'padmin PHP configuration dump, HOST: '.$_SERVER['HTTP_HOST'].', '.date("Y-m-d H:i:s").'<br>';
  print 'PHP version: '.phpversion();
  print '<br><br>';
  foreach (ini_get_all(null, false) as $key => $value) {
    if (is_null($value)) $value = 'null';
    if (is_bool($value)) $value = $value? 'true' : 'false';
    print $key.': '.$value.'<br>';
  }
  die();
}

function dbAction()
{
  switch($this->db->drv->extension) {
  case 'mysql':
  case 'pdo_mysql':
    $output = $this->db->select_pair(
    'select VARIABLE_NAME,VARIABLE_VALUE from information_schema.GLOBAL_VARIABLES
    order by VARIABLE_NAME'
    );
    break;
  case 'pgsql':
    $output = array();
    $q = $this->db->query('show all');
    while($row = $this->db->fetch($q, 'r')) $output[$row[0]] = $row[1];
    break;

  case 'pdo_sqlite':
    $output = array();
    break;
  default: $this->app->error('Neznámá databáze.');
  }
  $title = 'Databáze '.$this->db->drv->extension.' '.$this->db->drv->version();
  return $this->getTable($title, $output);
}

function webserverAction()
{
  $title = $_SERVER['SERVER_SOFTWARE'];
  if (function_exists('apache_get_modules')) {
    $details = apache_get_modules();
  }
  else {
    $details = array('' => 'No details');
  }

  return $this->getTable($title, $details);
}

function pclibAction()
{
  $title = 'PClib '.PCLIB_VERSION;
  return $this->getTable($title, $this->getConfig());

}

function getPhpInfo()
{
    ob_start();
    phpinfo();
    $info_arr = array();
    $info_lines = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));
    $cat = "General";
    foreach($info_lines as $line)
    {
        // new cat?
        preg_match("~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;
        if(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val))
        {
            $info_arr[$cat][trim($val[1])] = $val[2];
        }
        elseif(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val))
        {
            //$info_arr[$cat][trim($val[1])] = array("local" => $val[2], "master" => $val[3]);
            $info_arr[$cat][trim($val[1])] = $val[3];
        }
    }
    return $info_arr;
}

function getConfig()
{
  $config = $this->app->config;
  $config['pclib.auth']['secret'] = '-- hidden --';
  
  $output = array();
  foreach($config as $i => $v) {
    if (strpos($i, 'pclib.') === false) continue;  
    $output[$i] = json_encode($v, JSON_UNESCAPED_SLASHES);
  }

  return $output;
}

function getTable($title, $data)
{
  $table = new PCTpl('tpl/output.tpl');
  $output = array();
  foreach($data as $k => $v) $output[] = array('KEY' => $k, 'VALUE' => $v);
  $table->_items = $output;
  $table->_TITLE = $title;
  return $table;
}

}

?>
