<?php
/**
 * File: SaeTimer.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/1/15 21:53
 * Description: 计数器
 */
class SaeTimer extends CApplicationComponent{
    private $counter;

    public function init(){
        parent::init();

        $this->counter = new SaeCounter();
    }

    /**
     * 增加一个计数器
     *
     * @param string $name 计数器名称
     * @param int $value 计数器初始值，默认值为0
     * @return bool 成功返回true，失败返回false（计数器已存在返回false）
     */
    public function create($name, $value = 0){
        return $this->counter->create($name, $value);
    }

    /**
     * 删除一个计数器
     *
     * @param string $name 计数器名称
     * @return bool 成功返回true，失败返回false（计数器不存在返回false）
     */
    public function remove($name){
        return $this->counter->remove($name);
    }

    /**
     * 判断一个计数器是否存在
     *
     * @param string $name 计数器名称
     * @return bool 存在返回true，不存在返回false
     */
    public function exists($name){
        return $this->counter->exists($name);
    }

    /**
     * 获取当前应用的所有计数器数据
     *
     * @return array|bool成功返回数组array，失败返回false
     */
    public function listAll(){
        return $this->counter->listAll();
    }

    /**
     * 获取当前应用的计数器个数
     *
     * @return int|bool成功返回计数器个数，失败返回false
     */
    public function length(){
        return $this->counter->length();
    }

    /**
     * 获取指定计数器的值
     *
     * @param string $name 计数器名称
     * @return int|bool成功返回该计数器的值，失败返回false
     */
    public function get($name){
        return $this->counter->get($name);
    }

    /**
     * 重新设置指定计数器的值
     *
     * @param string $name 计数器名称
     * @param int $value 计数器的值
     * @return bool 成功返回true，失败返回false
     */
    public function set($name, $value){
        return $this->counter->set($name, $value);
    }

    /**
     * 同时获取多个计数器值
     *
     * @param array $names 计数器名称数组，array($name1, $name2, ...)
     * @return array|bool成功返回关联数组，失败返回false
     */
    public function mget($names){
        return $this->counter->mget($names);
    }

    /**
     * 获取当前应用所有计数器的值
     *
     * @return array|bool成功返回关联数组，失败返回false
     */
    public function getAll(){
        return $this->counter->getall();
    }

    /**
     * 对指定计数器做加法操作
     *
     * @param string $name 计数器名称
     * @param int $value 计数器增加值
     * @return int|bool成功返回该计数器的当前值，失败返回false（计数器不存在返回false）
     */
    public function incr($name, $value = 1){
        return $this->counter->incr($name, $value);
    }

    /**
     * 对指定计数器做减法操作
     *
     * @param string $name 计数器名称
     * @param int $value 计数器减少值
     * @return int|bool成功返回该计数器的当前值，失败返回false（计数器不存在返回false）
     */
    public function decr($name, $value = 1){
        return $this->counter->decr($name, $value);
    }
}