<?php
include 'BaseController.php';

class LookupsController extends BaseController {

function indexAction() {
  $this->title(1, 'Číselníky');
  $lookups = new PCGrid('tpl/lookups.tpl');
  $lookups->setquery('select distinct CNAME from LOOKUPS');
  return $lookups;
}

function viewAction($lookup) {
  $this->title(2, 'Číselník '. $lookup);
  $grid = new PCGrid('tpl/lookup.tpl');
  $grid->_CNAME = $lookup;
  $grid->setquery("select * from LOOKUPS where CNAME='{0}'", $lookup);
  return $grid;
}

function editAction($id) {
  $this->title(3, 'Editace');
  $form = new PCForm('tpl/lookupform.tpl');
  $form->values = $this->db->select('LOOKUPS',"GUID='{#0}'", $id);
  $form->enable('update', 'delete');
  return $form;
}

function addAction($lookup) {
  $this->title(3, 'Nový');
  $form = new PCForm('tpl/lookupform.tpl');
  $form->_CNAME = $lookup;
  $form->enable('insert');
  return $form;
}

function insertAction($lookup) {
  $form = new PCForm('tpl/lookupform.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');
  $form->_CNAME = $lookup;
  $form->insert('LOOKUPS');
  $this->app->message('Položka byla uložena.');
  $this->redirect("lookups/view/lookup:$lookup");
}

function deleteAction($id) {
  $form = new PCForm('tpl/lookupform.tpl');
  $form->delete('LOOKUPS', "GUID='{#0}'", $id);
  $this->app->message('Položka byla smazána.');
  $this->redirect("lookups/view/lookup:{GET.lookup}");
}

function updateAction($id) {
  $form = new PCForm('tpl/lookupform.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');
  $form->update('LOOKUPS', "GUID='{#0}'", $id);
  $this->app->message('Položka byla uložena.');
  $this->redirect("lookups/view/lookup:{GET.lookup}");
}

function addlookupAction() {
  $this->title(1, 'Nový číselník');
  return new PCForm('tpl/lookupaddform.tpl');
}

}
?>
