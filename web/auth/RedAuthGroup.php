<?php
/**
 * file:RedAuthGroup.php
 * author:Toruneko@outlook.com
 * date:2014-7-13
 * desc:RedAuthGroup
 */
class RedAuthGroup extends RedAuthItem{
	const ADMIN_GROUP = 'user_group';
	const ROLE_GROUP = 'role_group';

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
	 * @param integer $roleId
	 * @return effected row
	 */
	public function addRole($roleId){
		return $this->getAuthManager()->assign(self::ROLE_GROUP, array(
			'role_id' => $roleId,
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
	 * @return RedAuthRole array
	 */
	public function getRoles(){
		$records = $this->getAuthManager()->getAuthAssignment(self::ROLE_GROUP, array(
			'group_id' => $this->getId()
		));
		$roles = array();
		foreach($records as $record){
			$roles[] = $this->getAuthManager()->getAuthItems(RedAuthItem::TYPE_ROLE,$record['role_id']);
		}
		return $roles;
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
	 * @param integer $roleId
	 * @return boolean
	 */
	public function hasRole($roleId){
		return $this->getAuthManager()->isAssigned(self::ROLE_GROUP, array(
			'role_id' => $roleId,
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
	 * @param integer $roleId
	 * @return effected row
	 */
	public function removeRole($roleId){
		return $this->getAuthManager()->revoke(self::ROLE_GROUP, array(
			'role_id' => $roleId,
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