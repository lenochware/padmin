<?php
include 'BaseController.php';

class UserParamsController extends BaseController {

function indexAction()
{
  return 'Parametry uživatele';
}

function editAction($id)
{
  $this->title(3, 'Parametry');
  $user = $this->db->select('AUTH_USERS', ['ID' => $id]);
  if (!$user) $this->app->error("Uživatel nenalezen.");

  if (!array_key_exists('JSON_PARAMS', $user)) $this->app->error("Nenalezen sloupec AUTH_USERS.JSON_PARAMS.");

  $params = $user['JSON_PARAMS']? json_decode($user['JSON_PARAMS'], true) : [];
  $form = $this->template('tpl/user_params/form.tpl', $params);
  return $this->template('tpl/user_params/edit.tpl', ['USERNAME' => $user['USERNAME'], 'form' => $form]);
}

function updateAction($id)
{
  $post = json_encode($_POST['data'] ?? []);
  $this->db->update('AUTH_USERS', ['JSON_PARAMS' => $post], ['ID' => $id]);
  $this->app->message('Položka byla uložena.');
  $this->redirect("users/edit/id:" . $id);  
}

}

?>