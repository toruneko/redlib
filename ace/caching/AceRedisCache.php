<?php

/**
 * File: AceRedisCache.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/4/6 10:27
 * Description: redis
 */
class AceRedisCache extends CCache
{
    private $_cache = null;

    public $host;
    public $port;
    public $username;
    public $password;

    public function init()
    {
        parent::init();

        $this->_cache = Alibaba::cache(array(
            'host'  => $this->host,
            'port'  => $this->port,
            'username' => $this->username,
            'password' => $this->password
        ));
    }

    protected function getValue($key)
    {
        return $this->_cache->get($key);
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