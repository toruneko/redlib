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
	 * @param string $name
	 * @param string $description
	 * @param array $options
	 */
	public function createRole($name,$description = '',$fid = 0){
		return $this->createAuthItem($name,RedAuthItem::TYPE_ROLE,$description,$fid,array());
	}

	/**
	 * 添加用户组
	 * @param string $name
	 * @param string $description
	 * @param array $options
	 */
	public function createGroup($name,$description = '',$fid = 0){
		return $this->createAuthItem($name,RedAuthItem::TYPE_GROUP,$description,$fid,array());
	}

	/**
	 * 添加操作
	 * @param string $name
	 * @param string $description
	 * @param array $options
	 */
	public function createOperation($name,$description = '',$fid = 0,$mca = array()){
		return $this->createAuthItem($name,RedAuthItem::TYPE_OPERATION,$description,$fid,$mca);
	}
	
	/**
	 * 删除角色
	 * @param boolean $id
	 */
	public function removeRoleByPk($id){
		return $this->removeAuthItem(new RedAuthRole($this, $id));
	}
	
	/**
	 * 删除用户组
	 * @param boolean $id
	 */
	public function removeGroupByPk($id){
		return $this->removeAuthItem(new RedAuthGroup($this, $id));
	}
	
	/**
	 * 删除操作
	 * @param boolean $id
	 */
	public function removeOperationByPk($id){
		return $this->removeAuthItem(new RedAuthOperation($this, $id));
	}

	/**
	 * 返回角色
	 * @param string $userId
	 */
	public function getRoleByPk($roleId){
		return $this->getAuthItems(RedAuthItem::TYPE_ROLE,$roleId);
	}

	/**
	 * 返回用户组
	 * @param string $userId
	 */
	public function getGroupByPk($groupId){
		return $this->getAuthItems(RedAuthItem::TYPE_GROUP,$groupId);
	}

	/**
	 * 返回操作
	 * @param string $userId
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
		return $this->getAuthItemGroup(RedAuthGroup::ADMIN_GROUP, $userId);
	}
	
	/**
	 * 返回角色所在组
	 * @param integer $roleId
	 */
	public function getGroupByRoleId($roleId){
		return $this->getAuthItemGroup(RedAuthGroup::ROLE_GROUP, $roleId);
	}
}