<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\components\rest;

use yii\base\Model;

class Request extends Model
{
    /**
     * API username
     */
    public $username = null;

    /**
     * API password
     */
    public $password = null;

    /**
     * API request signature
     */
    public $signature = null;

    /**
     * API domain
     */
    public $domain = null;

    /**
     * API stability
     */
    public $stability = null;

    /**
     * API name
     */
    public $api = null;

    /**
     * API endpoint
     */
    public $endpoint = null;

    /**
     * Parsed request data
     */
    public $data = null;

    /**
     * Raw request data
     */
    public $rawData = null;


    /**
     * API request method
     */
    public $method = null;

    /**
     * API major version
     */
    public $versionMajor = null;

    /**
     * Api minor(revision) version
     */
    public $versionMinor = null;

    /**
     * Request timestamp
     */
    public $timestamp = null;


    /**
     * @inheritdoc
     */
    public function fields(){
        switch($this->scenario){
            case Rest::REQUEST_SCENARIO_PUBLIC:
                return [
                    'domain' => 'domain',
                    'stability' => 'stability',
                    'method' => 'method',
                    'versionMajor' => 'versionMajor',
                    'versionMinor' => 'versionMinor',
                    'data' => 'data',
                ];
        }
    }

    /**
     * Rules
     */
    public function rules()
    {
        return [
            [
                ['username','password','signature','domain','stability','api','endpoint','method','versionMajor','versionMinor','timestamp'], 'required','message' => "'{attribute}' is required"
            ],
            // Version major
            [
                ['versionMajor'],'integer', 'message' => 'API version is missing (versionMajor)'
            ],
            // Username
            [
                ['username'],'email','message' => 'invalid username'
            ],
        ];
    }

}
