<?php

/**
 * file:RedAction.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc:RedAction
 */
class RedAction extends CAction
{
    /*
     * @see CComponent::__get()
     */
    public function __get($name)
    {
        if (isset($this->getController()->$name)) {
            return $this->getController()->$name;
        } else {
            return parent::__get($name);
        }
    }

    /*
     * @see CComponent::__call()
     */
    public function __call($name, $parameters)
    {
        if (method_exists($this->getController(), $name)) {
            return call_user_func_array(array($this->getController(), $name), $parameters);
        } else {
            return parent::__call($name, $parameters);
        }
    }
}