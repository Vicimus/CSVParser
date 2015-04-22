<?php

namespace Vicimus\CSVParser\Controllers;

use Vicimus\CSVParser\CSVParser;

class MainController extends \BaseController
{
	public function start()
	{
		//Process the file
		$name = time().'.csv';
		$file = public_path().'/uploads/csv/'.$name;
		\Input::file('csv')->move(public_path().'/uploads/csv', $name);

		$schema = \Input::get('schema');
		$headers = \Input::get('headers');

		//Create a CSVParser object and populate it with the appropriate data
		$parser = new CSVParser($file, $schema, $headers, true);

		//Return a json encoded version of the parser for the js interface to render
		return json_encode($parser);
	}

	public function process()
	{
		$csv = \Input::get('csv');
		$headers = \Input::get('headers');

		$map = json_decode(\Input::get('map'));
		
		$mapping = array();
		foreach($map as $relation)
		{
			if(property_exists($relation, 'db'))
				$mapping[$relation->csv] = $relation->db;
			else
				$mapping[$relation->csv] = null;
		}

		$schema = \Input::get('schema');
	
		if(!\Schema::hasTable($schema))
			throw new \Exception('Invalid Schema: '.$schema);

		$parser = new CSVParser($csv, $schema, $headers);
		$parser->parse();

		$inserts = array();
		foreach($parser->getLines() as $line)
		{
			$insert = array();
			foreach($line as $column => $data)
			{
				if(!array_key_exists($column, $mapping))
					continue; 

				$index = $mapping[$column];
				if(!$index)
					continue;

				$insert[$parser->getColumn($mapping[$column])] = $data;
			}
			$inserts[] = $insert;
		}

		return json_encode($inserts);
	}

	public function finish()
	{
		dd(\Input::all());
	}
}