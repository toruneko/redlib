<?php
/**
 * File: RedSearchQuery.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/3/30 21:25
 * Description: 搜索请求
 */
class RedSearchQuery{

    /**
     * @var 原始文本
     */
    private $text;

    /**
     * @var 文本ID
     */
    private $id;

    /**
     * @var 分词结果对象
     */
    private $segment = array();

    function __construct($text, $id, $segment){
        $this->setText($text);
        $this->setId($id);
        $this->setSegment($segment);
    }

    /**
     * @return 分词结果对象
     */
    public function getSegment(){
        return $this->segment;
    }

    public function addSegment($keyword, RedSearchSegment $segment){
        $this->segment[$keyword] = $segment;
    }

    /**
     * @param 分词结果对象 $segment
     */
    private function setSegment($segment){
        $this->segment = $segment;
    }

    /**
     * @return 原始文本
     */
    public function getText(){
        return $this->text;
    }

    public function getTextLength(){
        $len = 0;
        foreach($this->text as $text){
            $len += (strlen($text) + 12 - 1);
        }
        return $len;
    }

    /**
     * @param 原始文本 $text
     */
    private function setText($text){
        $this->text = $text;
    }

    /**
     * @return 文本ID
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param 文本ID $id
     */
    private function setId($id){
        $this->id = $id;
    }
}