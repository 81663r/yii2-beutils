<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\components\rest;

use yii\base\Model;

/**
 * This model handles the API request.
 *
 * @package yii\beutils\components\rest
 */
class Request extends Model
{
    /**
     * Basic HTTP authentication username
     */
    public $username = null;

    /**
     * Basic HTTP authentication password
     */
    public $password = null;

    /**
     * Request signature
     */
    public $signature = null;

    /**
     * API domain
     */
    public $domain = null;

    /**
     * Stability (dev | prod | test)
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
     * API request method (GET | POST | PUT | DELETE)
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
                ['username','password'], 'required','message' => '{attribute} is required for basic HTTP authentication'
            ],
            [
                ['domain'], 'required', 'message' => 'Auth-Api-Domain http header is required'
            ],
            [
                ['signature'], 'required', 'message' => 'Auth-Api-Signature http header is required'
            ],
            [
                ['stability','versionMajor','versionMinor','timestamp'],'required','message'=>'Accept header is invalid {attribute} is required'
            ],
            [
                ['api','endpoint','method'],'required','message' => "{attribute} is required"
            ]
        ];
    }
}
