<?php

class ParamsController extends BaseController {

function init()
{
  parent::init();
  $this->TABLE = 'app_params';
  $this->templateName = 'params';
}

protected function getForm()
{
  $form = parent::getForm();
  if ($_GET['editor']) $form->enable('editor');
  return $form;
}

function indexAction()
{
  $this->title(1, 'Nastavení');

  $grid = new PCGrid('tpl/lookups/'.$this->templateName.'.tpl', $this->templateName);
  $grid->_TITLE = $this->title();
  $grid->setQuery(
    "select * from {0} where 1=1
    ~ and param_name like '{param_name}%'", $this->TABLE
  );

  $search = new SearchForm($grid, ['param_name']);

  return $search.$grid;
}

}

?>