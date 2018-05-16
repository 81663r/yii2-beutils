<?php
/**
 * @link
 * @copyright Copyright (c) 2018 Welogy LLC
 * @license 
 */

namespace yii\beutils;

use Yii;

/**
 * 
 *
 * @author 
 * @since 
 */
class Module extends \yii\base\Module 
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }


    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
    }


    /**
     * @inheritdoc
     * @since 2.0.7
     */
    protected function defaultVersion()
    {
        $packageInfo = Json::decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'composer.json'));
        $extensionName = $packageInfo['name'];
        if (isset(Yii::$app->extensions[$extensionName])) {
            return Yii::$app->extensions[$extensionName]['version'];
        }
        return parent::defaultVersion();
    }
}
