<?php

/**
 * @file: AceMemCache.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeMemCache class file
 */
class AceMemCache extends CCache
{
    private $_cache = null;

    public $bucket;

    public function init()
    {
        parent::init();

        $this->_cache = Alibaba::Cache($this->bucket);
    }

    protected function getValue($key)
    {
        return $this->_cache->get($key);
    }

    protected function getValues($keys)
    {
        return $this->_cache->get($keys);
    }

    protected function setValue($key, $value, $expire = 0)
    {
        if ($expire > 0) {
            $expire += time();
        } else {
            $expire = 0;
        }
        return $this->_cache->set($key, $value, $expire);
    }

    protected function addValue($key, $value, $expire = 0)
    {
        if ($expire > 0) {
            $expire += time();
        } else {
            $expire = 0;
        }
        return $this->_cache->add($key, $value, $expire);
    }

    protected function deleteValue($key)
    {
        return $this->_cache->delete($key);
    }

    protected function flushValues()
    {
        return $this->_cache->flush();
    }
}