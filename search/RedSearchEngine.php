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

    public $dbId = 'db';
    public $cacheId = 'cache';
    public $redisId = 'redis';

	public $segment;
    public $table = 'search';
    public $indexCachingDuration = 86400;
    public $disabled_tags = array();

    public function init(){
        parent::init();

		$app = Yii::app();
        $this->db = $app->getComponent($this->dbId);
        $this->cache = $app->getComponent($this->cacheId);
        $this->redis = $app->getComponent($this->redisId);
		
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
        $cachedIndex = array();

        /**
         * 从缓存中检索
         */
        $words = array();
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
            foreach($result as $item){
                $item['index'] = CJSON::decode($item['index']);
                $cachedIndex[$item['keyword']] = $item['index'];

                $this->redis->set($item['keyword'], $item['index']);
            }
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
        $ids = array();
        $offset = array();
        foreach($cachedIndex as $keyword => $indexes){
            $idf = log10($totalPage / count($indexes));
            foreach($indexes as $docId => $index){
                $offset[$docId] = $index['indexes'];
                $tf = $index['times'] / $index['textLen'];
                if(isset($tf_idf[$docId])){
                    $ids[$docId] += $tf * $idf;
                }else{
                    $ids[$docId] = $tf * $idf;
                }
            }
        }

        arsort($ids);

        return array(
            'ids' => array_keys($ids),
            'offset' => $offset
        );
    }

    /**
     * 创建索引
     * @param RedSearchQuery $query
     * @return bool
     */
    public function commit(RedSearchQuery $query){
        $segment = $query->getSegment();
        $docId = $query->getId();
        $textLen = $query->getTextLength();

        $transaction = $this->db->beginTransaction();
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
        $segment = $query->getSegment();
        $docId = $query->getId();

        $transaction = $this->db->beginTransaction();
        try{
            foreach($segment as $item){
                $keyword = $item->getKeyword();

                $result = $this->db->createCommand()
                    ->select()->from($this->table)
                    ->where('keyword=:kw', array('kw' => $keyword))
                    ->queryRow();
                $index = CJSON::decode($result['index']);
                if(isset($index[$docId]))unset($index[$docId]);

                if(empty($index)){
                    $res = $this->db->createCommand()
                        ->delete($this->table, 'keyword=:kw', array('kw' => $keyword));
                    if($res)
                        $this->redis->delete($item->getKeyword());
                }else{
                    $res = $this->db->createCommand()
                        ->update($this->table, array(
                            'index' => CJSON::encode($index),
                        ), 'keyword=:kw', array('kw' => $keyword));
                    if($res)
                        $this->redis->set($keyword, $index);
                }

                if(!$res) throw new Exception('删除索引失败');
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
            if(!$discached){
                $this->cache->set($cacheKey, $query, $this->indexCachingDuration);
            }
        }

        return $query;
    }
}
