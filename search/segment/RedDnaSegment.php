<?php
/**
 * File: RedDnaSegment.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/3/20 12:53
 * Description: DNA 分词服务
 */
class RedDNASegment extends CApplicationComponent implements IRedSegment{
    public $wordSize = 12;
    public $read = 1;

    public function segment($text, $mode){
        $len = strlen($text) - $this->wordSize + 1;
        $segment = array();

        for($i = 0; $i < $len; $i += $this->read){
            $segment[] = array(
				'index' => $i,
				'word' => substr($text, $i, $this->wordSize),
				'word_tag' => 999
			);
        }

        return $segment;
    }
}