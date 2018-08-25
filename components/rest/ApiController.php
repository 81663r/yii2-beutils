<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\components\rest;

use yii\base\Controller;

/**
 * This class handles API controller functionality.
 *
 * This class abstracts all functionality required for API controllers.
 * All controllers that conform an API must extend this class.
 *
 * @package yii\beutils\components\rest
 */
abstract class ApiController extends Controller
{
    /**
     * The API request object for easier access.
     */
    private $request = null;

    /**
     * The API endpoint object.
     */
    protected $endpoint = null;

    /**
     * This method resolves where the endpoints reside.
     */
    abstract protected function getEndpointNamespace();

	/**
	 * @inheritdoc
	 */
	final public function beforeAction($action){

        $this->request = \Yii::$app->rest->getRequest();

        $this->resolveEndpoint($action);

        \Yii::$app->rest->authenticateApiRequest();

        \Yii::$app->rest->authorizeApiRequest();

	    return parent::beforeAction($action);
	}


	/**
     * Resolve endpoint object.
     *
     * This method will look in the endpoint namespace for a class with the following format;
     * Endpoint<Controller Action>V<version>.php (i.e EndpointAcmeV1_0.php)
     * Where Acme conforms to the name of the action in the controller
     * that extends ApiController class
     *
     */
	private function resolveEndpoint($action){

        // Get path
        $path = $this->getEndpointNamespace();

        // Build class name and attempt to create endpoint object
        $class = $path."\\Endpoint".ucfirst($action->id)."V".$this->request->versionMajor."_".$this->request->versionMinor;
        try{

            // Create endpoint object
            $this->endpoint = new $class();
        }
        catch(\Exception $e){
            \Yii::$app->rest->NOT_FOUND('unable to find endpoint -> '.$e->getMessage());
        }

        // Set endpoint's request body data
        $this->endpoint->setRequest($this->request);
    }
}
