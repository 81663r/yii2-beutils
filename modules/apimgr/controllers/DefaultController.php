<?php

namespace yii\beutils\modules\apimgr\controllers;

use yii\web\Controller;
use yii\beutils\modules\apimgr\essentials\UserModel;

/**
 * Default controller for the `apimgr` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        // Create user model
        $umodel = new UserModel();

        // Controller module
        $module = \Yii::$app->controller->module;

        if (\Yii::$app->request->isGet)
            return $this->render('index');
        else{

            // Populate user model
            $umodel->attributes = $_POST['UserModel'];


        }
    }

    public function actionTestone(){
        echo "<b>HELLO FROM MODULE</b>";
    }


    public function actionAdduser(){


    }
}
