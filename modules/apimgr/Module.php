<?php
namespace yii\beutils\modules\apimgr;

use yii\beutils\modules\apimgr\essentials\User;
/**
 * apimgr module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'yii\beutils\modules\apimgr\controllers';

    /**
     * User object
     */
    private $user = null;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
}
