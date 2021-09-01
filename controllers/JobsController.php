<?php
include 'BaseController.php';

/**
 * Class JobsController
 * Managing cron jobs with JobsManager class.
 */
class JobsController extends BaseController
{
	const TABLE = 'jobs';
	protected $jobs;

	function init()
	{
		global $pclib;

		parent::init();

		$this->app->layout->addScripts(
			'css/zebra_datepicker/bootstrap/zebra_datepicker.css',
			'css/zebra_datepicker/default.css',
			'js/zebra_datepicker.min.js'
		);

		$this->jobs = $this->getJobsManager();

		if ($this->app->config['jobs-dir']) {
			$pclib->autoloader->addDirectory($this->app->config['jobs-dir']);
		}
	}

	// @override
	function testPerm($perm)
	{
		if ($this->action == 'run') {
			if ($_GET['key'] != $this->getApiKey()) {
				throw new Exception('Neplatný aplikační klíč. Přístup zamítnut.');
			}
		} else {
			parent::testPerm($perm);
		}
	}


	protected function getApiKey()
	{
		return $this->app->config['api-key'];
	}


	protected function getJobsManager()
	{
		$jobs = new JobsManager;
		$jobs->logger = $this->app->logger;
		return $jobs;
	}

	/**
	 * @return \pclib\Grid
	 */
	public function indexAction()
	{
		$this->title(1, 'Plánovač úloh');
		$grid = new pclib\Grid('tpl/jobs/list.tpl');
		$grid->setQuery('select * from jobs');

		if (!$this->app->config['api-key']) {
			$runUrl = $this->app->request->getUrl() .'/run&key=[api_key]';
			$this->app->message("Nakonfigurujte CRON na periodické spouštění adresy:<br><code>$runUrl</code>");			
		}

		return $grid;
	}

	/**
	 * @return \pclib\Form
	 */
	protected function getForm()
	{
		return new pclib\Form('tpl/jobs/form.tpl');
	}

	/**
	 * @return \pclib\Form
	 */
	public function addAction()
	{
		$form = $this->getForm();
		$form->values['first_run_at'] = date('d. m. Y H:i');
		$form->enable('insert');
		$this->title(2, 'Nový');
		return $form;
	}

	/**
	 * Add Job to DB
	 */
	public function insertAction()
	{
		global $user;

		$this->testPerm('padmin/jobs/edit');
		$form = $this->getForm();
		if (!$form->validate()) {
			$this->invalid($form);
		}
		$form->_created_at = now();
		$form->_author_id = $user['ID'];
		$id = $form->insert($this::TABLE);
		$this->app->message('Položka byla přidána.');
		$this->redirect('jobs/edit/id:'.$id);
	}

	/**
	 * @param $id
	 * @return \pclib\Form
	 */
	public function editAction($id)
	{
		$form = $this->getForm();
		$form->values = $this->db->select($this::TABLE, pri($id));
		$form->enable('update', 'delete', 'runJob');
		$this->title(2, $form->values['name']);
		return $form;
	}

	/**
	 * @param $id
	 */
	public function updateAction($id)
	{
		$this->testPerm('padmin/jobs/edit');
		$form = $this->getForm();
		if (!$form->validate()) {
			$this->invalid($form);
		}

		$form->update($this::TABLE, pri($id));
		$this->app->message('Položka byla uložena.');
		$this->redirect('jobs/edit/id:'.$id);
	}

	/**
	 * @param $id
	 */
	public function deleteAction($id)
	{
		$this->testPerm('padmin/jobs/edit');
		$form = $this->getForm();
		if (!$form->validate()) {
			$this->invalid($form);
		}
		$form->delete($this::TABLE, pri($id));
		$this->app->message('Položka byla smazána.');
		$this->reload();
	}

	/**
	 * @param $id
	 * @throws \pclib\Exception
	 * @throws Exception
	 */
	public function runJobAction($id)
	{
		$job = $this->db->select($this::TABLE, pri($id));
		$this->jobs->runJob($job['name']);
		$this->app->message('Úloha byla spuštěna.');
		$this->redirect('jobs/edit/id:'.$id);
	}

	public function runAction($job = null)
	{
		if ($job) {
			$this->jobs->runJob($job);
			$this->outputJson(['status' => 'ok', 'message' => "Úloha '$job' byla spuštěna."]);
		}
		else {
			$this->jobs->run();
			$this->outputJson(['status' => 'ok', 'message' => 'Úlohy byly spuštěny.']);
		}

	}

}
