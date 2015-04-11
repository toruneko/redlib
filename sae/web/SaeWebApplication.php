<?php
/**
 * @file: SaeWebApplication.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeWebApplication class file.
 */
class SaeWebApplication extends RedWebApplication{

	protected function registerCoreComponents(){
		parent::registerCoreComponents();
		
		$components=array(
			'session'=>array(
				'class'=>'SaeHttpSession',
			),
			/*'assetManager'=>array(
				'class'=>'SaeAssetManager',
			),*/
            'statePersister'=>array(
                'class'=>'SaeStatePersister',
            ),
			'db'=>array(
				'class'=>'SaeDbConnection',
			),
		);
		
		$this->setComponents($components);
	}
}