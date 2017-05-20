<?php

/**
 * file:RedSecurityManager.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc: RedSecurityManager
 */
class RedSecurityManager extends CSecurityManager
{
    public $uuidSalt = '';

    public function generateUUID($rawData, $saltCost = 10)
    {
        if (is_string($rawData)) {
            $rawData = array($rawData);
        }

        $string = '';
        foreach ($rawData as $data) {
            $string .= is_string($data) ? $data : strval($data);
        }
        $string .= $this->uuidSalt === '' ? $this->generateRandomString($saltCost) : $this->uuidSalt;

        $rawUuid = md5($string);
        $uuidBody = array();
        for ($i = 0; $i < 31; $i += 4) {
            $uuidBody[] = substr($rawUuid, $i, 4);
        }
        shuffle($uuidBody);

        $uuid = $uuidBody[0] . $uuidBody[1] . '-';
        $uuid .= $uuidBody[2] . '-' . $uuidBody[3] . '-' . $uuidBody[4] . '-';
        $uuid .= $uuidBody[5] . $uuidBody[6] . $uuidBody[7];

        return $uuid;
    }
}