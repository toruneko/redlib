<?php

/**
 * File: BaeWebApplication.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 16/4/6 23:24
 * Description:
 */
class BaeWebApplication extends RedWebApplication
{
    protected function registerCoreComponents()
    {
        parent::registerCoreComponents();

        $components = array(
            'statePersister' => array(
                'class' => 'BaeStatePersister',
            ),
            'session' => array(
                'class' => 'CCacheHttpSession'
            ),
        );

        $this->setComponents($components);
    }
}