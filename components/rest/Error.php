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
 * This models is for API error handling.
 *
 * @package yii\beutils\components\rest
 */
class Error extends Model
{
    /**
     * Message describing the error that has ocurred
     */
    public $err_msg = null;

    /**
     * A code that indicates which error has happened
     */
    public $err_code = null;

    /**
     * Contains additional error data
     */
    public $err_data = null;

    /**
     * The HTTP code that was used
     */
    public $http_code = null;

    /**
     * Previous error object
     */
    public $prev = null;

    /**
     * Validation rules for model
     */
    public function rules(){
        return [
            [
                ['err_msg','err_code','http_code'], 'required','message' => "'{attribute}' is required"
            ],
        ];
    }
}