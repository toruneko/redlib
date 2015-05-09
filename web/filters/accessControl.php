<?php
/**
 * File: accessControl.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/5/8 21:27
 * Description: 
 */
class accessControl extends CFilter{

    public function preFilter($filterChain){
        $filterChain->action->controller->filterAccessControl($filterChain);
    }
}