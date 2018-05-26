<?php
namespace yii\beutils\components\error;

use yii\base\Component;
use yii\base\Model;
use yii\db\Query;

class Error extends Component
{
	/**
	 * Dynamic error function delimiter
	 */
	const DEF_DELIMITER = '_';

    /**
     * Database handle
     * Where 'error' table exists
     */
    public $db = null;

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
     * Error data
     */
    private $data = null;


	/**
	 * This method allows for dynamic error function calling
	 * using action, entity and object
	 *
	 * Format:
	 *	action_entity_object
	 */
	public function __call($name, $arguments){

	    $query = new Query();



		// Parse function into sections
		list($action, $entity, $object) = explode(self::DEF_DELIMITER, $name);

		// Get error from db
        $query->select('*')
            ->from('error')
            ->where('action=:a AND entity=:e AND object=:o',[':a' => $action, ':e' => $entity, ':o' => $object]);

        $error = $query->createCommand()->queryOne();

        $this->id = $error == false ? -1 : $error['id'];
        $this->action = $error == false ? "unknown" : $error['action'];
        $this->entity = $error == false ? "unknown" : $error['entity'];
        $this->object = $error == false ? "unknown" : $error['object'];
        $this->systemMessage = $error == false ? "not available" : $error['system_message'];
        $this->userMessage = $error == false ? "not available" : $error['user_message'];

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
     * Return error as array
     */
    public function toArray(){
        return [
            'id' => $this->getId(),
            'action' => $this->getAction(),
            'entity' => $this->getEntity(),
            'object' => $this->getObject(),
            'user_message' => $this->getUserMessage(),
            'system_message' => $this->getSystemMessage(),
            'data' => $this->getData()
        ];
    }


    /**
     * Add error data if available
     */
    public function withData(array $data){
        $this->data = $data;
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

	/**
     * Data getter
     */
	public function getData(){
	    return $this->data;
    }
}
