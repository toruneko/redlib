<?php
/**
 * File: RedSearch.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 14/10/31 09:33
 * Description: 
 */
class RedSearchEngine extends CApplicationComponent{
    private $db;
    private $cache;
    private $redis;

	public $segment;
    public $table = 'search';
    public $indexCachingDuration = 86400;
    public $disabled_tags = array();

    public function init(){
        parent::init();

		$app = Yii::app();
        $this->db = $app->getComponent('db');
        $this->cache = $app->getComponent('cache');
        $this->redis = $app->getComponent('redis');
		
		if(is_array($this->segment)){
			$this->segment = Yii::createComponent($this->segment);
		}
    }

    /**
     * 检索
     * @param RedSearchQuery $query
     * @return array
     */
    public function search(RedSearchQuery $query){
        $segment = $query->getSegment();

        /**
         * 从缓存中检索
         */
        $words = array();
        $cachedIndex = array();
        foreach($segment as $item){
            $keyword = $item->getKeyword();
            if(($result = $this->redis->get($keyword)) == false){
                $words[] = "'".$keyword."'";
            }else{
                $cachedIndex[$keyword] = $result;
            }
        }

        /**
         * 从数据库中检索
         */
        if(!empty($words)){
            $result = $this->db->createCommand()
                ->select()->from($this->table)
                ->where('keyword IN ('.join(',', $words).')')
                ->queryAll();
            $indexes = array();
            foreach($result as $item){
                $item['index'] = CJSON::decode($item['index']);
                $this->redis->set($item['keyword'], $item['index']);

                $indexes[$item['keyword']] = $item['index'];
            }
            $cachedIndex = array_merge($cachedIndex, $indexes);
        }
        if(empty($cachedIndex)) return array();

        /**
         * 获取本次检索中的最大文件频率数作为总文件数
         */
        $totalPage = 0;
        foreach($cachedIndex as $indexes){
            $count = count($indexes);
            if($totalPage < $count){
                $totalPage = $count;
            }
        }

        /**
         * 计算TF-IDF
         */
        $tf_idf = array();
        foreach($cachedIndex as $keyword => $indexes){
            $idf = log10($totalPage / count($indexes));
            foreach($indexes as $docId => $index){
                $tf = $index['times'] / $index['textLen'];
                $tf_idf[$docId][] = $tf * $idf;
            }
        }

        /**
         * 求和并排序
         */
        $ids = array();
        foreach($tf_idf as $docId => $item){
            $ids[$docId] = array_sum($item);
        }
        arsort($ids);

        return array_keys($ids);
    }

    /**
     * 创建索引
     * @param RedSearchQuery $query
     * @return bool
     */
    public function commit(RedSearchQuery $query){
        $transaction = $this->db->beginTransaction();
        $segment = $query->getSegment();
        $docId = $query->getId();
        $textLen = $query->getTextLength();
        try{
            foreach($segment as $item){
                $keyword = $item->getKeyword();
                $times = $item->getTimes();
                $indexes = $item->getIndexes();

                $result = $this->db->createCommand()
                    ->select()->from($this->table)
                    ->where('keyword=:kw', array('kw' => $keyword))
                    ->queryRow();

                if($isNewKeyword = empty($result)){
                    $result = array(
                        'keyword' => $keyword,
                        'index' => '[]'
                    );
                }

                $index = CJSON::decode($result['index']);
                if(array_key_exists($docId, $index)) continue;
                $index[$docId] = array(
                    'textLen' => $textLen,
                    'times' => $times,
                    'indexes' => $indexes
                );
                $result['index'] = CJSON::encode($index);

                if($isNewKeyword){
                    $res = $this->db->createCommand()
                        ->insert($this->table, $result);
                }else{
                    $res = $this->db->createCommand()
                        ->update($this->table, array(
                            'index' => $result['index'],
                        ), 'keyword=:kw', array('kw' => $result['keyword']));
                }

                if(!$res) throw new Exception('更新索引失败');
                $this->redis->set($keyword, $index);
            }

            $transaction->commit();
            return true;
        }catch (Exception $e){
            $transaction->rollback();
            return false;
        }
    }

    /**
     * 删除索引
     * @param RedSearchQuery $query
     * @return bool
     */
    public function delete(RedSearchQuery $query){
        $transaction = $this->db->beginTransaction();
        $segment = $query->getSegment();
        try{
            foreach($segment as $item){
                $result = $this->db->createCommand()
                    ->delete('keyword=:kw', array(
                        'kw' => $item->getKeyword()
                    ));
                if(!$result) throw new Exception('删除索引失败');
                $this->redis->delete($item->getKeyword());
            }

            $transaction->commit();
            return true;
        }catch (Exception $e){
            $transaction->rollback();
            return false;
        }
    }

    /**
     * 创建搜索请求对象
     * @param $text
     * @param int $id
     * @param bool $discached
     * @return array
     */
    public function createSearchQuery($text, $id = 0, $discached = false){
        if(!is_array($text)) $text = array($text);

        $cacheKey = md5(CJSON::encode($text));
        if($discached || ($query = $this->cache->get($cacheKey)) == false){
            $words = array();
            foreach($text as $kw){
                $words = array_merge($words, (array)$this->segment->segment($kw, 1));
            }

            $segment = array();
            foreach($words as $word){
                $keyword = trim($word['word']);
                if(empty($keyword)) continue;
                if(in_array($word['word_tag'], $this->disabled_tags)) continue;

                if(isset($segment[$keyword])){
                    $segment[$keyword]->addTimes();
                    $segment[$keyword]->addIndex($word['index']);
                }else{
                    $segment[$keyword] = new RedSearchSegment($keyword, $word['word_tag'], $word['index']);
                }
            }

            $query = new RedSearchQuery($text, $id, $segment);
            $this->cache->set($cacheKey, $query, $this->indexCachingDuration);
        }

        return $query;
    }
}
