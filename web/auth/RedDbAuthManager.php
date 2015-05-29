<?php
/**
 * file:RedDbAuthManager.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc:RedDbAuthManager
 */
class RedDbAuthManager extends RedAuthManager{
	public $connectionID = 'db';
	public $db;
	
	private $_classMap = array(
		RedAuthItem::TYPE_ROLE => 'RedAuthRole',
		RedAuthItem::TYPE_GROUP => 'RedAuthGroup',
		RedAuthItem::TYPE_OPERATION => 'RedAuthOperation'
	);
	
	public function init(){
		parent::init();

		if($this->db !== null){
			return $this->db;
		}elseif(($this->db = Yii::app()->getComponent($this->connectionID)) instanceof CDbConnection){
			return $this->db;
		}else{
			throw new CException(Yii::t('yii','CDbAuthManager.connectionID "{id}" is invalid. Please make sure it refers to the ID of a CDbConnection application component.',
					array('{id}'=>$this->connectionID)));
		}
	}

	/**
	 * 权限检查
	 * @see IAuthManager::checkAccess()
	 */
	public function checkAccess($itemName, $userId, $params = array()) {
		$assigns = $this->getAuthAssignments($userId);
		
		foreach($assigns as $item){
			if($item->checkAccess($itemName))
				return true;
		}

		return false;
	}

	/* 
	 * 添加节点
	 * @see IAuthManager::createAuthItem()
	 */
	public function createAuthItem($name, $type, $description = '', $bizRule = null, $data = null) {
		$parent = $this->db->createCommand()
			->select()->from($type)
			->where('id=:id',array('id' => $bizRule))
			->queryRow();

		$transaction = $this->db->beginTransaction();
		try{
			$levelInfo = $this->updateTreeOnCreate($type,$parent);
			$effectRow = $this->db->createCommand()->insert($type,array_merge($data,$levelInfo,array(
				'name' => $name,
				'description' => $description,
				'status' => 0
			)));
            $lastId = $this->db->getLastInsertID();

			if($effectRow){
				$transaction->commit();

				$class = new ReflectionClass($this->_classMap[$type]);
				return call_user_func_array(array($class,'newInstance'),array(
					$this,$lastId,$name,$description,0,array_merge($data,$levelInfo)
				));
			}
		
			throw new CException();
		}catch (CException $e){
			$transaction->rollback();
			return false;
		}
	}
	
	/* 
	 * 删除节点
	 * @see IAuthManager::removeAuthItem()
	 */
	public function removeAuthItem($name) {
        if($name == false) return false;

		if (!$name->getIsMultiLevel()) {
			return $this->db->createCommand->delete($name->getType(),
					'id=:id',array('id' => $name->getId()));
		}else{
			
			$transaction = $this->db->beginTransaction();
			try {
				$effectRow = $this->db->createCommand()
					->delete($name->getType(),'lft>=:left AND rgt<=:right',array(
						'left' => $name->getLft(),
						'right' => $name->getRgt()
					));
				
				$decrease = $name->getRgt() - $name->getLft() + 1;
				$this->updateTreeOnDelete($name->getType(),$name->getRgt(),$decrease);
				
				if ($effectRow){
					$transaction->commit();
					return true;
				}
				
				throw new CException();
			}catch ( CException $e ){
				$transaction->rollback();
				return false;
			}
		}
	}
	
	/*
	 * 更新节点
	 * @see IAuthManager::saveAuthItem()
	*/
	public function saveAuthItem($item, $oldName = null) {
		if($item->getIsMultiLevel()){
			$old = $this->db->createCommand()
				->select()->from($item->getType())
				->where('id=:id',array('id' => $item->getId()))
				->queryRow();
			if ($old === false) return false;

			if($old['fid'] != $item->getFid()){
				$transaction = $this->db->beginTransaction();
				try {
					$effectRow = $this->db->createCommand()
						->update($item->getType(), $item->getOptions(), 'id=:id', array(
							'id' => $item->getId()
						));

					if($this->updateTreeOnMigrate($item->getType(),$old,$item->getFid())){
                        if ( $effectRow ){
                            $transaction->commit();
                            return true;
                        }
                    }
	
					throw new CException();
				}catch ( CException $e ){
                    var_dump($e);
					$transaction->rollback();
					return false;
				}
			}
		}
	
		return $this->db->createCommand()
			->update($item->getType(), $item->getOptions(), 'id=:id', array(
				'id' => $item->getId()
			));
	}
	
	/*
	 * 添加子节点
	 * @see IAuthManager::addItemChild()
	*/
	public function addItemChild($itemName, $childName) {
		if(!$itemName->getIsMultilevel()) return false;

		return $this->createAuthItem($childName, $itemName->getType(), null, $itemName->getId(),array());
	}
	
