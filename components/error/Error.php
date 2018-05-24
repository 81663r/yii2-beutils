<?php
namespace yii\beutils\components\error;

use yii\base\Component;
use yii\base\Model;

class Error extends Component
{
	/**
	 * Dynamic error function delimiter
	 */
	const DEF_DELIMITER = '_';

	/**
	 * Language country delimiter
	 */
	const LANG_DELIMITER = '-';

	/**
	 * This property contains a list of errors
	 */
	public $errors;

	/**
	 * Error id
	 */
	private $id = null;

	/**
	 * Action that failed
	 */
	private $action = null;

	/**
	 * Entity that performed the action
	 */
	private $entity = null;

	/**
	 * Object on which the action was performed
	 */
	private $object = null;

	/**
	 * System message
	 */
	private $systemMessage = null;

	/**
	 * User message
	 */
	private $userMessage = null;

	/**
	 * Previous error object
	 */
	private $previous = null;
	
	
	/**
	 * This method allows for dynamic error function calling
	 * using action, entity and object
	 *
	 * Format:
	 *	action_entity_object
	 */
	public function __call($name, $arguments){

		// Parse function into sections
		list($action, $entity, $object) = explode(self::DEF_DELIMITER, $name);

		// Get langguage
		list($language, $country) = explode(self::LANG_DELIMITER, \Yii::$app->language);
		
		// Iterate to search over errors
		foreach($this->errors as $index => $error){
			
			if (($error['action'] == $action) && ($error['entity'] == $entity) && ($error['object'] == $object)){
				
				$this->id = $error['id'];
				$this->action = $error['action'];
				$this->entity = $error['entity'];
				$this->object = $error['object'];
				$this->systemMessage = $error['message'][$language]['system'];
				$this->userMessage = $error['message'][$language]['user'];
			}
		}

		// Replace tokens with token value in the messages
		// This is an expensive operation
		if (!empty($arguments)){
			if (array_key_exists('system', $arguments[0])){
				foreach($arguments[0]['system'] as $token => $value){
					$this->systemMessage = (str_replace($token, $value, $this->systemMessage));
				}
			}
			if (array_key_exists('user', $arguments[0])){
				foreach($arguments[0]['user'] as $token => $value){
					$this->userMessage = (str_replace($token, $value, $this->userMessage));
				}
			}
		}

		return $this;
	}


	/**
	 * Raise error exception
	 */
	public function raise(){
		
		throw new BackendException($this);
	}

	/**
	 * id getter
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Action getter
	 */
	public function getAction(){
		return $this->action;
	}

	/**
	 * Entity getter
	 */
	public function getEntity(){
		return $this->entity;
	}

	/**
	 * Object getter
	 */
	public function getObject(){
		return $this->object;
	}

	/**
	 * System message getter
	 */
	public function getSystemMessage(){
		return $this->systemMessage;
	}

	/**
	 * User message getter
	 */
	public function getUserMessage(){
		return $this->userMessage;
	}

	/**
	 * Previous getter
	 */
	public function getPrevious(){
		return $this->previous;
	}
}
