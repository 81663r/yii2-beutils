<?php
namespace yii\beutils\modules\apimgr\controllers;

use yii\web\Controller;
use yii\beutils\modules\apimgr\essentials\User;
use yii\beutils\modules\apimgr\models\UserModel;

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

        if (\Yii::$app->request->isGet)
            return $this->render('index');
        else{
        }
    }

    public function actionNewuser(){
        
        // Create user object
        $user = new User();

        // Get post request
        print_r(\Yii::$app->request->post());
    }
}
