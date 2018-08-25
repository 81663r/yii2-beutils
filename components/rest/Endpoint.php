<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\components\rest;


abstract class Endpoint
{
    /**
     * Holds api request
     */
    protected $request = null;

    abstract protected function get();

    abstract protected function post();

    abstract protected function put();

    abstract protected function delete();

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

    final public function setRequest($request){
        $this->request = $request;
    }
}
