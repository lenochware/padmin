<?php
include 'BaseController.php';

use pclib\extensions\GridForm;

class RlookupsController extends BaseController {

private $title = array('role' => 'Seznam rolí', 'right' => 'Seznam oprávnění');
private $table = array('role' => 'AUTH_ROLES', 'right' => 'AUTH_RIGHTS');
private $authMng;

function init() {
  parent::init();
  $lookup = $_GET['lookup'];
  if (!in_array($lookup, array_keys($this->table))) {
    $this->app->error('Neplatný číselník!');
  }

  $this->authMng = new pclib\extensions\AuthManager;
}

function viewAction($lookup) {
  $grid = new PCGrid('tpl/rlookup.tpl');
  $grid->_LOOKUP = $lookup;
  $grid->_TITLE = $this->title[$lookup];
  $this->title(1, $this->title[$lookup]);
  if ($lookup == 'role') {
    $grid->addtag('link lnset lb "Nastavení" route "rlookups/rlist/lookup:role/id:{ID}"');
  }
  $grid->setquery('select * from '.$this->table[$lookup]);
  return $grid;
}

function addAction($lookup) {
  $this->title(2, 'Nová '.$lookup);
  $form = new PCForm('tpl/rlookupform.tpl');
  $form->_TITLE = $lookup;
  $form->enable('insert');
  return $form;
}

function editAction($lookup, $id) {
  $form = new PCForm('tpl/rlookupform.tpl');
  $form->values = $this->db->select($this->table[$lookup], pri($id));
  $form->_TITLE = $lookup;
  $this->title(2, 'Editace '.$lookup);
  $form->enable('update', 'delete');
  return $form;
}

function insertAction($lookup) {
  $form = new PCForm('tpl/rlookupform.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');
  
  if ($lookup == 'right')
    $this->authMng->mkright($form->values['SNAME'], $form->values['ANNOT']);
  else
    $this->authMng->mkrole($form->values['SNAME'], $form->values['ANNOT']);

  if ($this->authMng->errors)
    $this->app->error($this->authMng->errors);
  else
    $this->app->message('Položka byla uložena.');
    
  $this->redirect("rlookups/view/lookup:$lookup");
}


function deleteAction($lookup, $id) {
  if ($lookup == 'right')
    $this->authMng->rmright('#'.$id);
  else
    $this->authMng->rmrole('#'.$id);

  if ($this->authMng->errors)
    $this->app->error($this->authMng->errors);
  else
    $this->app->message('Položka byla odstraněna.');

  $this->redirect("rlookups/view/lookup:$lookup");
}

function updateAction($lookup, $id) {
  $form = new PCForm('tpl/rlookupform.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');

  $form->values['ID'] = $id;
  if ($lookup == 'right') $this->authMng->setright($form->values);
  else $this->authMng->setrole($form->values);

  if ($this->authMng->errors)
    $this->app->error($this->authMng->errors);
  else
    $this->app->message('Položka byla uložena.');

  $this->redirect("rlookups/view/lookup:$lookup");
}

function rlistAction($lookup, $id) {
  $this->title(3, 'Práva');
  $grid = new GridForm('tpl/rlist.tpl');
  
  if ($lookup == 'role') {
    $grid->filter['ROLE_ID'] = $id;
    $grid->_TITLE = "roli ".$this->db->field('AUTH_ROLES:SNAME', pri($id));
  }
  else {
    $grid->_USER_ID = $id;
    $grid->filter['USER_ID'] = $id;
    $grid->_TITLE = "uživatele ".$this->db->field('AUTH_USERS:USERNAME', pri($id));
  }
  
  $grid->_STATUS->onprint = array($this, 'getstatus');
  
  $grid->setquery(
  "select R.*,
    CASE rval WHEN '0' THEN '2'
     WHEN '1' THEN '1'
     ELSE '3'
    END as RSET
 from AUTH_RIGHTS R
  left join AUTH_REGISTER REG on REG.RIGHT_ID=R.ID
  and
  ~ REG.ROLE_ID = '{ROLE_ID}'
  ~ REG.USER_ID = '{USER_ID}'
  ~ where R.SNAME like '{SNAME}%'"
  );
  return $grid;
}

function rupdateAction($lookup, $id) {
  $grid = new GridForm('tpl/rlist.tpl');

  foreach($grid->values as $ra) {
    switch ($ra['RSET']) {
      case '1': $rval = '1';  break;
      case '2': $rval = '0';  break;
      case '3': $rval = null; break;
      default: continue;
    }

    $right_id = (int)$ra['ID'];
    if ($lookup == 'right')
      $this->authMng->ugrant('#'.$id, '#'.$right_id, $rval);
    elseif ($lookup == 'role')
      $this->authMng->rgrant('#'.$id, '#'.$right_id, $rval);
  };

  $this->app->message('Položky byly uloženy.');
  $this->app->redirect("rlookups/rlist/{GET}");
}

function getstatus($o, $id, $sub, $value) {
  if ($sub) return true;

  $rset = $o->getvalue('RSET');
  $user_id = $o->getvalue('USER_ID');

  if ($rset == '1') print "Povolen";
  else if ($rset == '2') print "<span style='color:red'>Zakázán</span>";
  else if ($user_id) {
    $x = $this->app->auth->user('#'.$user_id)->hasright($o->getvalue('SNAME'));
    if ($x) print "Povolen R"; else  print "<span style='color:red'>Zakázán</span> R";
  }
}

}
?>
