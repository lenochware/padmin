<?php
include 'BaseController.php';

class ParamsController extends BaseController {

function indexAction()
{
  $this->title(1, 'Parametry aplikace');

  $grid = new PCGrid('tpl/params/list.tpl', 'params');
  $grid->setQuery(
    "select * from APP_PARAMS where 1=1
    ~ and PARAM_NAME like '%{PARAM_NAME}%'");

  //$search = new SearchForm($grid, ['PARAM_NAME']);

  return $grid;
}

function editAction($id)
{
  $param = $this->db->select('APP_PARAMS', ['ID' => $id]);
  if (!$param) $this->app->error('Parametr nenalezen.');

  $this->title(2, $param['TITLE']);

  $form = new PCForm('tpl/params/form.tpl');
  $form->values = $param;
  $form->enable('update');
  return $form;
}

function updateAction($id)
{
  $form = new PCForm('tpl/params/form.tpl');
  $form->update('APP_PARAMS', ['ID' => $id]);
  $this->app->message('Položka byla uložena.');
  $this->redirect("/self");  
}

}

?>