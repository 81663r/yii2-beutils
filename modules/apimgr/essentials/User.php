<?php
namespace yii\beutils\modules\apimgr\essentials;

use yii\beutils\components\rest\Manager;

class User{

    /**
     * Holds api manager object
     */
    private $manager = null;

    /**
     * Uses user model
     */
    private $umodel = null;

    public function __construct(\UserModel $umodel){

        // Create manager object
        $this->manager = new Manager();

        // Set user model object
        $this->umodel = $umodel;
    }


    /**
     * Create new api user
     */
    public function newUser(){
    }
}
