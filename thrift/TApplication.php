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
        }
        parent::runController($route);
    }
}