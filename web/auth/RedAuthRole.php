<?php
/**
 * file:RedAuthRole.php
 * author:Toruneko@outlook.com
 * date:2014-7-13
 * desc:RedAuthRole
 */
class RedAuthRole extends RedAuthItem{
	const ADMIN_ROLE = 'user_role';
	const PERMISSION = 'permission';
	
	/**
	 * @param integer $userId
	 * @return effected row
	 */
	public function addUser($userId) {
		return $this->getAuthManager()->assign(self::ADMIN_ROLE, array(
			'user_id' => $userId, 
			'role_id' => $this->getId()
		));
	}

	/**
	 * @return array
	 */
	public function getUsers() {
		return $this->getAuthManager()->getAuthAssignment(self::ADMIN_ROLE, array(
			'role_id' => $this->getId()
		));
	}

	/**
	 * @param integer $userId
	 * @return boolean
	 */
	public function hasUser($userId) {
		return $this->getAuthManager()->isAssigned(self::ADMIN_ROLE, array(
			'user_id' => $userId,
			'role_id' => $this->getId()
		));
	}

	/**
	 * @param integer $userId
	 * @return effected row
	 */
	public function removeUser($userId) {
		return $this->getAuthManager()->revoke(self::ADMIN_ROLE, array(
			'user_id' => $userId,
			'role_id' => $this->getId()
		));
	}

	/**
	 * @param integer $operationId
	 * @return effected row
	 */
	public function assign($operationId){
		return $this->getAuthManager()->assign(self::PERMISSION, array(
			'operation_id' => $operationId, 
			'role_id' => $this->getId()
		));
	}
	
	/**
	 * @param integer $operationId
	 * @return effected row
	 */
	public function revoke($operationId){
		return $this->getAuthManager()->revoke(self::PERMISSION, array(
			'operation_id' => $operationId, 
			'role_id' => $this->getId()
		));
	}
	
	/**
	 * @param integer $operationId
	 * @return boolean
	 */
	public function isAssigned($operationId){
		return $this->getAuthManager()->isAssigned(self::PERMISSION, array(
			'operation_id' => $operationId,
			'role_id' =>  $this->getId()
		));
	}
	
	/**
	 * @return RedAuthOperation
	 */
	public function getAssigns(){
        $records = $this->getAuthManager()->getAuthAssignment(self::PERMISSION, array(
			'role_id' => $this->getId()
		));
		$operations = array();
		foreach($records as $record){
			$operations[] = $this->getAuthManager()->getAuthItems(RedAuthItem::TYPE_OPERATION,$record['operation_id']);
		}
		return $operations;
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
		return RedAuthItem::TYPE_ROLE;
	}
	
}