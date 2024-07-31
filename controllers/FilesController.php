<?php
include 'BaseController.php';

/**
 * Class FilesController
 * Managing uploaded files.
 */
class FilesController extends BaseController
{
	protected $uploadDir;

	function init()
	{
		parent::init();
		$this->uploadDir = array_get($this->app->config, 'upload-dir', '../uploaded');
	}

	/**
	 * @return \pclib\Grid
	 */
	public function indexAction()
	{
		$this->title(1, 'Nahrané soubory');
		$grid = new pclib\Grid('tpl/files/list.tpl', 'files');
		$grid->setQuery(
			"select * from FILESTORAGE 
			where 1=1
			~ and ORIGNAME like '%{ORIGNAME}%'
			~ and ENTITY_TYPE like '%{ENTITY_TYPE}%'
			~ and ENTITY_ID='{ENTITY_ID}'
			~ and DT like '{DT}%'
			order by ID desc"
		);

		$grid->values['TOTAL_SIZE'] = $this->totalSize();
		$grid->setAttr('SIZE', 'onprint', [$this, 'fileSize']);

		$search = new SearchForm($grid, ['ORIGNAME', 'ENTITY_TYPE', 'ENTITY_ID', 'DT']);

		return $search.$grid;
	}

	public function showAction($id)
	{
		try {
			$fs = new pclib\FileStorage($this->uploadDir);
			$fs->output($id);			
		}
		catch(Exception $e) {
			$this->app->error('Soubor nenalezen.');
		}

	}

	protected function formatSize($size)
	{
		$units = array( 'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$power = $size > 0 ? floor(log($size, 1024)) : 0;
		return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
	}

	public function fileSize($obj, $id, $sub, $value)
	{
		print $this->formatSize($value);
	}

	protected function totalSize()
	{
		if ($this->app->getSession('files-size')) return $this->app->getSession('files-size');
		$size = $this->formatSize($this->db->field("select sum(SIZE) from FILESTORAGE"));
		$this->app->setSession('files-size', $size);
		return $size;
	}


}
