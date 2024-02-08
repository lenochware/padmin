<?php
include 'BaseController.php';

use pclib\extensions\GridForm;

class RolesController extends BaseController {

private $authMng;

function init() {
  parent::init();
  $this->authMng = new pclib\extensions\AuthManager;
}

function indexAction() {
  $grid = new PCGrid('tpl/roles/list.tpl', 'rlookup');
  $grid->_LOOKUP = 'role';
  $grid->_TITLE = 'Seznam rolí';
  $this->title(1, 'Seznam rolí');

  $grid->addtag('link lnset lb "Nastavení" route "roles/rights/id:{ID}"');
  $grid->setquery('select * from AUTH_ROLES');

  return $grid;
}

function exportAction() {
  $form = new PCForm('tpl/lookups/export.tpl');
  $form->_STEXT = $this->getRolesExport();
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
  $this->redirect('roles/export');
}

protected function getRolesExport()
{
  $s = '';
  $roles = $this->db->selectAll('select * from AUTH_ROLES order by DT');
  foreach ($roles as $role) {
    $s .= "+role ".$role['SNAME'] . ($role['ANNOT']? ' "'.$role['ANNOT'].'"': ''). "\n";
    $s .= $this->getRoleRights($role);
    $s .= "\n";
  }

  return $s;
}

protected function getRoleRights($role)
{
  $html = '';

  $rights = $this->db->selectAll(
    "select r.* from AUTH_REGISTER reg left join AUTH_RIGHTS r on reg.RIGHT_ID=r.ID
    where reg.ROLE_ID='{0}'", $role['ID']
  );

  foreach ($rights as $r) {
    $html .= "role ".$role['SNAME']." +right ".$r['SNAME'] . "\n";
  }

  return $html;
}

function addAction() {
  $this->title(2, 'Nová role');
  $form = new PCForm('tpl/roles/form.tpl');
  $form->_TITLE = 'role';
  $form->enable('insert');
  return $form;
}

function editAction($id) {
  $form = new PCForm('tpl/roles/form.tpl');
  $form->values = $this->db->select('AUTH_ROLES', pri($id));
  $form->values['AUTHOR'] = $this->db->field('AUTH_USERS:USERNAME', pri($form->values['AUTHOR_ID']));
  $form->_TITLE = 'role';
  $this->title(2, 'Editace role');
  $form->enable('update', 'delete');
  return $form;
}

function insertAction() {
  $form = new PCForm('tpl/roles/form.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');
  
  $this->authMng->mkrole($form->values['SNAME'], $form->values['ANNOT']);

  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));
  else
    $this->app->message('Položka byla uložena.');
    
  $this->redirect("roles");
}


function deleteAction($id) {

  $this->authMng->rmrole('#'.$id);

  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));
  else
    $this->app->message('Položka byla odstraněna.');

  $this->redirect("roles");
}

function updateAction($id) {
  $form = new PCForm('tpl/roles/form.tpl');
  if (!$form->validate()) $this->app->error('Chybně vyplněný formulář.');

  $form->values['ID'] = $id;
  $this->authMng->setrole($form->values);

  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));
  else
    $this->app->message('Položka byla uložena.');

  $this->redirect("roles");
}

function rightsAction($id) {
  $this->title(3, 'Práva');
  $grid = new GridForm('tpl/roles/edit.tpl', 'rolerights');
  
  $grid->_TITLE = "roli ".$this->db->field('AUTH_ROLES:SNAME', pri($id));
  $grid->_STATUS->onprint = array($this, 'getStatus');
  $grid->filter['ROLE_ID'] = $id;
  
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
  $search->addTag('check ALLOWED lb "Povolen"');

  return $search->html().$grid;
}

function rupdateAction($id) {
  $grid = new GridForm('tpl/roles/edit.tpl');

  foreach($_POST['rowdata'] as $ra) {
    switch ($ra['RSET']) {
      case '1': $rval = '1';  break;
      case '2': $rval = '0';  break;
      case '3': $rval = null; break;
      default: continue 2;
    }

    $right_id = (int)$ra['ID'];
    $this->authMng->rgrant('#'.$id, '#'.$right_id, $rval);
  };

  $this->app->message('Položky byly uloženy.');
  $this->app->redirect("roles/rights/{GET}");
}

function setFilter($filter) {
  $this->app->setsession('rolerights.filter', $filter);
  $this->app->setsession('search-rolerights.values', $filter);
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