<?php

namespace yii\beutils\modules\apimgr\controllers;

use yii\web\Controller;

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
        return $this->render('index');
    }

    public function actionTestone(){
        echo "HELLO FROM MODULE";
    }
}
