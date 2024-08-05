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
  $users = new PCGrid('tpl/users/list.tpl', 'users');

  if (isset($users->filter['new_users'])) unset($users->filter['inactive']); //hack
  
  $users->setQuery(
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

  $users->_USERROLES->onprint = [$this, 'userroles'];
  $search = new PCForm('tpl/users/search.tpl', 'usersearch');
  
  return $search.$users;
}

function exportAction()
{
  $users = new PCGrid('tpl/users/list.tpl', 'users');

  $users->setQuery(
  "SELECT DISTINCT U.* from AUTH_USERS U
   left join AUTH_USER_ROLE UR on U.ID=UR.USER_ID
   where 1=1
   ~ AND UR.ROLE_ID='{ROLE}'
   ~ AND ACTIVE='{INACTIVE}'
   ~ AND USERNAME like '%{USERNAME}%'
   ~ AND ANNOT like '%{ANNOT}%'
   ~ AND U.DT>NOW() - INTERVAL 1 month {?new_users}
   ~ AND (U.LAST_LOGIN<NOW() - INTERVAL 1 year or LAST_LOGIN is null) order by LAST_LOGIN {?inactive}
   "
  );

  $users->_USERROLES->onprint = [$this, 'userroles'];
    
  $users->exportExcel('users.csv');
}

function editAction($id)
{
  $user = $this->getForm();
  $user->values = $this->db->select('AUTH_USERS', ['ID' => $id]);
  $author_id = array_get($user->values, 'AUTHOR_ID');
  $user->values['AUTHOR'] = $this->db->field('AUTH_USERS:USERNAME', ['ID' => $author_id]);
  $user->_RINDIV = implode('<br>', $this->getRights($id));

  $i = 0;
  foreach($this->getRoles($id) as $role_id => $tmp) {
    $user->values['ROLE'.(++$i)] = $role_id;
  }

  if ($user->values['PASSW']) {
    $user->_PASSWORD = '(hidden)';
  }

  $user->enable('copy', 'update', 'delete', 'impersonate');
  $this->title(2, $user->values['FULLNAME']);
  return $user;
}

function addAction()
{
  $user = $this->getForm();
  $user->enable('insert');
  $this->title(2, 'Nový uživatel');
  return $user;
}

function insertAction()
{
  $userform = $this->getForm();
  if (!$userform->validate()) $this->invalid($userform);

  $id = $this->authMng->mkUser($userform->_USERNAME);
  if (!$id) $this->app->error(implode('<br>', $this->authMng->errors));

  $this->setUser($id, $userform->preparedValues());

  if ($this->authMng->errors) {
    $this->app->error(implode('<br>', $this->authMng->errors));
  }

  $this->app->message('Položka byla přidána.');
  $this->redirect('users/edit/id:'.$id);
}

function updateAction($id)
{
  $userform = $this->getForm();
  if (!$userform->validate()) $this->invalid($userform);

  $this->setUser($id, $userform->preparedValues());

  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));

  $this->app->message('Položka byla uložena.');
  $this->redirect('users/edit/id:'.$id);
}

function deleteAction($id)
{
  $this->testPerm('padmin/users/delete');

  $userform = $this->getForm();
  //if (!$userform->validate()) $this->invalid($userform);

  $this->authMng->rmUser('#'.$id);
  $this->app->message('Položka byla smazána.');
  $this->reload();
}

function impersonateAction()
{
  $this->testPerm('padmin/users/impersonate');

  $auth = $this->app->auth;

  $userform = $this->getForm();
  if (!$userform->validate()) $this->invalid($userform);

  $user = $auth->getUser($userform->values['USERNAME']);
  $auth->setLoggedUser($user);
  $this->app->message('Přihlášení změněno.');
  $this->reload();
}

function copyAction()
{
  $userform = $this->getForm();
  if (!$userform->validate()) $this->invalid($userform);

  $name = $userform->values['USERNAME'];
  if (!$name) $this->app->error('Uživatel nenalezen.');

  $uid = $this->authMng->mkUser($name.'_copy');
 	$this->authMng->cpUser($name, $name.'_copy');
  
  if ($this->authMng->errors)
    $this->app->error(implode('<br>', $this->authMng->errors));  

	$this->app->message('Uživatel zkopírován.');
  $this->app->redirect("users/edit/id:$uid");
}

function searchAction()
{
  $this->enableFilter();
  $this->reload();
}

function showallAction() {
  $this->enableFilter(false);
  $this->reload();
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
  $data = $this->db->selectOne(
    "select SNAME from AUTH_RIGHTS R
    left join AUTH_REGISTER REG on R.ID=REG.RIGHT_ID
    where REG.USER_ID={#0}", $user_id
  );
  return $data;
}

function enableFilter($enable = true)
{
  $filter = $enable? $_POST['data'] : null;

  $this->app->setSession('users.filter', $filter);
  $this->app->setSession('usersearch.values', $filter);
  if (!$filter) $this->app->setSession('users.sortarray', null);
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

protected function getForm() {
  $form = new PCForm('tpl/users/form.tpl');
  $roles = $this->getRoles();
  for ($i = 1; $i < 6; $i++)
    $form->elements['ROLE'.$i]['items'] = $roles;

  return $form;
}

}

?>