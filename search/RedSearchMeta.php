<?php
/**
 * File: RedSearchMeta.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 14/10/31 09:36
 * Description: 搜索元信息
 */
class RedSearchMeta extends CApplicationComponent{
    private $id;
    private $keyword;
    private $class;
    private $times;

    /**
     * @return mixed
     */
    public function getTimes(){
        return !empty($this->times) ? $this->times : 1;
    }

    /**
     * @param mixed $times
     */
    public function setTimes($times){
        $this->times = $times;
    }

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id){
        $this->id = $id;
    }

    public function getIds(){
        return serialize(array(
            $this->getId() => $this->getTimes()
        ));
    }

    /**
     * @return mixed
     */
    public function getKeyword(){
        return $this->keyword;
    }

    /**
     * @param mixed $keyword
     */
    public function setKeyword($keyword){
        $this->keyword = $keyword;
    }

    /**
     * @return mixed
     */
    public function getClass(){
        return !empty($this->class) ? $this->class : 0;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class){
        $this->class = $class;
    }

    public function toArray(){
        return array(
            'keyword' => $this->getKeyword(),
            'class' => $this->getClass(),
            'ids' => $this->getIds(),
            'times' => 1
        );
    }
}