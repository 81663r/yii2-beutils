<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\components\rest;


/**
 * This class handles API endpoint functionality.
 *
 * This class abstracts all functionality required for API endpoints.
 * All endpoints that conform an API must extend this class.
 *
 * @package yii\beutils\components\rest
 */
abstract class Endpoint
{
    /**
     * Holds api request object for easier access
     */
    protected $request = null;

    abstract protected function get();

    abstract protected function post();

    abstract protected function put();

    abstract protected function delete();


    /**
     * This methods exposes the HTTP CRUD to the end user.
     *
     * @return mixed
     */
    final public function consume(){

        switch(($this->request->method)) {
            case 'GET':
                return ($this->get());
            case 'POST':
                return ($this->post());
            case 'PUT':
                return ($this->put());
            case 'DELETE':
                return ($this->delete());
            default:
        }
    }


    /**
     * Setter for API request.
     *
     * @param $request API request
     */
    final public function setRequest($request){
        $this->request = $request;
    }
}
