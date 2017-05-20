<?php

/**
 * File: SaeKvCache.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/4/6 10:27
 * Description: redis
 */
class SaeRedisCache extends CCache
{
    private $_cache = null;

    public function init()
    {
        parent::init();

        $this->_cache = new SaeKV();
        $this->_cache->init();
    }

    protected function getValue($key)
    {
        $data = $this->_cache->get($key);
        $data = CJSON::decode($data);
        $expire = $data['expire'];
        if ($expire == -1) {
            return $data['value'];
        }
        if (time() <= $expire) {
            return $data['value'];;
        }
        return false;
    }

    protected function setValue($key, $value, $expire = 0)
    {
        if ($expire > 0) {
            $expire = time() + $expire;
        } else {
            $expire = -1;
        }
        $data = [
            'value' => $value,
            'expire' => $expire
        ];
        return $this->_cache->set($key, CJSON::encode($data));
    }

    protected function addValue($key, $value, $expire = 0)
    {
        if ($expire > 0) {
            $expire = time() + $expire;
        } else {
            $expire = -1;
        }
        $data = [
            'value' => $value,
            'expire' => $expire
        ];
        return $this->_cache->add($key, CJSON::encode($data));
    }

    protected function deleteValue($key)
    {
        return $this->_cache->delete($key);
    }

    protected function flushValues()
    {
    }
}