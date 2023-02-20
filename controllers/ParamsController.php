<?php
include 'BaseController.php';

class ParamsController extends BaseController {

protected function getForm()
{
  $form = parent::getForm();
  if ($_GET['editor']) $form->enable('editor');
  return $form;
}

function indexAction()
{
  $this->title(1, 'Parametry aplikace');

  $grid = new PCGrid('tpl/params/list.tpl', 'params');
  $grid->setQuery(
    "select * from APP_PARAMS where 1=1
    ~ and PARAM_NAME like '{PARAM_NAME}%'");

  $search = new SearchForm($grid, ['PARAM_NAME']);

  return $search.$grid;
}

function setFilter($filter) {
  $this->app->setsession('params.filter', $filter);
  $this->app->setsession('search-params.values', $filter);
}

}

?>