<?php

/**
 * file:RedClientScript.php
 * author:Toruneko@outlook.com
 * date:2014-7-19
 * desc:RedClientScript
 */
class RedClientScript extends CClientScript
{
    public $coreScriptPosition = self::POS_END;
    public $defaultScriptFilePosition = self::POS_END;
    public $defaultScriptPosition = self::POS_END;

    /*
     * @see CApplicationComponent::init()
     */
    public function init()
    {
        parent::init();

        $userPackages = Yii::getPathOfAlias('application.config.packages') . '.php';
        if (file_exists($userPackages)) {
            $userPackages = require_once($userPackages);
        } else {
            $userPackages = array();
        }
        if ($this->corePackages === null) {
            $this->corePackages = require(YII_PATH . '/web/js/packages.php');
        }

        $this->corePackages = array_merge($this->corePackages, $userPackages);
    }
}