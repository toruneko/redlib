<?php

/**
 * file:RedWebApplication.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc: RedWebApplication
 */
class RedWebApplication extends CWebApplication
{
    public $defaultController = 'index';

    public function init()
    {
        parent::init();

        Yii::setPathOfAlias('red', RED_PATH);
        Yii::setPathOfAlias('app', $this->getBasePath());
        Yii::setPathOfAlias('root', dirname($_SERVER['SCRIPT_FILENAME']));
    }

    protected function registerCoreComponents()
    {
        parent::registerCoreComponents();

        $components = array(
            'user' => array(
                'class' => 'RedWebUser',
            ),
            'authManager' => array(
                'class' => 'RedDbAuthManager',
            ),
            'request' => array(
                'class' => 'RedHttpRequest',
            ),
            'clientScript' => array(
                'class' => 'RedClientScript',
            ),
            'securityManager' => array(
                'class' => 'RedSecurityManager',
            ),
        );

        $this->setComponents($components);
    }
}