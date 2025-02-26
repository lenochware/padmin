<?php
include 'BaseController.php';

class MailsController extends BaseController {

function indexAction()
{
  $this->title(1, 'Odeslané emaily');

  $grid = new PCGrid('tpl/mails/list.tpl', 'mails');
  $grid->setQuery("select * from PCLIB_MAILS order by ID desc");

  return $grid;
}

function editAction($id)
{
  $mail = $this->db->select('PCLIB_MAILS', ['ID' => $id]);
  if (!$mail) $this->app->error('Položka nenalezena.');

  $this->title(2, $mail['SUBJECT']);

  // $mailer = new pclib\Mailer;
  // $mail = $mailer->get($id);

  $form = new PCForm('tpl/mails/form.tpl');
  $form->values = $mail;
  //$form->enable('update');
  return $form;
}

function updateAction($id)
{
  $form = new PCForm('tpl/mails/form.tpl');
  $form->update('PCLIB_MAILS', ['ID' => $id]);
  $this->app->message('Položka byla uložena.');
  $this->redirect("/self");  
}

}

?>