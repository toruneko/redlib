<?php
/**
 * File: RedDnaSegment.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/3/20 12:53
 * Description: DNA 分词服务
 */
class RedDnaSegment extends CApplicationComponent{
    public $wordSize;
    public $read;
    public $lexicon;
    private $cache;

    public function init(){
        parent::init();

        $this->cache = Yii::app()->cache;
    }

    private function getWordSize(){
        if(empty($this->wordSize)){
            $this->wordSize = 12;
        }
        return $this->wordSize;
    }

    private function getRead(){
        if(empty($this->read)){
            $this->read = 1;
        }
        return $this->read;
    }

    private function getLexicon(){
        if(empty($this->lexicon) || !file_exists($this->lexicon)){
            $this->lexicon = array();
        }else{
            if(($res = $this->cache->get($this->lexicon)) == false){
                $file = file_get_contents($this->lexicon);
                $res = explode("\n",$file);

                $this->cache->set($this->lexicon, $res);
            }
            $this->lexicon = $res;
        }
    }

    public function segment($dna){
        $wordSize = $this->getWordSize();
        $read = $this->getRead();
        //$lexicon = $this->getLexicon();
        $len = strlen($dna) - $wordSize;
        $segment = array();

        for($i = 0; $i < $len; $i += $read){
            $segment[] = substr($dna, $i, $wordSize);
        }

        return $segment;
    }
}