<?php
/**
 * file:RedController.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc: RedController
 */
class RedController extends CController{
	/* 主视图 */
	public $layout='/layouts/main';
	/* 菜单 */
	public $menu=array();
	/* 面包屑 */
	public $breadcrumbs;
	
	/* Yii::app() */
	public $app;
	/* Yii::app()->request */
	public $request;
	/* Yii::app()->user */
	public $user;
	/* script resource */
	public $assets;
    /* client script */
    public $cs;
	/*
	 * @see CController::init()
	*/
	public function init() {
		$this->app = Yii::app();
		$this->request = $this->app->getRequest();
		$this->user = $this->app->getUser();
        $this->cs = $this->app->clientScript;
		$this->assets = Yii::app()->getAssetManager()->getBaseUrl().'/';
	}
	
	/* 
	 * @see CController::accessRules()
	 */
	public function accessRules(){
		$module = $this->getModule();
		return array(
			array('allow','roles' => array(
				array(
					'module' => $module === null ? null : $module->getId(),
					'controller' => $this->getId(),
					'action' => $this->getAction()->getId()),
				),
				'deniedCallback' => array($this,'accessDenied')
			)
		);
	}
	
	public function accessDenied(){
		
	}

	/* 
	 * @see CController::actions()
	 */
	public function actions() {
		$actions = $this->getActions();
		$return = array();
        $module = $this->getModule();
		foreach($actions as $index => $action){
            if(is_numeric($index)){
                if($module === null){
                    $return[$action] = "app.controllers.{$this->id}.".ucfirst($action)."Action";
                }else{
                    $return[$action] = $module->getId().".controllers.{$this->id}.".ucfirst($action)."Action";
                }
            }else{
                $return[$index] = $action;
            }
		}
		return $return;
	}
	
	public function getActions(){
		return array();
	}

	/* 
	 * @see CController::filters()
	 */
	public function filters() {
		$filters = array(
			array('IsAjaxRequest'),
			array('IsGuest'),
		);
		return array_merge($filters,$this->getFilters());
	}
	
	public function getFilters(){
		return array();
	}
	
	/**
	 * 是否允许游客访问
	 * @return boolean
	 */
	public function allowGuest(){
		return true;
	}
	
	/**
	 * 是否允许ajax请求
	 * @return boolean
	 */
	public function allowAjaxRequest(){
		return false;
	}

    /**
     * 是否允许http请求
     * @return boolean
     */
    public function allowHttpRequest(){
        return true;
    }
	
	/**
	 * json返回
	 * @param number $status
	 * @param string $info
	 * @param string $data
	 */
	public function response($status = 200,$info = 'success',$data = null){
		header('Content-Type:application/json; charset=UTF-8');
        if(empty($data)){
            echo CJSON::encode(array('status' => $status,'info' => $info));
        }else{
            echo CJSON::encode(array('status' => $status,'info' => $info,'data' => $data));
        }
	}
}
