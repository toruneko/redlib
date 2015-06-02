<?php
/**
 * File: TController.php.
 * User: daijianhao@zhubajie.com
 * Date: 14-7-25
 * Time: 下午1:44
 */
class TController extends RedController{
    /* Yii::app()->thrift */
    public $thrift;

    public function actionIndex(){
        $param = $this->thrift->getRequestParams();
        $header = $this->thrift->getHeader();
        $this->thrift->response(call_user_func_array(array($this, $header['name']), $param));
    }

    /*
     * @see CController::init()
    */
    public function init() {
        parent::init();

        $this->thrift = $this->app->getThrift();
    }

    /*
     * @see CController::accessRules()
     */
    public function accessRules(){
        return array();
    }


    public function actions(){
        return array();
    }

    /*
     * @see CController::filters()
     */
    public function filters() {
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
        return true;
    }

    /**
     * 是否允许http请求
     * @return boolean
     */
    public function allowHttpRequest(){
        return true;
    }

    public function behaviors(){
        return array();
    }
}