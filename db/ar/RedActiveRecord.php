<?php
/**
 * file:RedActiveRecord.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc: RedActiveRecord
 */
class RedActiveRecord extends CActiveRecord{

    public $_sum;

	/**
	 * 求和
	 * @param string $field
	 * @param string $conditions
	 * @param array $params
	 */
	public function sum($field,$conditions = '',$params = array()){
		$data = $this->find(array(
			'select' => 'SUM(`'.$field.'`) AS `_sum`',
			'condition' => $conditions,
			'params' => $params
		));
		return $data->_sum;
	}
}