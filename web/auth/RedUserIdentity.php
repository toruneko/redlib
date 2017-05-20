<?php

/**
 * file:RedUserIdentity.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc:RedUserIdentity
 */
class RedUserIdentity extends CUserIdentity
{

    public function getId()
    {
        return $this->getState('id');
    }
}