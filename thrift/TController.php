<?php
/**
 * File: TController.php.
 * User: daijianhao@zhubajie.com
 * Date: 14-7-25
 * Time: 下午1:44
 */
class TController extends CBaseController{
    public $defaultAction='index';

    /* Yii::app() */
    public $app;
    /* Yii::app()->request */
    public $request;
    /* Yii::app()->user */
    public $user;
    /* Yii::app()->thrift */
    public $thrift;

    private $_id;
    private $_action;
    private $_module;

    public function __construct($id,$module=null){
        $this->_id=$id;
        $this->_module=$module;
        $this->attachBehaviors($this->behaviors());
    }

    public function actionIndex(){
        $param = $this->thrift->getRequestParams();
        $header = $this->thrift->getHeader();
        $this->thrift->response(call_user_func_array(array($this, $header['name']), $param));
    }

    /*
     * @see CController::init()
    */
    public function init() {
        $this->app = Yii::app();

        $this->request = $this->app->getRequest();
        $this->user = $this->app->getUser();
        $this->thrift = $this->app->getThrift();
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


    public function actions(){
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

    public function behaviors(){
        return array();
    }

    public function run($actionID){
        if(($action=$this->createAction($actionID))!==null){
            if(($parent=$this->getModule())===null){
                $parent=Yii::app();
            }
            if($parent->beforeControllerAction($this,$action)){
                $this->runActionWithFilters($action,$this->filters());
                $parent->afterControllerAction($this,$action);
            }
        }else{
            $this->missingAction($actionID);
        }
    }

    public function runActionWithFilters($action,$filters){
        if(empty($filters)){
            $this->runAction($action);
        }else{
            $priorAction=$this->_action;
            $this->_action=$action;
            CFilterChain::create($this,$action,$filters)->run();
            $this->_action=$priorAction;
        }
    }

    public function runAction($action){
        $priorAction=$this->_action;
        $this->_action=$action;
        if($this->beforeAction($action)){
            if($action->runWithParams($this->getActionParams())===false){
                $this->invalidActionParams($action);
            }else{
                $this->afterAction($action);
            }
        }
        $this->_action=$priorAction;
    }

    protected function beforeAction($action){
        return true;
    }

    protected function afterAction($action)
    {
    }

    public function getActionParams(){
        return $_GET;
    }

    public function invalidActionParams($action){
        throw new CHttpException(400,Yii::t('yii','Your request is invalid.'));
    }

    public function createAction($actionID){
        if($actionID===''){
            $actionID=$this->defaultAction;
        }
        if(method_exists($this,'action'.$actionID) && strcasecmp($actionID,'s')){ // we have actions method
            return new CInlineAction($this,$actionID);
        }else{
            $action=$this->createActionFromMap($this->actions(),$actionID,$actionID);
            if($action!==null && !method_exists($action,'run')){
                throw new CException(Yii::t('yii', 'Action class {class} must implement the "run" method.', array('{class}'=>get_class($action))));
            }
            return $action;
        }
    }

    protected function createActionFromMap($actionMap,$actionID,$requestActionID,$config=array()){
        if(($pos=strpos($actionID,'.'))===false && isset($actionMap[$actionID])){
            $baseConfig=is_array($actionMap[$actionID]) ? $actionMap[$actionID] : array('class'=>$actionMap[$actionID]);
            return Yii::createComponent(empty($config)?$baseConfig:array_merge($baseConfig,$config),$this,$requestActionID);
        }elseif($pos===false){
            return null;
        }

        // the action is defined in a provider
        $prefix=substr($actionID,0,$pos+1);
        if(!isset($actionMap[$prefix])){
            return null;
        }
        $actionID=(string)substr($actionID,$pos+1);

        $provider=$actionMap[$prefix];
        if(is_string($provider)){
            $providerType=$provider;
        }elseif(is_array($provider) && isset($provider['class'])){
            $providerType=$provider['class'];
            if(isset($provider[$actionID])){
                if(is_string($provider[$actionID])){
                    $config=array_merge(array('class'=>$provider[$actionID]),$config);
                }else{
                    $config=array_merge($provider[$actionID],$config);
                }
            }
        }else{
            throw new CException(Yii::t('yii','Object configuration must be an array containing a "class" element.'));
        }

        $class=Yii::import($providerType,true);
        $map=call_user_func(array($class,'actions'));

        return $this->createActionFromMap($map,$actionID,$requestActionID,$config);
    }

    public function missingAction($actionID){
        throw new CHttpException(404,Yii::t('yii','The system is unable to find the requested action "{action}".',
            array('{action}'=>$actionID==''?$this->defaultAction:$actionID)));
    }

    public function getAction(){
        return $this->_action;
    }

    public function setAction($value){
        $this->_action=$value;
    }

    public function getId(){
        return $this->_id;
    }

    public function getUniqueId(){
        return $this->_module ? $this->_module->getId().'/'.$this->_id : $this->_id;
    }

    public function getRoute(){
        if(($action=$this->getAction())!==null){
            return $this->getUniqueId().'/'.$action->getId();
        }else{
            return $this->getUniqueId();
        }
    }

    public function getModule(){
        return $this->_module;
    }

    public function getViewFile($viewName){
        throw new CException(Yii::t('yii','method getViewFile is invalid in TController'));
    }
}