<?php

/**
 * file:RedArrayDataProvider.php
 * author:Toruneko@outlook.com
 * date:2014-7-19
 * desc:
 */
class RedArrayDataProvider extends CArrayDataProvider
{

    /*
     * @see CArrayDataProvider::__construct()
     */
    public function __construct($rawData, $config = array())
    {
        parent::__construct($rawData, $config);

        $this->setPagination(false);
    }
}