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

		$job = $this->start($job);

		try {
			$job['last_run_result'] = call_user_func($runner, $job);
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

		if (!$job['active']) {
			return false;
		}
		if ($job['period'] == 0 and $job['last_run_at']) {
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
		if (!class_exists($className)) {
			throw new Exception("Class '$className' not found.");
		}

		$command = new $className($job);
		$result = $command->run();

		return $command->getOutput().$result;
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
	protected $output = [];

	function __construct($params)
	{
		global $pclib;
		$this->app = $pclib->app;
		$this->params = $params;
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
