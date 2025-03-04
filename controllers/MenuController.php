<?php
include 'BaseController.php';

class MenuController extends BaseController {

function indexAction() {
  $this->title(1, 'Editace menu');
  return new PCForm('tpl/lookups/menu.tpl');
}

function submitAction() {
  $form = new PCForm('tpl/lookups/menu.tpl');
  $id = (int)$form->values['TREE_ID'];
  if ($id) {
    $this->db->delete('TREE_LOOKUPS', ['ID' => $id]);
  } else {
    $id = $this->db->field('TREE_LOOKUPS:max(TREE_ID)') + 1;
    $this->db->insert('LOOKUPS',
      array('CNAME'=>'TREE', 'ID'=>$id, 'LABEL' => $form->values['TITLE'])
    );
  }

  $menu = new PCTree;
  $menu->importText($form->values['MENU']);
  $menu->save($id);
  $this->app->message('Data byla uložena.');
  $this->redirect('menu');
}

function deleteAction() {
  $form = new PCForm('tpl/lookups/menu.tpl');
  $id = (int)$form->values['TREE_ID'];
  if (!$id) $this->app->redirect('menu');

  $this->db->delete('TREE_LOOKUPS', "TREE_ID='{#0}'", $id);
  $this->db->delete('LOOKUPS', "CNAME='TREE' AND ID='{#0}'", $id);
  $this->app->message('Položka byla smazána.');
  $this->app->redirect('menu');
}

function ajax_loadAction($id) {
  $menu = new PCTree;
  $menu->load($id);
  die($menu->exportText("PATH|ROUTE|RKEY"));
}

}

?>
