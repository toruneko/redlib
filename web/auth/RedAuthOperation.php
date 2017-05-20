<?php

/**
 * file:RedAuthOperation.php
 * author:Toruneko@outlook.com
 * date:2014-7-13
 * desc:RedAuthOperation
 */
class RedAuthOperation extends RedAuthItem
{

    private $_module;
    private $_controller;
    private $_action;

    private $_rawdata;

    /**
     * @param IAuthManager|array $params
     * @param integer $id
     */
    public function __construct($auth, $id, $name = '', $description = '', $status = 0, $levelInfo = array())
    {
        parent::__construct($auth, $id, $name, $description, $status, $levelInfo);

        $this->_module = isset($levelInfo['module']) ? $levelInfo['module'] : null;
        $this->_controller = isset($levelInfo['controller']) ? $levelInfo['controller'] : null;
        $this->_action = isset($levelInfo['action']) ? $levelInfo['action'] : null;
        $this->_rawdata = $levelInfo;
    }

    /**
     * @see RedAuthItem::addChild()
     */
    /*public function addChild($name, $module ,$controller, $action, $description = ''){
        return $this->getAuthManager()->createAuthItem($name, $this->getType(),
                    $description,$this->getId(),array(
                        'module' => $module,
                        'controller' => $controller,
                        'action' => $action
                    ));
    }*/

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * @param string $module
     * @return RedAuthOperation
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     * @param string $controller
     * @return RedAuthOperation
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * @param string $action
     * @return RedAuthOperation
     */
    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function getRawData($name)
    {
        if (array_key_exists($name, $this->_rawdata)) {
            return $this->_rawdata[$name];
        } else {
            return null;
        }
    }


    /**
     * @see RedAuthItem::getOptions()
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), array(
            'module' => $this->getModule(),
            'controller' => $this->getController(),
            'action' => $this->getAction(),
        ));
    }

    /**
     * @see RedAuthItem::getIsMultiLevel()
     */
    public function getIsMultiLevel()
    {
        return !($this->getLevel() == 0);
    }

    /**
     * @see RedAuthItem::getType()
     */
    public function getType()
    {
        return TYPE_OPERATION;
    }
}