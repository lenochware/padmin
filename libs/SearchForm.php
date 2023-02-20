<?php 

class SearchForm extends pclib\Form {

	function __construct($pathOrGrid, array $fields)
	{
		$grid = is_string($pathOrGrid)? new pclib\Grid($pathOrGrid) : $pathOrGrid;	
		
		parent::__construct('tpl/search.tpl', 'search-'.$grid->name);

		$el = [];
		foreach ($fields as $name) {
			$el[$name] = $grid->elements[$name];
			if ($el[$name]['type'] == 'bind') {
				$el[$name]['type'] = 'select';
				$el[$name]['size'] = 1;
			}
			else {
				$el[$name]['type'] = 'input';
				$el[$name]['onprint'] = null; //custom field hack
				if ($el[$name]['date']) {
					$el[$name]['html']['class'] = 'calendar';
				}
			}
			$el[$name]['skip'] = null;
		}

		if ($this->isFiltered($grid)) {
			$this->values['FOUND'] = paramStr('<div class="message">Nalezeno {0} položek.</div>', [$grid->length]);
		}

		$this->elements += $el;
	}

	function isFiltered($grid)
	{
		$filter = $grid->filter;
		unset($filter['ou']);
		return (bool)$filter;
	}

	function addTagSearch($category)
	{
		$this->addTag("select TAG datasource \"base/getTags/entity:$category\" lb \"Štítek\"");
	}


  static function enableFilter($grid)
  {
  	global $pclib;

    $name = $grid->name;
    $data = self::prepareFilterData($_POST? $_POST['data'] : $_GET);

    $pclib->app->setsession("$name.filter", $data);
    $pclib->app->setsession("search-$name.values", $data);
  }

  static function disableFilter($grid)
  {
  	global $pclib;

  	$name = $grid->name;
  	$pclib->app->deletesession("$name.sortarray");
  	$pclib->app->deletesession("$name.filter");
    $pclib->app->deletesession("search-$name.values");
  	PCGrid::invalidate($name);
  }

  protected static function prepareFilterData($data)
  {
    if (!$data) return;
    foreach ($data as $key => $value) {
        if (!$value) continue;
        if (startsWith($key, 'date_')) $data[$key] = Datex::toSqlDate($value);
        if (startsWith($key, 'date_from')) $data[$key] .= ' 00:00:00';
        if (startsWith($key, 'date_to')) $data[$key] .= ' 23:59:59';
    }

    return $data;
  }   	

 }

 ?>