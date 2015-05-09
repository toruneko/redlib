<?php
/**
 * file:RedAuthItem.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc:RedAuthItem
 */
abstract class RedAuthItem extends CComponent{
	private $_auth;
	
	private $_id;
	private $_name;
	private $_description;
	private $_status;
	
	private $_fid;
	private $_level;
	private $_lft;
	private $_rgt;

	const TYPE_ROLE = 'role';
	const TYPE_GROUP = 'group';
	const TYPE_OPERATION = 'operation';
	
	/**
	 * @param IAuthManager $auth
	 * @param integer $id
	 * @param string $name
	 * @param string $description
	 */
	public function __construct($auth,$id,$name = '',$description = '', $status = 0, $levelInfo = array()){
		$this->_auth = $auth;
		$this->_id = $id;
		$this->_name = $name;
		$this->_description = $description;
		$this->_status = $status;
		
		$this->_fid = $levelInfo['fid'];
		$this->_level = $levelInfo['level'];
		$this->_lft = $levelInfo['lft'];
		$this->_rgt = $levelInfo['rgt'];
	}
	
	/**
	 * @param string $itemName
	 * @param array $params
	 * @return NULL|boolean
	 */
	public function checkAccess($itemName, $params = array()) {
		if(!$this->getIsMultiLevel()) return null;

	    $cache = Yii::app()->cache;
        $cacheName = 'RedAuthItem.checkAccess.'.CJSON::encode($itemName);
        if(($record = $cache->get($cacheName)) === false){
            if(!is_array($itemName)){
                $condition = 'id=:id';
                $itemName = array('id' => $itemName);
            }else{
                $condition = array();
                foreach($itemName as $key => $value){
                    $condition[] = $key.'=:'.$key;
                }
                $condition = join(' AND ', $condition);
            }

            $record = $this->getAuthManager()->db->createCommand()
                ->select()->from($this->getType())
                ->where($condition,$itemName)
                ->queryRow();

            $cache->set($cacheName, $record, 3600);
        }
        if($record === false) return null;

		return ($record['lft'] <= $this->getLft() && $this->getRgt() <= $record['rgt']);
	}

	/**
	 * @param string $name
	 * @return RedAuthItem 
	 */
	public function addChild($name){
		return $this->_auth->addItemChild($this, $name);
	}
	
	/**
	 * @return RedAuthItem array
	 */
	public function getChild(){
		return $this->_auth->getItemChildren($this);
	}
	
	/**
	 * @return boolean
	 */
	public function removeChild(){
		return $this->_auth->removeItemChild($this,null);
	}
	
	/**
	 * @param integer $id
	 * @return boolean
	 */
	public function hasChild($id){
		return $this->_auth->hasItemChild($this,$id);
	}
	
	/**
	 * @return IAuthManager
	 */
	public function getAuthManager(){
		return $this->_auth;
	}
	
	/**
	 * @return integer
	 */
	public function getId(){
		return $this->_id;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->_name;
	}

	/**
	 * @param string $value
	 * @return RedAuthItem
	 */
	public function setName($value){
		$this->_name = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->_description;
	}

	/**
	 * @param string $value
	 * @return RedAuthItem
	 */
	public function setDescription($value){
		$this->_description = $value;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getStatus() {
		return $this->_status;
	}
	
	/**
	 * @param integer $status
	 * @return RedAuthRole
	 */
	public function setStatus($status) {
		$this->_status = $status;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getFid() {
		return $this->_fid;
	}

	/**
	 * @return integer
	 */
	public function getLevel() {
		return $this->_level;
	}

	/**
	 * @return integer
	 */
	public function getLft() {
		return $this->_lft;
	}

	/**
	 * @return integer
	 */
	public function getRgt() {
		return $this->_rgt;
	}

	/**
	 * @param integer $fid
	 * @return RedAuthItem
	 */
	public function setFid($fid) {
		$this->_fid = $fid;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function update(){
		return $this->_auth->saveAuthItem($this);
	}
	
	/**
	 * @return array
	 */
	public function getOptions(){
		return array(
			'fid' => $this->getFid(),
			'name' => $this->getName(),
			'description' => $this->getDescription(),
			'status' => $this->getStatus()
		);
	}
	
	/**
	 * array_unique
	 * @return string
	 */
	public function __toString(){
		return get_class($this).$this->_id;
	}
	
	abstract public function getType();
	abstract public function getIsMultiLevel();
}