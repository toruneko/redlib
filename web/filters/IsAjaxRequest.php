<?php
/**
 * file: isAjaxRequest.php
 * author: Toruneko<toruneko@outlook.com>
 * date: 2013-8-31
 * desc: 检查是否是ajax请求
 */
class IsAjaxRequest extends CFilter{
	/*
	 * @see CFilter::preFilter()
	*/
	public function preFilter($filterChain){
		if(Yii::app()->request->getIsAjaxRequest()){
			return $filterChain->controller->allowAjaxRequest();
		}else{
			return $filterChain->controller->allowHttpRequest();
		}
	}
}