<?php
namespace yii\beutils\components\rest;

use yii\base\Model as BaseModel;

abstract class Model extends BaseModel
{
    /**
     * Convert error arrays to string
     */
    final protected function errToStr(){

        // String buffer
        $str = "";

        // Iterate over errors
        foreach($this->getErrors() as $index => $error){

        }

    }

}
