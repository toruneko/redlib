<?php
/**
 * @file: SaeDbCommand.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-10
 * @desc: This file contains the SaeDbCommand class.
 */
class SaeDbCommand extends CDbCommand{
	public function prepare(){
		if($this->_statement==null){
			try{
				$this->_statement=$this->getConnection()->getPdoInstance($this->getText())->prepare($this->getText());
				$this->_paramLog=array();
			}catch(Exception $e){
				Yii::log('Error in preparing SQL: '.$this->getText(),CLogger::LEVEL_ERROR,'system.db.SaeDbCommand');
				$errorInfo=$e instanceof PDOException ? $e->errorInfo : null;
				throw new CDbException(Yii::t('yii','SaeDbCommand failed to prepare the SQL statement: {error}',
						array('{error}'=>$e->getMessage())),(int)$e->getCode(),$errorInfo);
			}
		}
	}
}