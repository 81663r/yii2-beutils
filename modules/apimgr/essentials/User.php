<?php
namespace yii\beutils\modules\apimgr\essentials;

use yii\beutils\components\rest\Manager;
use yii\base\Model;

class User{

    /**
     * Holds api manager object
     */
    private $manager = null;

    /**
     * Uses user model
     */
    private $umodel = null;

    public function __construct(){

        // Create manager object
        $this->manager = new Manager();
    }


    /**
     * Create new api user
     * Assumes user model has been validated
     */
    public function newUser(Model $umodel ){
        // Create database model
        
    }
}
