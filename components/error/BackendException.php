<?php
namespace yii\beutils\components\error;


class BackendException extends \Exception
{
	/**
	 * Holds error object
	 */
	private $error = null;


	/**
	 * Constructor
	 */
	public function __construct(Error $error){
		
		// Set error object
		$this->error = $error;

		// Set parent exception's fields
		parent::__construct($error->getUserMessage(), $error->getId(), $error->getPrevious());
	}

	/**
	 * Gets error object
	 */
	public function getError(){
		return $this->error;
	}
}
