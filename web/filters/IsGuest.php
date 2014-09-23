<?php
/**
 * @file isGuestLogin.php
 * @author Toruneko<toruneko.dai@gmail.com>
 * @date 2013-8-7
 * @description 验证是否是游客访问
 */
class IsGuest extends CFilter{
	/* 
	 * @see CFilter::preFilter()
	 */
	protected function preFilter($filterChain) {
		if(Yii::app()->user->isGuest){
			return $filterChain->controller->allowGuest();
		}else{
			return true;
		}
	}
}