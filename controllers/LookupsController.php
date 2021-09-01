<?php
include 'BaseController.php';

class LookupsController extends BaseController {

function indexAction() {
  $this->title(1, 'Číselníky');
  $lookups = new PCGrid('tpl/lookups/list.tpl');
  $lookups->setquery('select distinct CNAME from LOOKUPS');
  return $lookups;
}

function viewAction($lookup) {
  $this->title(2, 'Číselník '. $lookup);
  $grid = new PCGrid('tpl/lookups/lookup.tpl');
  $grid->_CNAME = $lookup;
  $grid->setquery("select * from LOOKUPS where CNAME='{0}'", $lookup);
  return $grid;
}

function editAction($id) {
  $this->title(3, 'Editace');
  $form = new PCForm('tpl/lookups/form.tpl');
  $form->values = $this->db->select('LOOKUPS',"GUID='{#0}'", $id);
  $form->enable('update', 'delete');
  return $form;
}

function addAction($lookup) {
  $this->title(3, 'Nový');
  $form = new PCForm('tpl/lookups/form.tpl');
  $form->_CNAME = $lookup;
  $form->enable('insert');
  return $form;
}

function insertAction($lookup) {
  $form = new PCForm('tpl/lookups/form.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');
  $form->_CNAME = $lookup;
  $form->insert('LOOKUPS');
  $this->app->message('Položka byla uložena.');
  $this->redirect("lookups/view/lookup:$lookup");
}

function deleteAction($id) {
  $form = new PCForm('tpl/lookups/form.tpl');
  $form->delete('LOOKUPS', "GUID='{#0}'", $id);
  $this->app->message('Položka byla smazána.');
  $this->redirect("lookups/view/lookup:{GET.lookup}");
}

function updateAction($id) {
  $form = new PCForm('tpl/lookups/form.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');
  $form->update('LOOKUPS', "GUID='{#0}'", $id);
  $this->app->message('Položka byla uložena.');
  $this->redirect("lookups/view/lookup:{GET.lookup}");
}

function addlookupAction() {
  $this->title(1, 'Nový číselník');
  return new PCForm('tpl/lookups/add.tpl');
}

function exportAction() {
  $form = new PCForm('tpl/lookups/export.tpl');
  $form->_STEXT = $this->getLookupsExport();
  return $form;
}

function importAction()
{
  $form = new PCForm('tpl/lookups/export.tpl');
  $s = $form->values['STEXT'];

  $this->importLookups($s);

  $this->app->message("hotovo.");
  $this->redirect('lookups/export');
}

protected function getLookupsExport()
{
  $s = '';
  $list = $this->db->selectAll("select * from LOOKUPS order by CNAME,GUID");

  foreach ($list as $row) {
    $s .= paramStr("{APP};{CNAME};{ID};{LABEL};{POSITION}\n", $row);
  }

  return $s;
}

protected function importLookups($s)
{
  $rows = explode("\n", $s);

  $i = $j = 0;

  foreach ($rows as $row) {
    $in = explode(";", trim($row));
    if (!$in[1]) continue;

    $data = ['APP' => $in[0], 'CNAME' => $in[1], 'ID' => $in[2]];
    $guid = $this->db->field('LOOKUPS:GUID', $data);

    if ($guid) {
      if ($in[4] == 'x')
         $this->db->delete('LOOKUPS', "GUID='$guid'");
      else 
        $this->db->update('LOOKUPS', ['LABEL' => $in[3], 'POSITION' => $in[4]], "GUID='$guid'");
      
      $i++;
    }
    else {
      $data['LABEL'] = $in[3];
      $data['POSITION'] = $in[4];
      $this->db->insert('LOOKUPS', $data);
      $j++;
    }
  }

  $this->app->message("$i aktualizováno, $j přidáno.");
}

}
?>
