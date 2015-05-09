<?php
/**
 * File: RedDnaSegment.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/3/20 12:53
 * Description: DNA 分词服务
 */
class RedDNASegment extends CApplicationComponent implements IRedSegment{

    public function segment($text, $mode){
        $len = strlen($text) - 12 + 1;
        $segment = array();

        for($i = 0; $i < $len; $i++){
            $segment[] = array(
				'index' => $i,
				'word' => substr($text, $i, 12),
				'word_tag' => 999
			);
        }

        return $segment;
    }
}