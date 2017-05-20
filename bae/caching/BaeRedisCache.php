<?php

/**
 * File: BaeRedisCache.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 16/4/6 22:56
 * Description:
 */
class BaeRedisCache extends CCache
{
    private $_cache = null;

    public $host;
    public $port;
    public $username;
    public $password;
    public $dbname;

    public function init()
    {
        parent::init();

        try {
            $this->_cache = new Redis();
            $ret = $this->_cache->connect($this->host, $this->port);
            if ($ret === false) {
                Yii::log($this->_cache->getLastError(), CLogger::LEVEL_ERROR);
                exit;
            }

            $ret = $this->_cache->auth($this->username . "-" . $this->password . "-" . $this->dbname);
            if ($ret === false) {
                Yii::log($this->_cache->getLastError(), CLogger::LEVEL_ERROR);
                exit;
            }

        } catch (RedisException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }

    protected function getValue($key)
    {
        try {
            $value = $this->_cache->get($key);
            return empty($value) ? false : gzuncompress($value);
        } catch (RedisException $e) {
            Yii::log($e->getMessage() . ":" . $key, CLogger::LEVEL_ERROR);
            return false;
        }
    }

    protected function setValue($key, $value, $expire = 0)
    {
        $compress = gzcompress($value);
        if (mb_strlen($compress) > 2000) return true;

        try {
            return $this->_cache->set($key, $compress, $expire);
        } catch (RedisException $e) {
            Yii::log($e->getMessage() . ":" . $key . "[" . $value . "]", CLogger::LEVEL_ERROR);
            return false;
        }
    }

    protected function addValue($key, $value, $expire = 0)
    {
        return $this->setValue($key, $value, $expire);
    }

    protected function deleteValue($key)
    {
        try {
            return $this->_cache->del($key);
        } catch (RedisException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            return false;
        }
    }

    protected function flushValues()
    {
        try {
            return $this->_cache->flushdb();
        } catch (RedisException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            return false;
        }
    }
}