<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\services;


/**
 *
 * 
 * 
 *
 * @package yii\beutils\services
 */
abstract class Service extends Configuration
{
	/**
	 * Service io directory name within service namespace
	 */
	const SERVICE_IO_DIR = 'io';

	/**
	 * Service type directory within service namespace
	 */
	const SERVICE_TYPE_DIR = 'type';

	/**
	 * Service branch directory within service namespace
	 */
	const SERVICE_BRANCH_DIR = 'branch';

	/**
	 * Input object
	 */
	private $input = null;

	/**
	 * Output object
	 */
	private $output = null;

	/**
	 * Configuration object
	 */
	private $conf = null;

	/**
	 * Service namespace
	 */
	private $service_namespace = null;

	abstract public function apply();

	/**
	 * Constructor
	 */
	public function __construct($provider, $type, $branch){

		// Set class attributes
		$this->provider = strtolower($provider);
		$this->type = strtolower($type);
		$this->branch = strtolower($branch);

		// Get configuration 
		$this->conf = $this->getConf($this->provider, $this->type, $this->branch);

		// Get defined services namespace
		$this->service_namespace = \Yii::$app->params['service_namespace'];
	}

	final public function run(){
		return $this->apply();
	}

	final public function input(array $input = null){
		
		// Resolve service input if not already set
		if ($this->input == null && !$this->resolveInput()){
			return null;
		}

		// Set input values if specified
		if ($input != null){
			$this->input->attributes = $input;
		}

		return $this->input;
	}

	final public function output(){
		return $this->output;
	}


	final protected function conf(array $query = null){

		// Holds key value pairs
		$conf = [];

		// Holds key value pair (filter)
		$conf_filter = [];

		foreach($this->conf as $index => $record){
			$conf[$record['key']] = $record['value'];
		}

		// Filter throught query
		if ($query != null){
			foreach($query as $key){
				if (array_key_exists($key, $conf))
					$conf_filter[$key] = $conf[$key];

			}
			return (count($conf_filter)==1 ? $conf_filter[$key] : $conf_filter);
		}

		return $conf;
	}

	private function resolveInput(){
		return $this->resolveIO('input');
	}

	private function resolveOutput(){
		return $this->resolveIO('output');
	}

	private function resolveIO($io_type){

		// Create class path
		$class = $this->service_namespace.'\\'.self::SERVICE_IO_DIR.'\\'.ucfirst($this->type).ucfirst($this->branch).ucfirst($this->provider).ucfirst(strtolower($io_type));

		// Make sure class exists
		if (!class_exists($class)){
			return false;
		}

		// Create object from input class for service
		$this->$io_type = new $class();

		return true;
	}
}