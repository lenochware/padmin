<?php 

class SearchForm extends pclib\Form {

	protected $grid;
	protected $searchFields;

	function __construct($grid, array $fields)
	{		
		parent::__construct('tpl/search.tpl', 'search-'.$grid->name);

		$this->grid = $grid;
		$this->searchFields = $fields;

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
				if (!empty($el[$name]['date'])) {
					$el[$name]['html']['class'] = 'calendar';
				}
			}

			$el[$name] += pclib\system\ElementsDef::getElement($name, $el[$name]['type']);
			$el[$name]['skip'] = $el[$name]['block'] = null;
		}

		if ($this->isFiltered($grid)) {
			$this->values['FOUND'] = paramStr('<div class="message">Nalezeno {0} položek.</div>', [$grid->length]);
		}

		$this->elements += $el;

		if ($this->submitted) {
			if (isset($_POST['search'])) $this->enableFilter();
			if (isset($_POST['showall'])) $this->disableFilter();
			$this->reload();
		}
	}

	function isFiltered($grid)
	{
		foreach ($this->searchFields as $name) {
			if (!empty($grid->filter[$name])) return true;
		}

		return false;
	}

  function enableFilter()
  {
  	global $pclib;

    $name = $this->grid->name;
    $data = self::prepareFilterData($_POST? $_POST['data'] : $_GET);

    $pclib->app->setSession("$name.filter", $data);
    $pclib->app->setSession("search-$name.values", $data);
  }

  function disableFilter()
  {
  	global $pclib;

  	$name = $this->grid->name;
  	$pclib->app->deleteSession($name);
  	//$pclib->app->deleteSession("$name.filter");
    $pclib->app->deleteSession("search-$name.values");
  	PCGrid::invalidate($name);
  }

  protected function prepareFilterData($data)
  {
    if (!$data) return;

    //form->preparedValues()?

    // foreach ($data as $key => $value) {
    //     if (!$value) continue;
    //     $elem = $this->grid->elements[$key];

    //     if (isset($elem['date'])) {
    //     	$date = $this->toSqlDate($value, $elem['date']);
    //     	$data[$key] = substr($date, 0, 10); //hack
    //     }
    // }

    return $data;
  }

  /**
	 * Reload stranky.
	 */
	function reload() {
	  $this->app->router->reload();
	}

 }

 ?>