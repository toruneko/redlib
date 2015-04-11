<?php
/**
 * File: IRedSegment.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/3/29 09:34
 * Description: 分词器接口
 */
interface IRedSegment{
    public function segment($text, $mode);
}