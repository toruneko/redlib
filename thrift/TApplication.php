<?php
/**
 * File: TApplication.php.
 * User: daijianhao@zhubajie.com
 * Date: 14-7-25
 * Time: 下午1:11
 */
class TApplication extends RedWebApplication{

    protected function registerCoreComponents(){
        parent::registerCoreComponents();

        $components=array(
            'thrift'=>array(
                'class'=>'ThriftService',
            ),
        );

        $this->setComponents($components);
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
}