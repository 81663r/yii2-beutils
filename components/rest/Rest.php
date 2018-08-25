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
 * This class handles all REST capabilities.
 *
 * This class exposes all functionality that is availble to the
 * end user through the REST component.
 *
 * @package yii\beutils\components\rest
 */
class Rest extends Component
{
    /**
     * HTTP custom header for API request signature
     */
    const HEADER_AUTH_API_SIGNATURE= 'Auth-Api-Signature';

    /**
     * HTTP custom header for API request domain
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
	 * Database connection handle for API tables
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
     * HTTP error codes
     */
	static public $error_codes = [
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
     * Initializes REST component.
     *
     * This method performs a sanity check, creates
     * required request objects and accepts and validates the request.
     */
    public function init(){

        $this->sanityCheck();

        $this->manager = new Manager($this->db);

        $this->request = new Request();

        $this->acceptRequest();

        $this->validateRequest();
    }


    /**
     * Magic method used to handle dynamic error methods.
     *
     * This method uses $name to look into $error_codes to see if an HTTP error code
     * is being called. If so it will inject a reply error and throw an exception pertaining
     * to said http error.
     *
     * @param string $name Called method name
     * @param array $arguments Called method passed arguments
     * @return mixed|void
     * @throws HttpException
     */
    public function __call($name, $arguments){

        // Function name
        $fname = strtoupper($name);

        // If we find that $fname is any of $error_codes then inject an error reply and throw an exception
        if (array_key_exists($fname, Rest::$error_codes)) {

            \Yii::$app->response->{'on beforeSend'} = function ($event) use ($fname, $arguments) {

                // Create error model
                $error_model = new Error();

                // Set error model
                $error_model->err_msg= is_string($arguments[0]) ? $arguments[0] : "unexpected error";
                $error_model->err_code = is_int($argumnets[1]) ? $arguments[1] : -1;
                $error_model->err_data = isset($arguments[2]) && is_array($arguments[2]) ? $arguments[2] : null;
                $error_model->http_code = Rest::$error_codes[$fname];
                $error_model->prev = isset($arguments[3]) && $arguments[3] instanceof Error ? $arguments[3] : null;

                // Set reply model
                $event->sender->data = $error_model;
            };

            throw new HttpException(Rest::$error_codes[$fname]);
        }
    }


    /**
     * Wrapper method that calls api authentication method from manager object
     */
    public function authenticateApiRequest(){
        $this->manager->authenticateApiRequest($this->request->domain, $this->request->username, $this->request->password);
    }


    /**
     * Wrapper method that calls api authorization method from manager object
     */
    public function authorizeApiRequest(){
        $this->manager->authorizeApiRequest($this->request);
    }


    /**
     * Getter method to API request.
     *
     * @return object API request fields
     */
    public function getRequest(){

        $this->request->scenario = self::REQUEST_SCENARIO_PUBLIC;

        // Return API request fields as an object
        return ((Object)$this->request->toArray());
    }


    /**
     * Accept API request.
     *
     * This method accepts the request in a passive manner. No validation is performed.
     * The method will get basic auth credentials if available, headers and do some processing
     * on the 'Accept' header field to obtain required API entities.
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
     * This method validates API request.
     */
    private function validateRequest(){
            if (!$this->request->validate()){
                $this->BAD_REQUEST('unable to validate request', Rest::$error_codes['BAD_REQUEST'], $this->request->getErrors());
            }
    }


    /**
     * This method will ensure that the required components have been loaded within the application.
     */
    private function sanityCheck(){

        // Make sure there are parsers set
        if (empty(\Yii::$app->request->parsers)){
            $this->SERVICE_UNAVAILABLE('there are no request parsers configured. check config file');
        }

        // Make sure there are formatters set
        if (empty(\Yii::$app->response->formatters)){
            $this->SERVICE_UNAVAILABLE('there are no response formats configure. check config file');
        }
    }
}
