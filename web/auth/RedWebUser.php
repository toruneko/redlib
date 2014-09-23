<?php
/**
 * file:RedWebUser.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc:RedWebUser
 */
class RedWebUser extends CWebUser{
	public $accessCacheTimeout = 3600;
	
	public function checkAccess($operation,$params=array(),$allowCaching=true){
		if($allowCaching){
			if(($access = $this->getCachedAccess($this->generateKey($operation))) !== false){
				return $access;
			}
		}
	
		$access=Yii::app()->getAuthManager()->checkAccess($operation,$this->getId(),$params);
		if($allowCaching){
			$this->cacheAccess($this->generateKey($operation), $access);
		}
	
		return $access;
	}
	
	public function generateKey($operation){
		return 'USER_'.$this->getId().'_CheckAccess_'.CJSON::encode($operation);
	}
	
	public function getCachedAccess($key){
		$app = Yii::app();
		$cache = $app->getCache();
		if ( $cache !== null ){
			return $cache->get($key);
		}else {
			return $app->session[$key];
		}
	}
	
	public function cacheAccess($key,$data){
		$app = Yii::app();
		$cache = $app->getCache();
		if ( $cache !== null ){
			$cache->set($key,$data,$this->accessCacheTimeout);
		}else {
			$app->session[$key] = $data;
		}
	}
}