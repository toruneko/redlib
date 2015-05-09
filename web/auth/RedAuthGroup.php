<?php
/**
 * file:RedAuthGroup.php
 * author:Toruneko@outlook.com
 * date:2014-7-13
 * desc:RedAuthGroup
 */
class RedAuthGroup extends RedAuthItem{
	const ADMIN_GROUP = 'user_group';

	/**
	 * @see RedAuthItem::addChild()
	 */
	public function addChild($name, $description = '') {
		return $this->getAuthManager()->createAuthItem($name, $this->getType(),
			$description,$this->getId(),array('status' => 0));
	}
	
	/**
	 * @param integer $userId
	 * @return effected row
	 */
	public function addUser($userId) {
		return $this->getAuthManager()->assign(self::ADMIN_GROUP, array(
			'user_id' => $userId, 
			'group_id' => $this->getId()
		));
	}
	
	/**
	 * @return array
	 */
	public function getUsers(){
		return $this->getAuthManager()->getAuthAssignment(self::ADMIN_GROUP, array(
			'group_id' => $this->getId()
		));
	}
	
	/**
	 * @param integer $userId
	 * @return boolean
	 */
	public function hasUser($userId){
		return $this->getAuthManager()->isAssigned(self::ADMIN_GROUP, array(
			'user_id' => $userId,
			'group_id' => $this->getId()
		));
	}
	
	/**
	 * @param integer $userId
	 * @return effected row
	 */
	public function removeUser($userId){
		return $this->getAuthManager()->revoke(self::ADMIN_GROUP, array(
			'user_id' => $userId,
			'group_id' => $this->getId()
		));
	}

	/**
	 * @see RedAuthItem::getIsMultiLevel()
	 */
	public function getIsMultiLevel() {
        return !($this->getLevel() == 0);
	}

	/**
	 * @see RedAuthItem::getType()
	 */
	public function getType() {
		return RedAuthItem::TYPE_GROUP;
	}
}