<?php
include 'BaseController.php';

class ContentController extends BaseController {

function indexAction()
{
  $this->title(1, 'Šablony stránek');

  $grid = new PCGrid('tpl/content/list.tpl', 'content');
  $grid->setQuery("select * from PCLIB_CONTENT");

  return $grid;
}

function editAction($id)
{
  $content = $this->db->select('PCLIB_CONTENT', ['ID' => $id]);
  if (!$content) $this->app->error('Položka nenalezena.');

  $this->title(2, $content['TITLE']);

  $form = new PCForm('tpl/content/form.tpl');
  $form->values = $content;
  $form->enable('update');
  return $form;
}

function updateAction($id)
{
  $form = new PCForm('tpl/content/form.tpl');
  $form->update('PCLIB_CONTENT', ['ID' => $id]);
  $this->app->message('Položka byla uložena.');
  $this->redirect("/self");  
}

}

?>