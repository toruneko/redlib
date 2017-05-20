<?php

/**
 * File: SaeQueue.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/1/15 21:30
 * Description: Sae 队列
 */
class SaeQueue extends CApplicationComponent
{
    private $queue = null;
    public $name;
    public $accesskey;
    public $secretkey;

    public function init()
    {
        parent::init();

        $this->queue = new SaeTaskQueue($this->name);
        if (!empty($this->accesskey) && !empty($this->secretkey)) {
            $this->queue->setAuth($this->accesskey, $this->secretkey);
        }
    }

    /**
     * 添加任务
     * @param $tasks
     * @param null $postdata
     * @param bool $prior
     * @param array $options
     * @return mixed
     */
    public function addTask($tasks, $postdata = NULL, $prior = false, $options = array())
    {
        return $this->queue->addTask($tasks, $postdata, $prior, $options);
    }

    /**
     * 加入队列
     * @return mixed
     */
    public function push()
    {
        return $this->queue->push();
    }

    /**
     * 剩余长度
     * @return mixed
     */
    public function leftLength()
    {
        return $this->queue->leftLength();
    }

    /**
     * 当前任务数
     * @return mixed
     */
    public function curLength()
    {
        return $this->queue->curLength();
    }

    /**
     * 错误码
     * @return mixed
     */
    public function errno()
    {
        return $this->queue->errno();
    }

    /**
     * 错误信息
     * @return mixed
     */
    public function errmsg()
    {
        return $this->queue->errmsg();
    }
}