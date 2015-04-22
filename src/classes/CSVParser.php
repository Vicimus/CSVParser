<?php

namespace Vicimus\CSVParser;

class CSVParser 
{

	public $columns;
	public $line;
	public $file;
	public $headers;

	public $lines = array();
	public $hasHeaders = false;
	protected $map;	

	public function __construct($csv, $schema, $hasHeaders = false, $preview = false)
	{
		$this->file = $csv;
		$this->schema = $schema;
		$this->hasHeaders = $hasHeaders;
		$this->columns = \Schema::getColumnListing($schema);

		if($preview)
		{
			$this->lines[] = $this->getLine($csv, $hasHeaders);
			$this->lines[] = $this->getLine($csv, $hasHeaders, 1);
		}
		
	}

	public function setMap($map)
	{
		$this->map = $map;
	}


	public function getLines()
	{
		return $this->lines;
	}

	public function parse()
	{
		$handle = fopen($this->file, 'r');

		if($this->hasHeaders)
			$this->headers = fgetcsv($handle);

		while(($line = fgetcsv($handle)) !== false)
		{
			$this->lines[] = $line;
		}

		fclose($handle);
	}

	public function getColumn($index)
	{
		return $this->columns[$index];
	}

	protected function getLine($csv, $hasHeaders, $start = 0)
	{
		$handle = fopen($csv, 'r');
		if($hasHeaders)
			$this->headers = fgetcsv($handle);
		
		$lines = array();
		for($x = 0; $x <= $start; $x++)
			$line = fgetcsv($handle);

		while($this->hasEmptyValues($line) && !feof($handle))
		{
			$newLine = fgetcsv($handle);
			$this->fillEmptyValues($line, $newLine);
		}

		//$this->removeEmptyValues($line);
		fclose($handle);
		return $line;
	}

	public function getColumns()
	{
		return $this->lines[0];
	}

	protected function removeEmptyValues(array &$line)
	{
		foreach($line as $index => $value)
			if(!$value)
				array_splice($line, $index, 1);
	}

	protected function fillEmptyValues(array &$line, $newLine)
	{
		foreach($line as $index => $value)
			if(!$value)
				$line[$index] = $newLine[$index];
	}

	protected function hasEmptyValues(array $line)
	{
		foreach($line as $index => $value)
			if(!$value)
				return true;
		return false;
	}
}