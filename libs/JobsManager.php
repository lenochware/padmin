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
	public $runners;

	/**
	 * JobsManager constructor.
	 * @throws \pclib\Exception
	 */
	public function __construct()
	{
		parent::__construct();
		$this->service('db');
		$this->runners = [
			'shell' => [$this, 'runShellJob'],
			'url' => [$this, 'runUrlJob'],
			'function' => [$this, 'runFunctionJob'],
			'class' => [$this, 'runClassJob'],
			'phpfile' => [$this, 'runPhpFileJob'],
			'batch' => [$this, 'runBatchJob'],
		];
		$this->logger = $this->service('logger');
	}

	/**
	 * @param string $name
	 * @throws Exception
	 */
	public function runJob($name)
	{
		$job = $this->getJob($name);
		$runner = $this->runners[$job['job_type']];
		if (!$runner) {
			throw new Exception('Unsupported job type.');
		}
		$job['last_run_result'] = call_user_func($runner, $job);
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
		if (!$job['active']) {
			return false;
		}
		if ($job['period'] == 0 and $job['last_run_at']) {
			return false;
		}
		return ($this->getTime($job['last_run_at'] ?: $job['first_run_at']) + $job['period'] < $this->currentTime());
	}

	/**
	 * @param array $job
	 */
	protected function log(array $job)
	{
		if (!$this->logger) {
			return;
		}
		$this->logger->log('JobsManager', 'job.run', $job['name'] . ' result: ' . $job['last_run_result'], $job['id']);
	}

	/**
	 * @param array $job
	 */
	protected function finish(array $job)
	{
		$job['last_run_at'] = date('Y-m-d H:i:s', $this->currentTime());
		$this->db->update('jobs', $job, pri($job['id']));
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
		return strtotime($mysqlDate);
	}

	/**
	 * Spusti prikaz shellu.
	 * @param array $job
	 * @return string
	 */
	protected function runShellJob(array $job)
	{
		return shell_exec($job['job_command']);
	}

	/**
	 * Zavola url.
	 * @param array $job
	 * @return bool|mixed|string
	 */
	protected function runUrlJob(array $job)
	{
		$url = $job['job_command'];
		if (extension_loaded('curl')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
		} else {
			$response = file_get_contents($url);
		}
		return $response;
	}

	/**
	 * Spusti php funkci.
	 * @param array $job
	 * @return mixed
	 */
	protected function runFunctionJob(array $job)
	{
		return call_user_func($job['job_command'], $job);
	}

	/**
	 * Instancuje tridu Job a zavola Job->run().
	 * @param array $job
	 * @return mixed
	 */
	protected function runClassJob(array $job)
	{
		$className = $job['job_command'];
		$command = new $className($job);
		return $command->run();
	}

	/**
	 * Includuje php skript (path je v job_command) a vrati vysledek.
	 * @param array $job
	 * @return string
	 */
	protected function runPhpFileJob(array $job)
	{
		ob_start();
		require $job['job_command'];
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}

	/**
	 * Spusti davkove nekolik jobu. Nazvy jobu jsou v job_command oddelene strednikem.
	 * @param array $job
	 * @throws Exception
	 */
	protected function runBatchJob(array $job)
	{
		foreach (explode(';', $job['job_command']) as $jobName) {
			$this->runJob(trim($jobName));
		}
	}

}

/**
 * Class Job
 * Superclass of any Job with job_type=class.
 */
abstract class Job
{
	protected $app;
	protected $params;

	function __construct($params)
	{
		global $pclib;
		$this->app = $pclib->app;
		$this->params = $params;
	}

	abstract function run();
}