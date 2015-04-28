<?php
/**
 * file:RedActiveRecord.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc: RedActiveRecord
 */
class RedActiveRecord extends CActiveRecord{

    /**
     * 求和
     * @param $field
     * @param string $conditions
     * @param array $params
     * @return mixed
     */
	public function sum($field,$conditions = '',$params = array()){
		$data = $this->find(array(
			'select' => 'SUM(`'.$field.'`) AS `sum`',
			'condition' => $conditions,
			'params' => $params
		));
		return $data->sum;
	}
}