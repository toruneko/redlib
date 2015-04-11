<?php
/**
 * File: SaeKvCache.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/4/6 10:27
 * Description: redis
 */
class SaeRedisCache extends CCache{
    private $_cache=null;

    public function init(){
        parent::init();

        $this->_cache = new SaeKV();
        $this->_cache->init();
    }

    protected function getValue($key){
        return $this->_cache->get($key);
    }

    protected function getValues($keys){
        return $this->_cache->mget($keys);
    }

    protected function setValue($key,$value,$expire = 0){
        return $this->_cache->set($key,$value);
    }

    protected function addValue($key,$value,$expire = 0){
        return $this->_cache->add($key,$value);
    }

    protected function deleteValue($key){
        return $this->_cache->delete($key);
    }

    protected function flushValues(){
    }
}