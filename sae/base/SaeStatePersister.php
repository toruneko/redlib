<?php
/**
 * @file: SaeStatePersister.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: This file contains classes implementing security manager feature.
 */
class SaeStatePersister extends CApplicationComponent implements IStatePersister{
	public $stateID;
	public $cacheID = 'cache';
	
	public function init(){
		$this->stateID = 'runtimes'.DIRECTORY_SEPARATOR.'state.bin';
	}
	
	public function load(){
		if($this->cacheID !== false && ($cache = Yii::app()->getComponent($this->cacheID)) !== null){
			$cacheKey = 'Yii.CStatePersister.'.$this->stateID;
			if(($value = $cache->get($cacheKey)) !== false){
				return unserialize($value);
			}else{
				return null;
			}
		}else{
			return null;
		}
	}
	
	public function save($state){
		if($this->cacheID !== false && ($cache = Yii::app()->getComponent($this->cacheID)) !== null){
			$cacheKey = 'Yii.CStatePersister.'.$this->stateID;
			$content = serialize($state);
			$cache = Yii::app()->getComponent($this->cacheID);
			$cache->set($cacheKey,$content);
			return true;
		}else{
			return false;
		}
	}
}