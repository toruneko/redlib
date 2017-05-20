<?php

/**
 * @file: AceHttpSession.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-11
 * @desc:
 */
class AceHttpSession extends CHttpSession
{
    public function setTimeout($value)
    {
        ini_set('session.cookie_lifetime', $value);
    }

    public function getTimeout()
    {
        return (int)ini_get('session.cookie_lifetime');
    }
}