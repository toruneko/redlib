<?php
/**
 * File: BaeEmptyCache.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 16/4/8 20:57
 * Description: 
 */
class BaeEmptyCache extends CCache
{

    protected function getValue($key)
    {
        return false;
    }

    protected function getValues($keys)
    {
        return false;
    }

    protected function setValue($key, $value, $expire = 0)
    {
        return true;
    }

    protected function addValue($key, $value, $expire = 0)
    {
        return true;
    }

    protected function deleteValue($key)
    {
        return true;
    }

    protected function flushValues()
    {
        return true;
    }
}