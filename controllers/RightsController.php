<?php
include 'BaseController.php';

use pclib\extensions\GridForm;

class RightsController extends BaseController {

private $authMng;

function init() {
  if (in_array($this->action, ['user', 'rupdate', 'search', 'showall'])) {
    $this->testPerm('padmin/users');
  }
  else {
    $this->testPerm('padmin/'.$this->app->controller);
  }

  $this->authMng = new pclib\extensions\AuthManager;
}

function indexAction() {
  $grid = new PCGrid('tpl/rights/list.tpl', 'rlookup');
  $grid->_LOOKUP = 'right';
  $grid->_TITLE = 'Seznam oprávnění';
  $this->title(1, 'Seznam oprávnění');
  $grid->setquery('select * from AUTH_RIGHTS');
  return $grid;
}

function exportAction() {
  $form = new PCForm('tpl/lookups/export.tpl');
  $form->_STEXT = $this->getRightsExport();
  return $form;
}

function importAction()
{
  $form = new PCForm('tpl/lookups/export.tpl');
  $s = $form->values['STEXT'];

  $authCon = new pclib\extensions\AuthConsole($this->authMng);
  $authCon->executeScript($s);

  $message = implode("<br>", $authCon->messages);

  $this->app->message($message);
  $this->redirect('rights/export');
}

protected function getRightsExport()
{
  $s = '';
  $rights = $this->db->selectAll('select * from AUTH_RIGHTS order by DT', 'order by DT');
  foreach ($rights as $right) {
    $s .= "+right ".$right['SNAME'] . ($right['ANNOT']? ' "'.$right['ANNOT'].'"': ''). "\n";
  }

  return $s;
}

function addAction() {
  $this->title(2, 'Nové oprávnění');
  $form = new PCForm('tpl/rights/form.tpl');
  $form->_TITLE = 'right';
  $form->enable('insert');
  return $form;
}

function editAction($id) {
  $form = new PCForm('tpl/rights/form.tpl');
  $form->values = $this->db->select('AUTH_RIGHTS', pri($id));
  $form->_TITLE = 'right';
  $this->title(2, 'Editace oprávnění');
  $form->enable('update', 'delete');
  return $form;
}

function insertAction() {
  $form = new PCForm('tpl/rights/form.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');
  
  $this->authMng->mkright($form->values['SNAME'], $form->values['ANNOT']);

  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));
  else
    $this->app->message('Položka byla uložena.');
    
  $this->redirect("rights");
}


function deleteAction($id) {
  $this->authMng->rmright('#'.$id);

  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));
  else
    $this->app->message('Položka byla odstraněna.');

  $this->redirect("rights");
}

function updateAction($id) {
  $form = new PCForm('tpl/rights/form.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');

  $form->values['ID'] = $id;
  $this->authMng->setright($form->values);

  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));
  else
    $this->app->message('Položka byla uložena.');

  $this->redirect("rights");
}

function userAction($id) {
  $this->title(3, 'Práva');
  $grid = new GridForm('tpl/rights/edit.tpl', 'userrights');
  
  $grid->_USER_ID = $id;
  $grid->_TITLE = "uživatele ".$this->db->field('AUTH_USERS:USERNAME', pri($id));
  $grid->_STATUS->onprint = array($this, 'getStatus');
  $grid->filter['USER_ID'] = $id;
  
  $grid->setquery(
  "select R.*,
    CASE rval WHEN '0' THEN '2'
     WHEN '1' THEN '1'
     ELSE '3'
    END as RSET, if(R.RTYPE='B', 1, 0) as RBOOL, REG.RVAL
 from AUTH_RIGHTS R
  left join AUTH_REGISTER REG on REG.RIGHT_ID=R.ID
  ~ and REG.ROLE_ID = '{ROLE_ID}'
  ~ and REG.USER_ID = '{USER_ID}'
  where 1=1
  ~ and R.SNAME like '%{SNAME}%'
  ~ and REG.rval is not null {?ALLOWED}
  order by R.SNAME"
  );

  $search = new SearchForm($grid, ['SNAME']);
  $search->addTag('check ALLOWED lb "Nastaven"');

  return $search->html().$grid;
}

function rupdateAction($id) {
  $grid = new GridForm('tpl/rights/edit.tpl');

  foreach($_POST['rowdata'] as $ra) {
    switch ($ra['RSET']) {
      case '1': $rval = '1';  break;
      case '2': $rval = '0';  break;
      case '3': $rval = null; break;
      default: continue 2;
    }

    $right_id = (int)$ra['ID'];
    $this->authMng->ugrant('#'.$id, '#'.$right_id, $rval);
  };

  $this->app->message('Položky byly uloženy.');
  $this->app->redirect("rights/user/{GET}");

}

function getStatus($o, $id, $sub, $value) {
  if ($sub) return true;

  $rset = $o->getValue('RSET');
  $user_id = $o->getValue('USER_ID');

  if ($rset == '1') print "Povolen";
  else if ($rset == '2') print "<span style='color:red'>Zakázán</span>";
  else if ($user_id) {
    $userData = $this->authMng->getUser('#'.$user_id);
    $user = $this->app->auth->getUser($userData['USERNAME']);
    $x = $user->hasRight($o->getvalue('SNAME'));
    if ($x) print "Povolen R"; else  print "<span style='color:red'>Zakázán</span> R";
  }
}

}
?>
