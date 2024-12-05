<?php
/*
	Excel+cestina friendly CSV RFC 4180 export/import.
	(Konverze z/do pole vhodneho k vlozeni do databaze.)
*/

class CsvFile
{
	protected $data;
	protected $columns = [];
	protected $options = [];

	function __construct($options = [])
	{
		$this->setOptions($options);
	}

	function load($filePath)
	{
		$this->data = $this->removeBom(file_get_contents($filePath));

		if ($this->options['charset']) {
		   $this->data = iconv($this->options['charset'], 'utf-8', $this->data);
		}
	}

	function save($filePath)
	{
		$str = $this->data;

		if ($this->options['charset']) {
		   $str = iconv('utf-8', $this->options['charset'], $str);
		}

		file_put_contents($filePath, $str);
	}

	function setOptions($options)
	{
		$defaults = [
			'charset' => '',
			'separator' => ';',
			'enclosure' => '"',
			'lineBreak' => "\n",
			'columnsRename' => [],
			'trim' => false,
		];

		$this->options = $options + $defaults;
	}

	function getColumns()
	{
		$this->getValues(1);
		return $this->columns;
	}

	function toArray()
	{
		return $this->getValues();
	}

	function first($n)
	{
		return $this->getValues($n);
	}

	protected function getValues($n = 0)
	{
		$output = [];
		$str = $this->data;

		$rowCount = 0; 
		$firstRow = true;

    while ($str)
    {
    	$row = $this->decodeRow($str);

      if ($firstRow) {
      	$firstRow = false;
      	$this->columns = $row;

      	foreach ($this->columns as $i => $col) {
      		if (!$col) $this->columns[$i] = "Column$i";
      	}

      	continue;
      }

      $output[] = $this->getRow($row);

 	    if ($n and ++$rowCount >= $n) {
    		break;
    	}
    }

		return $output;
	}

	function fromArray($rows)
	{
		$this->columns = array_keys($rows[0]);

		$rename = $this->options['columnsRename'];
		foreach ($this->columns as $i => $col) {
			if (isset($rename[$col])) $this->columns[$i] = $rename[$col];
		}

		$this->data = $this->encodeRow($this->columns);

		foreach ($rows as $row) {
			$this->data .=  $this->encodeRow($row);
		}

		return $this->data;
	}

	function fromString($s)
	{
		$this->data = $this->removeBom($s);

		if ($this->options['charset']) {
		   $this->data = iconv($this->options['charset'], 'utf-8', $this->data);
		}
	}

	function toString()
	{
		return $this->data;
	}

	function export($fileName)
	{
  	print header('Content-type: text/csv;charset=utf-8');
  	// //print header("Content-Disposition: inline; filename=\"$fileName\"");
  	print header("Content-Disposition: attachment; filename=\"$fileName\"");
  	die($this->toString());
	}

	function exportExcel($fileName)
	{
	  header("Content-Type: application/vnd.ms-excel;charset=utf-8");
  	header("Content-Disposition: attachment; filename=\"$fileName\"");
	  echo pack("CCC",0xef,0xbb,0xbf);
  	die($this->toString());
	}

	protected function getRow($row)
	{
		$data = [];
		
		$rename = $this->options['columnsRename'];

		foreach ( $this->columns as $i => $col) {
			if (isset($rename[$col])) $col = $rename[$col];
			if (!$col) continue; //hack
			$data[$col] = $row[$i] ?? null;
		}

		return $data;
	}

	protected function encodeRow($row)
	{
		$CSV_SEPARATOR = $this->options['separator'];
		$CSV_ENCLOSURE = $this->options['enclosure'];
		$CSV_LINEBREAK = $this->options['lineBreak'];

		$trans = ($CSV_ENCLOSURE == '"')? ['"' => '""'] : [$CSV_ENCLOSURE => "\\".$CSV_ENCLOSURE];

		$output = [];
    foreach($row as $s) 
    {
      $s = strval($s);
      if(strcspn($s, $CSV_SEPARATOR.$CSV_ENCLOSURE.$CSV_LINEBREAK) < strlen($s)) {
      	$s = $CSV_ENCLOSURE.strtr($s, $trans).$CSV_ENCLOSURE;
      }

      $output[] = $s;
    }

    return implode($CSV_SEPARATOR, $output) . $CSV_LINEBREAK;
	}

	protected function removeBom($text)
	{
	    $bom = pack("CCC",0xef,0xbb,0xbf);//pack('H*','EFBBBF');
	    $text = preg_replace("/^$bom/", '', $text);
	    return $text;
	}

	/* https://www.php.net/manual/en/function.fgetcsv.php#98800 */
	protected function decodeRow(&$string)
	{
		$CSV_SEPARATOR = $this->options['separator'];
		$CSV_ENCLOSURE = $this->options['enclosure'];
		$CSV_LINEBREAK = $this->options['lineBreak'];

	  $o = array();

	  $cnt = strlen($string);
	  $esc = false;
	  $escesc = false;
	  $num = 0;
	  $i = 0;
	  while ($i < $cnt) {
	    $s = $string[$i];

	    if ($s == $CSV_LINEBREAK) {
	      if ($esc) {
	        $o[$num] .= $s;
	      } else {
	        $i++;
	        break;
	      }
	    } elseif ($s == $CSV_SEPARATOR) {
	      if ($esc) {
	        $o[$num] .= $s;
	      } else {
	        $num++;
	        $o[$num] = '';
	        $esc = false;
	        $escesc = false;
	      }
	    } elseif ($s == $CSV_ENCLOSURE) {
	      if ($escesc) {
	        $o[$num] .= $CSV_ENCLOSURE;
	        $escesc = false;
	      }

	      if ($esc) {
	        $esc = false;
	        $escesc = true;
	      } else {
	        $esc = true;
	        $escesc = false;
	      }
	    } else {
	      if ($escesc) {
	        $o[$num] .= $CSV_ENCLOSURE;
	        $escesc = false;
	      }

	      if (!isset($o[$num])) $o[$num] = '';
	      $o[$num] .= $s;
	    }

	    $i++;
	  }

	  $string = substr($string, $i);

	  if ($this->options['trim']) {
	  	$o = array_map('trim', $o);
	  }

	  $o[$num] = rtrim($o[$num]); //hack: remove \n v poslednim poli

	  return $o;
	} 

}


 ?>