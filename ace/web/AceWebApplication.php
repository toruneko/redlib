<?php

/**
 * @file: SaeWebApplication.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeWebApplication class file.
 */
class AceWebApplication extends RedWebApplication
{

    protected function registerCoreComponents()
    {
        parent::registerCoreComponents();

        $components = array(
            'session' => array(
                'class' => 'AceHttpSession',
            ),
        );

        $this->setComponents($components);
    }
}