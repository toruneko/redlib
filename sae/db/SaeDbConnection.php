<?php
/**
 * @file: SaeDbConnection.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeDbConnection class file.
 */
class SaeDbConnection extends CDbConnection{
	public $connectionMaster;
	public $connectionSlave;
	
	private $_slave;
	
	public $driverMap=array(
		'mysqli'=>'CMysqlSchema',   // MySQL
		'mysql'=>'CMysqlSchema',    // MySQL
	);
	
	public function getPdoInstance($query = null){
		if(preg_match('/^\s*(SELECT|SHOW|DESCRIBE|PRAGMA)/i',$query)){
			return $this->_salve;
		}else{
			return parent::getPdoInstance();
		}
	}
	
	protected function open(){
		$this->connectionString = $this->connectionMaster;
		parent::open();
		
		if($this->_slave === null){
			$this->connectionString = $this->connectionSlave;
			try{
				Yii::trace('Opening Slave DB connection','system.db.SaeDbConnection');
				$this->_slave = $this->createPdoInstance();
				$this->initConnection($this->_slave);
			}catch(PDOException $e){
				if(YII_DEBUG){
					throw new CDbException('SaeDbConnection failed to open the Slave DB connection: '.
							$e->getMessage(),(int)$e->getCode(),$e->errorInfo);
				}else{
					Yii::log($e->getMessage(),CLogger::LEVEL_ERROR,'exception.SaeDbException');
					throw new CDbException('SaeDbConnection failed to open the Slave DB connection.',(int)$e->getCode(),$e->errorInfo);
				}
			}
		}
	}
	
	protected function close(){
		parent::close();
		
		Yii::trace('Closing Slave DB connection','system.db.SaeDbConnection');
		$this->_salve = null;
	}
}