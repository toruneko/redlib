<?php

/**
 * file:RedHttpRequest.php
 * author:Toruneko@outlook.com
 * date:2014-7-19
 * desc:RedHttpRequest
 */
class RedHttpRequest extends CHttpRequest
{

    /**
     * 对自定义头信息的访问
     * @param string $name
     * @param string $defaultValue
     * @return string
     */
    public function getUserHttp($name, $defaultValue = null)
    {
        $name = strtoupper("HTTP_" . $name);
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $defaultValue;
    }

    /**
     * 获取参数列表
     * @return string
     */
    public function getPostString($post = array())
    {
        $return = array();
        $post = empty($post) ? $_POST : $post;
        foreach ($post as $key => $value) {
            if (is_array($value)) {
                $return[] = $key . '[' . $this->getPostString($value) . ']';
            } else {
                $return[] = $key . '=' . $value;
            }
        }
        return join('&', $return);
    }

    public function getParamString()
    {
        $param = $this->getPostString();
        if (empty($param)) {
            $param = $this->getQueryString();
        }
        return $param;
    }

    public function getRequestUrl()
    {
        $url = $this->getRequestUri();
        $param = $this->getParamString();
        return trim(str_replace($param, '', $url), '?');
    }
}