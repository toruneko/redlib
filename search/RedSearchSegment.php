<?php
/**
 * File: RedSearchSegment.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/3/30 21:30
 * Description: 分词结果对象
 */
class RedSearchSegment{

    /**
     * @var 关键词
     */
    private $keyword;
    /**
     * @var 词性
     */
    private $class;
    /**
     * @var 出现次数
     */
    private $times = 0;
    /**
     * @var 偏移量数组
     */
    private $indexes = array();

    function __construct($keyword, $class, $index){
        $this->setKeyword($keyword);
        $this->setClass($class);
        $this->addIndex($index);
        $this->addTimes();
    }

    /**
     * @return 关键词
     */
    public function getKeyword(){
        return $this->keyword;
    }

    /**
     * @param 关键词 $keyword
     */
    public function setKeyword($keyword){
        $this->keyword = $keyword;
    }

    /**
     * @return 词性
     */
    public function getClass(){
        return $this->class;
    }

    /**
     * @param 词性 $class
     */
    public function setClass($class){
        $this->class = $class;
    }

    /**
     * @return 出现次数
     */
    public function getTimes(){
        return $this->times;
    }

    /**
     * @param 出现次数 $times
     */
    public function setTimes($times){
        $this->times = $times;
    }

    /**
     * @param int $time
     */
    public function addTimes($time = 1){
        $this->times += $time;
    }

    /**
     * @return 偏移量数组
     */
    public function getIndexes(){
        return $this->indexes;
    }

    /**
     * @param 偏移量数组 $indexes
     */
    public function setIndexes($indexes){
        if(is_array($indexes))
            $this->indexes = $indexes;
    }

    /**
     * @param 偏移量 $index
     */
    public function addIndex($index){
        $this->indexes[] = $index;
    }
}