<?php
include 'BaseController.php';

class UsersController extends BaseController {

private $authMng;

protected $allowedFields = ['USERNAME','FULLNAME','EMAIL','ANNOT','ACTIVE'];

function init()
{
  parent::init();
  $this->authMng = new \pclib\extensions\AuthManager;
}

function indexAction()
{
  $this->title(1, "Uživatelé");
  $users = $this->getGrid();
  $search = new SearchForm($users, ['USERNAME', 'ANNOT', 'ROLE', 'ACTIVE']);
  $search->addTag('check new_users lb "Noví" html_title "za poslední měsíc"');
  $search->addTag('check inactive lb "Nepřihlášení 1y"');
  return $search.$users;
}

function exportAction()
{
  $users = $this->getGrid();  
  $users->exportExcel('users.csv');
}

function editAction($id)
{
  $form = $this->getForm();
  $form->values = $this->db->select('AUTH_USERS', ['ID' => $id]);
  $author_id = array_get($form->values, 'AUTHOR_ID');
  $form->values['AUTHOR'] = $this->db->field('AUTH_USERS:USERNAME', ['ID' => $author_id]);
  $form->_RINDIV = implode('<br>', $this->getRights($id));

  $i = 0;
  foreach($this->getRoles($id) as $role_id => $tmp) {
    $form->values['ROLE'.(++$i)] = $role_id;
  }

  if ($form->values['PASSW']) {
    $form->_PASSWORD = '(hidden)';
  }

  $form->enable('copy', 'update', 'delete', 'impersonate');
  $this->title(2, $form->values['FULLNAME']);
  return $form;
}

function addAction()
{
  $form = $this->getForm();
  $form->enable('insert');
  $this->title(2, 'Nový uživatel');
  return $form;
}

function insertAction()
{
  $form = $this->getForm();
  if (!$form->validate()) $this->invalid($form);

  $id = $this->authMng->mkUser($form->_USERNAME);
  if (!$id) $this->app->error(implode('<br>', $this->authMng->errors));

  $this->setUser($id, $form->preparedValues());

  if ($this->authMng->errors) {
    $this->app->error(implode('<br>', $this->authMng->errors));
  }

  $this->app->message('Položka byla přidána.');
  $this->redirect('users/edit/id:'.$id);
}

function updateAction($id)
{
  $form = $this->getForm();
  if (!$form->validate()) $this->invalid($form);

  $this->setUser($id, $form->preparedValues());

  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));

  $this->app->message('Položka byla uložena.');
  $this->redirect('users/edit/id:'.$id);
}

function deleteAction($id)
{
  $this->authorize('padmin/users/delete');

  $form = $this->getForm();
  //if (!$form->validate()) $this->invalid($form);

  $this->authMng->rmUser('#'.$id);
  $this->app->message('Položka byla smazána.');
  $this->reload();
}

function impersonateAction()
{
  $this->authorize('padmin/users/impersonate');

  $auth = $this->app->auth;

  $form = $this->getForm();
  if (!$form->validate()) $this->invalid($form);

  $user = $auth->getUser($form->values['USERNAME']);
  $auth->setLoggedUser($user);
  $this->app->message('Přihlášení změněno.');
  $this->reload();
}

function copyAction()
{
  $form = $this->getForm();
  if (!$form->validate()) $this->invalid($form);

  $name = $form->values['USERNAME'];
  if (!$name) $this->app->error('Uživatel nenalezen.');

  $uid = $this->authMng->mkUser($name.'_copy');
 	$this->authMng->cpUser($name, $name.'_copy');
  
  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));  

	$this->app->message('Uživatel zkopírován.');
  $this->app->redirect("users/edit/id:$uid");
}

/* @ajax */
function genPasswordAction()
{
  $password = $this->authMng->genPassw();
  die($password);
}

protected function getRoles($user_id = null)
{
  if ($user_id) {
    return $this->db->selectPair(
    "select R.ID, coalesce(R.ANNOT,R.SNAME) from AUTH_USER_ROLE UR
    left join AUTH_ROLES R on R.ID=UR.ROLE_ID
    where UR.USER_ID='{#0}' order by R_PRIORITY desc",
    $user_id
    );
  }
  else {
    return $this->db->selectPair(
    "select ID,coalesce(ANNOT,SNAME) from AUTH_ROLES"
    );
  }
}

protected function getRights($user_id)
{
  $rows = $this->db->selectAll(
    "select SNAME,REG.RVAL as RVAL from AUTH_RIGHTS R
    left join AUTH_REGISTER REG on R.ID=REG.RIGHT_ID
    where REG.USER_ID={#0}", $user_id
  );

  $rights = [];
  foreach ($rows as $row) {
    $rights[] = $row['RVAL'] == '1' ? $row['SNAME'] : $row['SNAME'] . ": ".$row['RVAL'];
  }

  return $rights;
}

function userRoles($o, $id, $sub, $val)
{
  if ($sub) return true;

  $user_id = (int)$o->getValue('ID');
  if (!$user_id) return;
  
  $o->print_String('USERROLES', '', implode(',', array_values($this->getRoles($user_id))));
}

protected function setRoles($uid, $roles)
{
  $this->db->delete('AUTH_USER_ROLE', "USER_ID='{#0}'", $uid);
  foreach($roles as $role) {
    if (!$role) continue;
    $this->authMng->uRole('#'.$uid, '#'.$role);
  }
}

function setUser($id, $data)
{
  $u = [];
  $r = [];
  foreach($data as $k => $v) {
    if (strpos($k, 'ROLE') === 0) $r[] = $v;
    elseif(in_array($k, $this->allowedFields)) $u[$k] = $v;
  }

  $this->authMng->setUser('#'.$id, $u);
  $this->setRoles($id, $r);

  $password = $data['PASSWORD'];
  if($password != '(hidden)')  $this->authMng->setPassw('#'.$id, $password);
}

protected function getForm()
{
  $form = new PCForm('tpl/users/form.tpl');
  $roles = $this->getRoles();
  for ($i = 1; $i < 6; $i++)
    $form->elements['ROLE'.$i]['items'] = $roles;

  return $form;
}

protected function getGrid()
{
  $grid = new PCGrid('tpl/users/list.tpl', 'users');

  if (isset($grid->filter['new_users'])) unset($grid->filter['inactive']); //hack
  
  $grid->setQuery(
    "SELECT DISTINCT U.* from AUTH_USERS U
     left join AUTH_USER_ROLE UR on U.ID=UR.USER_ID
     where 1=1
     ~ AND UR.ROLE_ID='{ROLE}'
     ~ AND ACTIVE='{ACTIVE}'
     ~ AND USERNAME like '%{USERNAME}%'
     ~ AND ANNOT like '%{ANNOT}%'
     ~ AND U.DT>NOW() - INTERVAL 1 month order by DT desc {?new_users}
     ~ AND (U.LAST_LOGIN<NOW() - INTERVAL 1 year or LAST_LOGIN is null) order by LAST_LOGIN {?inactive}
     "
  );

  $grid->_USERROLES->onprint = [$this, 'userRoles'];
  
  return $grid;
}

}

?>