	/*
	 * 获取所有子节点
	 * @see IAuthManager::getItemChildren()
	*/
	public function getItemChildren($itemName) {
		if(!$itemName->getIsMultilevel()) return array();
	
		$records = $this->db->createCommand()
			->select()->from($itemName->getType())
			->where('lft>:left AND rgt<:right',array(
				'left' => $itemName->getLft(),
				'right' => $itemName->getRgt()
			))->order('lft ASC')->queryAll();
			
		$class = new ReflectionClass(get_class($itemName));
		$objects = array();
		foreach($records as $record){
			$objects[] = call_user_func_array(array($class,'newInstance'), array(
				$this,$record["id"],$record["name"],$record["description"],$record["status"],$record
			));
		}
		return $objects;
	}
	
	/*
	 * 是否拥有该子节点
	 * @see IAuthManager::hasItemChild()
	*/
	public function hasItemChild($itemName, $childName) {
		if(!$itemName->getIsMultilevel()) return false;
	
		$records = $this->db->createCommand()
			->select()->from($itemName->getType())
			->where('lft>:left AND rgt<:right',array(
				'left' => $itemName->getLft(),
				'right' => $itemName->getRgt()
			))->queryAll();
		
		foreach($records as $record){
			if($record['id'] == $childName) return true;
		}
		return false;
	}
	
	/*
	 * 删除所有子节点
	 * @see IAuthManager::removeItemChild()
	*/
	public function removeItemChild($itemName, $childName) {
		if(!$itemName->getIsMultilevel()) return false;

		$transaction = $this->db->beginTransaction();
		try{
			$effectRow = $this->db->createCommand()->delete($itemName->getType(),
				'lft>:left AND rgt<:right',array(
					'left' => $itemName->getLft(),
					'right' => $itemName->getRgt()
				));
			$decrease = $itemName->getRgt() - $itemName->getLft() - 1;
			$this->updateTreeOnDelete($itemName->getType(),$itemName->getRgt(),$decrease);
	
			if($effectRow){
				$transaction->commit();
				return true;
			}
	
			throw new CException();
		}catch (CException $e){
			$transaction->rollback();
			return false;
		}
	}
	
	/*
	 * 返回节点
	 * @see IAuthManager::getAuthItems()
	*/
	public function getAuthItems($type = null, $userId = null) {
		$data = $this->db->createCommand()
			->select()->from($type)
			->where('id=:id',array('id' => $userId))
			->queryRow();
		if($data == false) return false;

		$class = new ReflectionClass($this->_classMap[$type]);
		return call_user_func_array(array($class,'newInstance'), array(
			$this,$data['id'],$data['name'],$data['description'],$data['status'],$data
		));
	}
	
	/*
	 * 返回用户角色
	 * @see IAuthManager::getAuthItem()
	*/
	public function getAuthItem($name) {
		$records = $this->db->createCommand()
			->select()->from(RedAuthRole::ADMIN_ROLE)
			->where('user_id=:uid',array('uid' => $name))
			->queryAll();
		$roles = array();
		foreach($records as $record){
			$roles[] = $this->getAuthItems(RedAuthItem::TYPE_ROLE,$record['role_id']);
		}
		return $roles;
	}

    /**
     * 返回用户或角色所在用户组
     * @return boolean|multitype:object
     */
    public function getAuthItemGroup($userId){
        $records = $this->db->createCommand()
            ->select()->from(RedAuthGroup::ADMIN_GROUP)
            ->where('user_id=:uid',array('uid' => $userId))
            ->queryAll();
        $groups = array();
        foreach($records as $record){
            $groups[] = $this->getAuthItems(RedAuthItem::TYPE_GROUP,$record['group_id']);
        }
        return $groups;
    }
	
	/**
	 * 返回关联
	 * @see IAuthManager::getAuthAssignment()
	 */
	public function getAuthAssignment($itemName, $userId) {
		$condition = array();
		foreach($userId as $key => $value){
			$condition[] = $key.'=:'.$key;
		}
		return $this->db->createCommand()
			->select()->from($itemName)
			->where(join(' AND ', $condition),$userId)
			->queryAll();
	}
	
	/**
	 * 添加关联
	 * @see IAuthManager::assign()
	 */
	public function assign($itemName, $userId, $bizRule = null, $data = null) {
		if($this->isAssigned($itemName, $userId)) return false;
		return $this->db->createCommand()->insert($itemName, $userId);
	}
	
	/**
	 * 是否关联
	 * @see IAuthManager::isAssigned()
	 */
	public function isAssigned($itemName, $userId) {
		$condition = array();
		foreach($userId as $key => $value){
			$condition[] = $key.'=:'.$key;
		}
		return $this->db->createCommand()
		->select()->from($itemName)
		->where(join(' AND ', $condition),$userId)
		->queryScalar() !== false;
	}
	
