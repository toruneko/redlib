<?php
/**
 * File: RedSearch.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 14/10/31 09:33
 * Description: 
 */
class RedSearch extends CApplicationComponent{
    private $db;
    private $cache;
	public $segment;
    public $table = 'search';
    public $sumRate = 0.6;
    public $countRate = 0.4;
    public $disabled_tags = array();

    public function init(){
        parent::init();

		$app = Yii::app();
        $this->db = $app->db;
        $this->cache = $app->cache;
		
		if(is_array($this->segment)){
			$this->segment = Yii::createComponent($this->segment);
		}
    }

    /**
     * 检索
     * @param $meta
     * @return array
     */
    public function search($meta){
        if($meta instanceof RedSearchMeta){
            $meta = array($meta);
        }
        $words = array();
        foreach($meta as $item){
            $words[] = "'".$item->getKeyword()."'";
        }
        if(empty($words)) return array();

        $cacheKey = md5(CJSON::encode($words));
        if(($return = $this->cache->get($cacheKey)) == false){
            $result = $this->db->createCommand()
                ->select()->from($this->table)
                ->where('keyword IN ('.join(',', $words).')')
                ->queryAll();
            $ids = array();
            foreach($result as $item){
                $ids[] = unserialize($item['ids']);
            }

            $result = array();
            foreach($ids as $item){
                foreach($item as $key => $value){
                    $result[$key][] = $value;
                }
            }
            $ids = array();
            foreach($result as $key => $value){
                $count = count($value);
                $sum = is_array($value) ? array_sum($value) : $value;
                $ids[$key] = $sum * $this->sumRate + $count * $this->countRate;
            }
            arsort($ids);

            $return = array();
            foreach($ids as $key => $value){
                $return[] = $key;
            }

            $this->cache->set($cacheKey, $return, 24 * 3600);
        }

        return $return;
    }

    /**
     * 分词创建meta
     * @param $keyword
     * @param int $id
     * @param bool $discached
     * @return array
     */
    public function createMeta($keyword, $id = 0, $discached = false){
        if(!is_array($keyword)){
            $keyword = array($keyword);
        }
        $cacheKey = md5(CJSON::encode($keyword));
        if($discached || ($meta = $this->cache->get($cacheKey)) == false){
            $words = array();
            do{
                $kw = array_pop($keyword);
                $words = array_merge($words, (array)$this->segment->segment($kw, 1));
            }while(!empty($keyword));

            $meta = array();
            foreach($words as $word){
                $index = trim($word['word']);
                if(empty($index)) continue;
                if(in_array($word['word_tag'], $this->disabled_tags)) continue;

                if(isset($meta[$index])){
                    $obj = &$meta[$index];
                    $obj->setTimes($obj->getTimes() + 1);
                    unset($obj);
                }else{
                    $obj = new RedSearchMeta();
                    $obj->setId($id);
                    $obj->setKeyword($index);
                    $obj->setTimes(1);
                    $obj->setClass($word['word_tag']);
                    $meta[$index] = $obj;
                }
            }

            $this->cache->set($cacheKey, $meta, 24 * 3600);
        }

        return $meta;
    }

    /**
     * 创建索引
     * @param $meta
     * @return bool
     */
    public function commit($meta){
        if($meta instanceof RedSearchMeta){
            $meta = array($meta);
        }
        $transaction = $this->db->beginTransaction();
        try{
            foreach($meta as $item){
                $result = $this->db->createCommand()
                    ->select()->from($this->table)
                    ->where('keyword=:kw', array('kw' => $item->getKeyword()))
                    ->queryRow();
                if($result){
                    $ids = unserialize($result['ids']);
                    if(array_key_exists($item->getId(), $ids)) continue;
                    $ids[$item->getId()] = $item->getTimes();

                    $result = $this->db->createCommand()
                        ->update($this->table, array(
                            'ids' => serialize($ids),
                            'times' => $result['times'] + 1,
                        ), 'keyword=:kw', array('kw' => $item->getKeyword()));
                }else{
                    $result = $this->db->createCommand()
                        ->insert($this->table, $item->toArray());
                }

                if(!$result) throw new Exception('更新索引失败');
            }

            $transaction->commit();
            return true;
        }catch (Exception $e){
            $transaction->rollback();
            return false;
        }
    }

    /**
     * 删除索引 By Meta
     */
    public function deleteByMeta($meta){
        if($meta instanceof RedSearchMeta){
            $meta = array($meta);
        }
        $transaction = $this->db->beginTransaction();
        try{
            foreach($meta as $item){
                $result = $this->db->createCommand()
                    ->delete('keyword=:kw', array(
                        'kw' => $item->getKeyword()
                    ));
                if(!$result) throw new Exception('删除索引失败');
            }

            $transaction->commit();
            return true;
        }catch (Exception $e){
            $transaction->rollback();
            return false;
        }
    }
}
