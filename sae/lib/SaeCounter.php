<?php
/**
 * SAE邮件服务
 *
 * @package sae
 * @version $Id$
 * @author lijun
 */


/**
 * SAE计数器服务
 *
 * <code>
 * <?php
 * $c = new SaeCounter();
 * $c->create('c1');  //创建计数器c1 创建成功返回true 如果该名字已被占用将返回false
 * $c->set('c1',100); // 返回true
 * $c->incr('c1'); // 返回101
 * $c->get('c1'); // 返回c1的值101
 * $c->decr('c1'); // 返回100
 * ?>
 * </code>
 *
 * @author  chenlei
 * @package sae
 */
class SaeCounter extends SaeObject
{
    /**
     * 构造函数
     */
    public function __construct()
    {
    }

    /**
     * 增加一个计数器
     *
     * @param string $name 计数器名称
     * @param int $value 计数器初始值，默认值为0
     * @return bool 成功返回true，失败返回false（计数器已存在返回false）
     */
    public function create($name, $value = 0)
    {
    }

    /**
     * 删除一个计数器
     *
     * @param string $name 计数器名称
     * @return bool 成功返回true，失败返回false（计数器不存在返回false）
     */
    public function remove($name)
    {
    }

    /**
     * 判断一个计数器是否存在
     *
     * @param string $name 计数器名称
     * @return bool 存在返回true，不存在返回false
     */
    public function exists($name)
    {
    }

    /**
     * 获取当前应用的所有计数器数据
     *
     * @return array|bool成功返回数组array，失败返回false
     */
    public function listAll()
    {
    }

    /**
     * 获取当前应用的计数器个数
     *
     * @return int|bool成功返回计数器个数，失败返回false
     */
    public function length()
    {
    }

    /**
     * 获取指定计数器的值
     *
     * @param string $name 计数器名称
     * @return int|bool成功返回该计数器的值，失败返回false
     */
    public function get($name)
    {
    }

    /**
     * 重新设置指定计数器的值
     *
     * @param string $name 计数器名称
     * @param int $value 计数器的值
     * @return bool 成功返回true，失败返回false
     */
    public function set($name, $value)
    {
    }

    /**
     * 同时获取多个计数器值
     *
     * @param array $names 计数器名称数组，array($name1, $name2, ...)
     * @return array|bool成功返回关联数组，失败返回false
     */
    public function mget($names)
    {
    }

    /**
     * 获取当前应用所有计数器的值
     *
     * @return array|bool成功返回关联数组，失败返回false
     */
    public function getall()
    {
    }

    /**
     * 对指定计数器做加法操作
     *
     * @param string $name 计数器名称
     * @param int $value 计数器增加值
     * @return int|bool成功返回该计数器的当前值，失败返回false（计数器不存在返回false）
     */
    public function incr($name, $value = 1)
    {
    }

    /**
     * 对指定计数器做减法操作
     *
     * @param string $name 计数器名称
     * @param int $value 计数器减少值
     * @return int|bool成功返回该计数器的当前值，失败返回false（计数器不存在返回false）
     */
    public function decr($name, $value = 1)
    {
    }
}