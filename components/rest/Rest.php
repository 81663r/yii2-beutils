<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\components\rest;

use yii\base\Component;
use yii\web\HttpException;


/**
 * Class Rest
 * @package yii\beutils\components\rest
 */
class Rest extends Component
{
    /**
     * Api auth signature custom header
     */
    const HEADER_AUTH_API_SIGNATURE= 'Auth-Api-Signature';

    /**
     * Api domain http header
     */
    const HEADER_AUTH_API_DOMAIN = 'Auth-Api-Domain';

    /**
     * Request scenario PUBLIC
     */
    const REQUEST_SCENARIO_PUBLIC = 'public';

    /**
     * Valid request timeout (sec)
     */
    const REQUEST_VALID_TIMEOUT = 84000;

	/**
	 * Database connection handle
	 * where API tables exist
	 */
	public $db;

    /**
     * Holds api manager object
     */
    public $manager = null;

    /**
     * Holds api request model
     */
    private $request = null;

	/**
     * Error codes
     */
	private $errorCodes = [
	    'BAD_REQUEST' => 400,
        'FORBIDDEN' => 403,
        'NOT_FOUND' => 404,
        'UNAUTHORIZED' => 401,
        'METHOD_FAILURE' => 424,
        'UNSUPPORTED_MEDIA_TYPE' => 415,
        'TOO_MANY_REQUEST' => 429,
        'PAYMENT_REQUIRED' => 402,
        'NOT_ACCEPTABLE' => 406,
        'PRECONDITION_FAILED' => 412,
        'EXPECTATION_FAILED' => 417,
        'INTERNAL_SERVER_ERROR' => 500,
        'NOT_IMPLEMENTED' => 501,
        'SERVICE_UNAVAILABLE' => 503,
        'GONE' => 410,
        'CONFLICT' => 409,
        'METHOD_NOT_ALLOWED' => 405
    ];

    /**
     * Error structure
     */
    private $error = [
        'error_code' => null,
        'error' => null,
    ];


    public function init(){

        // Run sanity check
        $this->sanityCheck();

        // Set manager object
        $this->manager = new Manager($this->db);

        // Set request object
        $this->request = new Request();

        // Accept http request
        $this->acceptRequest();

        // Validate http request
        $this->validateRequest();
    }



    /**
     * Dynamic rest methods
     */
    public function __call($name, $arguments){

        // Function name
        $fname = strtoupper($name);

        if (array_key_exists($fname, $this->errorCodes)){

            \Yii::$app->response->{'on beforeSend'} = function($event) use ($fname, $arguments) {

                // Set error structure
                $this->error['error_code'] = $this->errorCodes[$fname];

                if (isset($arguments[0]) && is_array($arguments[0])){

                    $this->error['error'] = $arguments[0];

                    $event->sender->data = $this->error;
                }
            };

            throw new HttpException($this->errorCodes[$fname]);
        }
    }


    /**
     * Authenticate api request
     */
    public function authenticateApiRequest(){
        $this->manager->authenticateApiRequest($this->request->domain, $this->request->username, $this->request->password);
    }


    /**
     * Authorize api request
     */
    public function authorizeApiRequest(){
        $this->manager->authorizeApiRequest($this->request);
    }

    /**
     * Get accepted http request
     */
    public function getRequest(){

        // Set scenario for request model
        $this->request->scenario = self::REQUEST_SCENARIO_PUBLIC;

        // Return data
        return ((Object)$this->request->toArray());
    }


    /**
     * Accept request
     * This method accepts the request in a passive manner
     * No validation is performed here
     */
    private function acceptRequest(){

        // Get basic auth username & password
        $this->request->username = \Yii::$app->request->getAuthUser();
        $this->request->password = \Yii::$app->request->getAuthPassword();

        // Get headers
        $headers = \Yii::$app->request->getHeaders()->toArray();

        // Set api key
        if (array_key_exists(strtolower(self::HEADER_AUTH_API_SIGNATURE), $headers))
            $this->request->signature= $headers[strtolower(self::HEADER_AUTH_API_SIGNATURE)][0];

        // Set api domain
        if (array_key_exists(strtolower(self::HEADER_AUTH_API_DOMAIN), $headers))
            $this->request->domain = $headers[strtolower(self::HEADER_AUTH_API_DOMAIN)][0];

        // Set method
        $this->request->method = \Yii::$app->request->getMethod();

        // Set accept header fields
        if (array_key_exists('accept', $headers)){
            $acceptFields = array_values(\Yii::$app->request->getAcceptableContentTypes());

            if (isset($acceptFields[0]['version'])){
                list($this->request->versionMajor, $this->request->versionMinor) = explode('.', $acceptFields[0]['version']);
            }

            if (isset($acceptFields[0]['stability']))
                $this->request->stability = $acceptFields[0]['stability'];

            if (isset($acceptFields[0]['timestamp']))
                $this->request->timestamp = $acceptFields[0]['timestamp'];
        }

        // Resolve execution route
        $route = \Yii::$app->request->resolve();

        // Parse route into its components
        $routeItems = explode('/', $route[0]);

        // Get endpoint
        $this->request->endpoint = $routeItems[count($routeItems)-1];

        // Get api
        $this->request->api = $routeItems[count($routeItems)-2];

        // Get raw and parsed data
        if (\Yii::$app->request->getIsGet()){
            $this->request->data = \Yii::$app->request->getQueryParams();
            $this->request->rawData = \Yii::$app->request->getQueryString();
        }
        else{
            if (\Yii::$app->request->getIsPost() || \Yii::$app->request->getIsPut() || \Yii::$app->request->getIsDelete()){
                $this->request->data = \Yii::$app->request->getBodyParams();
                $this->request->rawData = \Yii::$app->request->getRawBody();
            }
        }
    }


    /**
     * Validate request
     */
    private function validateRequest(){
            if (!$this->request->validate()){

                $this->BAD_REQUEST(\Yii::$app->error->action_entity_test(['hello' => 'world']));
                exit;
            }
    }


    /**
     * Sanity check.
     * This method will ensure that the required components
     * have been loaded within the application.
     */
    private function sanityCheck(){

        // Make sure there are parsers set
        if (empty(\Yii::$app->request->parsers)){
            $this->SERVICE_UNAVAILABLE(['message' => 'no parsers were configured']);
        }

        // Make sure there are formatters set
        if (empty(\Yii::$app->response->formatters)){
            $this->SERVICE_UNAVAILABLE(['message' => 'no formatters were configure']);
        }
    }
}
