<?php
/**
 * @file: SaeMemCache.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeMemCache class file
 */
class SaeMemCache extends CCache{
	private $_cache=null;
	
	public function init(){
		parent::init();

		$this->_cache = memcache_init();
	}
	
	protected function getValue($key){
		return $this->_cache->get($key);
	}
	
	protected function getValues($keys){
		return $this->_cache->get($keys);
	}
	
	protected function setValue($key,$value,$expire){
		if($expire > 0){
			$expire += time();
		}else{
			$expire = 0;
		}
		return $this->_cache->set($key,$value,0,$expire);
	}
	
	protected function addValue($key,$value,$expire){
		if($expire > 0){
			$expire += time();
		}else{
			$expire = 0;
		}
		return $this->_cache->add($key,$value,0,$expire);
	}
	
	protected function deleteValue($key){
		return $this->_cache->delete($key, 0);
	}
	
	protected function flushValues(){
		return $this->_cache->flush();
	}
}