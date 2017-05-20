<?php

/**
 * file:RedAuthAssignment.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc:RedAuthAssignment
 */
class RedAuthAssignment extends CComponent
{
    private $_auth;

    private $_role;
    private $_operations;

    public function __construct($auth, $role, $operations)
    {
        $this->_auth = $auth;

        $this->_operations = $operations;
        $this->_role = $role;
    }

    public function checkAccess($itemName)
    {
        foreach ($this->_operations as $operation) {
            if ($operation != false && $operation->checkAccess($itemName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return the $_auth
     */
    public function getAuth()
    {
        return $this->_auth;
    }

    /**
     * @return the $_operations
     */
    public function getOperations()
    {
        return $this->_operations;
    }

    public function getRole()
    {
        return $this->_role;
    }

    public function __toString()
    {
        return get_class($this) . $this->_id;
    }
}