	/**
	 * 取消关联
	 * @see IAuthManager::revoke()
	 */
	public function revoke($itemName, $userId) {
		$condition = array();
		foreach($userId as $key => $value){
			$condition[] = $key.'=:'.$key;
		}
		return $this->db->createCommand()->delete($itemName, join(' AND ', $condition), $userId);
	}
	
	
	/**
	 * 返回用户权限
	 * @see IAuthManager::getAuthAssignments()
	*/
	public function getAuthAssignments($userId) {
		$roles = $this->getAuthItem($userId); // getAuthItemRole
		$assigns = array();
		foreach($roles as $role){
			$assigns[] = new RedAuthAssignment($this, $role, $role->getAssigns());
		}
		return $assigns;
	}
	
	protected function updateTreeOnCreate($table,$parent){
		if ( $parent === false ){
			$rightPeak = $this->db->createCommand()
				->select('rgt')->from($table)
				->order('rgt DESC')->limit(1)
				->queryScalar();
			if ( $rightPeak === false ) $rightPeak = 0;
			
			return array('fid'=>0,'level'=>1,'lft'=>$rightPeak + 1,'rgt'=>$rightPeak + 2);
		}else {

			$this->db->createCommand()
				->update($table,array(
					'lft' => new CDbExpression('lft+2')
				),'lft>=:right',array(
					'right' => $parent['rgt']
				));
			$this->db->createCommand()
				->update($table,array(
					'rgt' => new CDbExpression('rgt+2')
				),'rgt>=:right',array(
					'right' => $parent['rgt']
				));
			
			return array(
				'fid' => $parent['id'],
				'level' => $parent['level'] + 1,
				'lft' => $parent['rgt'],
				'rgt' => $parent['rgt'] + 1
			);
		}
	}
	
	protected function updateTreeOnDelete($table,$rightPeak,$decrease){		
		$this->db->createCommand()
			->update($table,array(
				'lft' => new CDbExpression('lft-'.$decrease)
			),'lft>=:right',array(
				'right' => $rightPeak
			));
			
		$this->db->createCommand()
			->update($table,array(
				'rgt' => new CDbExpression('rgt-'.$decrease)
			),'rgt>=:right',array(
				'right' => $rightPeak
			));
	}
	
	protected function updateTreeOnMigrate($table,$subtreeRoot,$targetNode){		
		$targetNode = $this->db->createCommand()
			->select()->from($table)
			->where('id=:id',array('id' => $targetNode))
			->queryRow();
		if($targetNode === false) return false;

		$this->db->createCommand()
			->update($table,array(
				'lft' => new CDbExpression('-lft'),
				'rgt' => new CDbExpression('-rgt')
			),'lft>=:left AND rgt<=:right',array(
				'left' => $subtreeRoot['lft'],
				'right' => $subtreeRoot['rgt']
			));
		
		$decrease = $subtreeRoot['rgt'] - $subtreeRoot['lft'] + 1;
        if($subtreeRoot['rgt'] > $targetNode['rgt']){
            $decrease = '+'.$decrease;
            $min = $targetNode['lft'];
            $max = $subtreeRoot['lft'];
        }else{
            $decrease = '-'.$decrease;
            $min = $subtreeRoot['rgt'];
            $max = $targetNode['rgt'];
        }
        $this->db->createCommand()
            ->update($table, array(
                'lft' => new CDbExpression('lft'.$decrease)
            ),'lft>:right AND lft<:fright', array(
                'right' => $min,
                'fright' => $max
            ));
        $this->db->createCommand()
            ->update($table, array(
                'rgt' => new CDbExpression('rgt'.$decrease)
            ),'rgt>:right AND rgt<:fright', array(
                'right' => $min,
                'fright' => $max
            ));


        if($subtreeRoot['rgt'] > $targetNode['rgt']){
            $increase = '-'.($subtreeRoot['lft'] - $targetNode['lft'] - 1);
        }else{
            $increase = '+'.($targetNode['rgt'] - $subtreeRoot['rgt'] - 1);
        }
        $level = $targetNode['level'] - $subtreeRoot['level'] + 1;
        if($level >= 0) $level = '+'.$level;
		$this->db->createCommand()
			->update($table,array(
				'lft' => new CDbExpression('(-lft)'.$increase),
				'level' => new CDbExpression('level'.$level),
				'rgt' => new CDbExpression('(-rgt)'.$increase)
			),'rgt>=:right AND lft<=:left',array(
				'right' => -$subtreeRoot['rgt'],
				'left' => -$subtreeRoot['lft']
			));
		return true;
	}
	
	public function save() {}
	public function executeBizRule($bizRule, $params, $data) {}
	public function clearAll() {}
	public function clearAuthAssignments() {}
	public function deleteAuthAssignments($assignment) {}
	public function saveAuthAssignment($assignment) {}
}