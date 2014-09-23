<?php
use Thrift\ClassLoader\ThriftClassLoader;

/**
 * File: TApplication.php.
 * User: daijianhao@zhubajie.com
 * Date: 14-7-25
 * Time: 下午1:11
 */
class TApplication extends CApplication{
    public $defaultController = 'index';
    public $controllerMap=array();
    public $catchAllRequest;
    public $controllerNamespace;

    private $_controllerPath;
    private $_controller;
    /**
     * Processes the request.
     * This is the place where the actual request processing work is done.
     * Derived classes should override this method.
     */
    public function processRequest(){
        if(is_array($this->catchAllRequest) && isset($this->catchAllRequest[0])){
            $route=$this->catchAllRequest[0];
            foreach(array_splice($this->catchAllRequest,1) as $name=>$value)
                $_GET[$name]=$value;
        }else{
            $route=$this->getUrlManager()->parseUrl($this->getRequest());
        }
        $this->runController($route);
    }

    protected function registerCoreComponents(){
        parent::registerCoreComponents();

        $components=array(
            'session'=>array(
                'class'=>'CHttpSession',
            ),
            'user'=>array(
                'class'=>'RedWebUser',
            ),
            'authManager'=>array(
                'class'=>'RedDbAuthManager',
            ),
            'request'=>array(
                'class'=>'RedHttpRequest',
            ),
            'thrift'=>array(
                'class'=>'ThriftService',
            ),
        );

        $this->setComponents($components);
    }

    public function getAssetManager(){}

    public function getAuthManager(){
        return $this->getComponent('authManager');
    }

    public function getSession(){
        return $this->getComponent('session');
    }

    public function getUser(){
        return $this->getComponent('user');
    }

    public function getThrift(){
        return $this->getComponent('thrift');
    }

    public function runController($route){
        if(($ca=$this->createController($route))!==null){
            list($controller,$actionID)=$ca;
            $this->getThrift()->setController($controller);
            $oldController=$this->_controller;
            $this->_controller=$controller;
            $controller->init();
            $controller->run($actionID);
            $this->_controller=$oldController;
        }else{
            throw new CHttpException(404,Yii::t('yii','Unable to resolve the request "{route}".',
                array('{route}'=>$route===''?$this->defaultController:$route)));
        }
    }

    public function createController($route,$owner=null){
        if($owner===null){
            $owner=$this;
        }
        if(($route=trim($route,'/'))===''){
            $route=$owner->defaultController;
        }
        $caseSensitive=$this->getUrlManager()->caseSensitive;
        $route.='/';
        while(($pos=strpos($route,'/'))!==false){
            $id=substr($route,0,$pos);
            if(!preg_match('/^\w+$/',$id)){
                return null;
            }
            if(!$caseSensitive){
                $id=strtolower($id);
            }
            $route=(string)substr($route,$pos+1);
            if(!isset($basePath)){  // first segment
                if(isset($owner->controllerMap[$id])){
                    return array(
                        Yii::createComponent($owner->controllerMap[$id],$id,$owner===$this?null:$owner),
                        $this->parseActionParams($route),
                    );
                }

                if(($module=$owner->getModule($id))!==null){
                    return $this->createController($route,$module);
                }
                $basePath=$owner->getControllerPath();
                $controllerID='';
            }else{
                $controllerID.='/';
            }
            $className=ucfirst($id).'Controller';
            $classFile=$basePath.DIRECTORY_SEPARATOR.$className.'.php';

            if($owner->controllerNamespace!==null){
                $className=$owner->controllerNamespace.'\\'.str_replace('/','\\',$controllerID).$className;
            }

            if(is_file($classFile)){
                if(!class_exists($className,false)){
                    require($classFile);
                }

                if(class_exists($className,false) && is_subclass_of($className,'CBaseController')){
                    $id[0]=strtolower($id[0]);
                    return array(
                        new $className($controllerID.$id,$owner===$this?null:$owner),
                        $this->parseActionParams($route),
                    );
                }
                return null;
            }
            $controllerID.=$id;
            $basePath.=DIRECTORY_SEPARATOR.$id;
        }
    }

    protected function parseActionParams($pathInfo){
        if(($pos=strpos($pathInfo,'/'))!==false){
            $manager=$this->getUrlManager();
            $manager->parsePathInfo((string)substr($pathInfo,$pos+1));
            $actionID=substr($pathInfo,0,$pos);
            return $manager->caseSensitive ? $actionID : strtolower($actionID);
        }else{
            return $pathInfo;
        }
    }

    public function getController(){
        return $this->_controller;
    }

    public function setController($value){
        $this->_controller=$value;
    }

    public function getControllerPath(){
        if($this->_controllerPath!==null){
            return $this->_controllerPath;
        }else{
            return $this->_controllerPath=$this->getBasePath().DIRECTORY_SEPARATOR.'controllers';
        }
    }

    public function setControllerPath($value){
        if(($this->_controllerPath=realpath($value))===false || !is_dir($this->_controllerPath)){
            throw new CException(Yii::t('yii','The controller path "{path}" is not a valid directory.',
                array('{path}'=>$value)));
        }
    }

    public function beforeControllerAction($controller,$action){
        return true;
    }

    public function afterControllerAction($controller,$action){
    }

    public function findModule($id){
        if(($controller=$this->getController())!==null && ($module=$controller->getModule())!==null){
            do{
                if(($m=$module->getModule($id))!==null){
                    return $m;
                }
            } while(($module=$module->getParentModule())!==null);
        }
        if(($m=$this->getModule($id))!==null){
            return $m;
        }
    }

    protected function init(){
        parent::init();
        // preload 'request' so that it has chance to respond to onBeginRequest event.
        $this->getRequest();

        Yii::setPathOfAlias('red', RED_PATH);
        Yii::setPathOfAlias('app',$this->getBasePath());
        Yii::setPathOfAlias('root',dirname($_SERVER['SCRIPT_FILENAME']));
    }

}