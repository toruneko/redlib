<?php
/**
 * file:RedAuthManager.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc:RedAuthManager
 */
abstract class RedAuthManager extends CApplicationComponent implements IAuthManager{

    /**
     * 添加角色
     * @param $name
     * @param string $description
     * @param int $fid
     * @return mixed
     */
	public function createRole($name,$description = '',$fid = 0){
		return $this->createAuthItem($name,RedAuthItem::TYPE_ROLE,$description,$fid,array());
	}

    /**
     * 添加用户组
     * @param $name
     * @param string $description
     * @param int $fid
     * @return mixed
     */
	public function createGroup($name,$description = '',$fid = 0){
		return $this->createAuthItem($name,RedAuthItem::TYPE_GROUP,$description,$fid,array());
	}

    /**
     * 添加操作
     * @param $name
     * @param string $description
     * @param int $fid
     * @param array $mca
     * @return mixed
     */
	public function createOperation($name,$description = '',$fid = 0,$mca = array()){
		return $this->createAuthItem($name,RedAuthItem::TYPE_OPERATION,$description,$fid,$mca);
	}
	
	/**
	 * 删除角色
	 * @param boolean $id
	 */
	public function removeRoleByPk($id){
		return $this->removeAuthItem($this->getRoleByPk($id));
	}
	
	/**
	 * 删除用户组
	 * @param boolean $id
	 */
	public function removeGroupByPk($id){
		return $this->removeAuthItem($this->getGroupByPk($id));
	}
	
	/**
	 * 删除操作
	 * @param boolean $id
	 */
	public function removeOperationByPk($id){
		return $this->removeAuthItem($this->getOperationByPk($id));
	}

	/**
	 * 返回角色
	 * @param string $roleId
	 */
	public function getRoleByPk($roleId){
		return $this->getAuthItems(RedAuthItem::TYPE_ROLE,$roleId);
	}

	/**
	 * 返回用户组
	 * @param string $groupId
	 */
	public function getGroupByPk($groupId){
		return $this->getAuthItems(RedAuthItem::TYPE_GROUP,$groupId);
	}

	/**
	 * 返回操作
	 * @param string $operationId
	 */
	public function getOperationByPk($operationId){
		return $this->getAuthItems(RedAuthItem::TYPE_OPERATION,$operationId);
	}
	
	/**
	 * 返回用户角色
	 * @param integer $userId
	 */
	public function getRoleByUserId($userId){
		return $this->getAuthItem($userId);
	}
	
	/**
	 * 返回用户所在组
	 * @param integer $userId
	 */
	public function getGroupByUserId($userId){
		return $this->getAuthItemGroup($userId);
	}

    abstract public function getAuthItemGroup($userId);
}