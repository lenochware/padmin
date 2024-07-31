<?php

/**
 * Class JobsManager
 * @todo Doba behu jobu, celkovy pocet spusteni
 */
class JobsManager extends pclib\system\BaseObject
{
	public $db;
	public $logger;
	protected $jobs;

	/**
	 * JobsManager constructor.
	 * @throws \pclib\Exception
	 */
	public function __construct()
	{
		parent::__construct();
		$this->service('db');
		$this->logger = $this->service('logger');
	}

	/**
	 * @param string $name
	 * @throws Exception
	 */
	public function runJob($name)
	{
		$job = $this->getJob($name);
		$job = $this->start($job);

		try {
			$job['last_run_result'] = $this->runClassJob($job);
		}
		catch(Exception $e) {
			$job['last_run_result'] = $e->getMessage();
		}
		
		$this->finish($job);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getJob($name)
	{
		$this->getJobs();
		return $this->jobs[$name];
	}

	/**
	 * @return array
	 */
	public function getJobs()
	{
		if (!$this->jobs) {
			$this->loadJobs();
		}
		return $this->jobs;
	}

	public function loadJobs()
	{
		$this->jobs = [];
		foreach ($this->db->selectAll('jobs') as $job) {
			$this->jobs[$job['name']] = $job;
		}
	}

	/**
	 * @throws Exception
	 */
	public function run()
	{
		set_time_limit(0);
		foreach ($this->getJobs() as $job) {
			if (!$this->shouldRun($job['name'])) {
				continue;
			}
			$this->runJob($job['name']);
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function shouldRun($name)
	{
		$job = $this->getJob($name);

		if (!$job['active'] or $job['period'] == 0) {
			return false;
		}

		//spoustet v pravidelnych intervalech od firsttime, bez ohledu na iregular lasttime
		$firstTime = $this->getTime($job['first_run_at']);
		$lastTime = $this->getTime($job['last_run_at']);

		if (!$lastTime) {
			$nextTime = $firstTime;
		}
		else {
			$p = $job['period'];
			$nextTime = (floor(($lastTime - $firstTime) / $p) + 1) * $p + $firstTime;
		}

		return ($nextTime <= $this->currentTime());
	}

	/**
	 * @param array $job
	 */
	protected function log(array $job)
	{
		if (!$this->logger) {
			return;
		}
		$this->logger->log('jobs', 'padmin/job-run', $job['name'] . 
			' result: ' . $job['last_run_result'] . ' ('. $job['last_run_duration'] .'s)', $job['id']
		);
	}

	protected function start(array $job)
	{
		$job['start'] = microtime(true);
		$time = date('Y-m-d H:i:s', $this->currentTime());
		$job['last_run_at'] = $time;

		$this->db->update('jobs', ['last_run_at' => $time, 'last_run_result' => 'Job started...'], ['id' => $job['id']]);

		return $job;
	}

	/**
	 * @param array $job
	 */
	protected function finish(array $job)
	{
		$job['last_run_duration'] = round(microtime(true) - $job['start'], 2);
		unset($job['start']);
 
		$this->db->update('jobs', $job, ['id' => $job['id']]);
		$this->jobs[$job['name']] = $job;
		$this->log($job);
	}

	/**
	 * @return int
	 */
	protected function currentTime()
	{
		return time();
	}

	/**
	 * @param string $mysqlDate
	 * @return false|int
	 */
	protected function getTime($mysqlDate)
	{
		if (!$mysqlDate) return 0;
		return strtotime($mysqlDate);
	}

	/**
	 * Instancuje tridu Job a zavola Job->run().
	 * @param array $job
	 * @return mixed
	 */
	protected function runClassJob(array $job)
	{
		$className = $job['job_command'];
		if (!class_exists($className)) {
			throw new Exception("Class '$className' not found.");
		}

		$command = new $className($job);
		$result = $command->run();

		return $command->getOutput().$result;
	}

}

/**
 * Class Job
 * Superclass of any Job with job_type=class.
 */
abstract class Job
{
	protected $app;
	protected $data;
	protected $output = [];

	function __construct($data)
	{
		global $pclib;
		$this->app = $pclib->app;
		$this->data = $data;
	}

	function write($message)
	{
		$this->output[] = $message;
	}

	function getOutput()
	{
		if (!$this->output) return '';
		return implode("\n", $this->output);
	}

	abstract function run();
}
