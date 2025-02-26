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

function addAction()
{
  $this->title(2, "Nová šablona");

  $form = new PCForm('tpl/content/form.tpl');
  $form->values['TITLE'] = 'Nová šablona';
  $form->setAttr('NAME', 'noedit', 0);
  $form->enable('insert');
  return $form;
}

public function insertAction()
{
  global $user;

  $form = new PCForm('tpl/content/form.tpl');

  if (!$form->validate()) {
    $this->invalid($form);
  }

  $form->_CREATED_AT = now();
  $form->_AUTHOR_ID = $user['ID'];
  $id = $form->insert('PCLIB_CONTENT');
  $this->app->message('Položka byla přidána.');
  $this->redirect('content/edit/id:'.$id);
}

function editAction($id)
{
  $content = $this->db->select('PCLIB_CONTENT', ['ID' => $id]);
  if (!$content) $this->app->error('Položka nenalezena.');

  $this->title(2, $content['TITLE']);

  $form = new PCForm('tpl/content/form.tpl');
  $form->values = $content;
  $form->enable('update', 'delete');
  return $form;
}

function updateAction($id)
{
  $form = new PCForm('tpl/content/form.tpl');
  $form->update('PCLIB_CONTENT', ['ID' => $id]);
  $this->app->message('Položka byla uložena.');
  $this->redirect("/self");  
}

function deleteAction($id)
{
  $this->authorize('padmin/content/delete');
  $form = new PCForm('tpl/content/form.tpl');
  $form->delete('PCLIB_CONTENT', ['ID' => $id]);
  $this->app->message('Položka byla smazána.');
  $this->reload();  
}

}

?>