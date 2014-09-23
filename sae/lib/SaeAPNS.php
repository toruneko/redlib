<?php
/**
 * Apple 应用消息推送服务
 *
 * <code>
 * <?php
 * $cert_id = 1;
 * $device_token = "xxxxxxxx xxxxxxxx xxxxxxxx xxxxxxxx xxxxxxxx xxxxxxxx xxxxxxxx xxxxxxxx";
 * 
 * $message = "测试消息";
 * $body = array(
 *     'aps' => array( 'alert' => $message , 'badge' => 1, 'sound' => 'in.caf')
 * );
 * $apns = new SaeAPNS();
 * $result = $apns->push( $cert_id , $body , $device_token );
 * 
 * if( $result && is_array($result) ){
 *     echo '发送成功！';
 *     var_dump( $result );
 * }
 * else {
 *     echo '发送失败。';
 *     var_dump($apns->errno(), $apns->errmsg());
 * }
 * ?>
 * </code>
 *
 * 错误码参考：
 *  - errno: 0         成功
 *  - errno: -1        信息内容为空
 *  - errno: -2        连接server http请求错误
 *  - errno: -3        server端错误
 *
 * @package sae
 *
 */
class SaeAPNS extends SaeObject
{
    private $_accesskey = "";    
    private $_secretkey = "";
    private $_errno = SAE_Success;
    private $_errmsg = "OK";
 
    /**
     * @ignore
     */
    const baseurl = "http://push1.sae.sina.com.cn/server.php";
 
    /**
     * 构造对象
     *
     */
    function __construct() {
        $this->_accesskey = SAE_ACCESSKEY;
        $this->_secretkey = SAE_SECRETKEY;
    }
 
    /**
     * 推送消息
     * 
     * @param int $cert_id  证书序号
     * @param array $body 消息体（包括消息、提醒声音等等），格式请参考Apple官方文档}
     * @param string $device_token 设备令牌
     * @return bool 成功返回true，失败返回false
     */
    function push($cert_id, $body, $device_token) {
        if(!is_array($body) || !isset($body['aps']['alert'])){
            $this->_errmsg = 'body must be an array';
            $this->_errno  = -1;
            return false;
        }
        $post = array();
        $params = array();
        $params['act'] = "push";
        $params['cert_id'] = intval($cert_id);
        $params['device_token'] = trim($device_token);
        $params['ak'] = $this->_accesskey;
        
        $encodings = array( 'UTF-8', 'GBK', 'BIG5' );
        if (is_string($body['aps']['alert'])) {
            $charset = mb_detect_encoding( $body['aps']['alert'] , $encodings);
            if ( $charset !='UTF-8' ) {
                $body['aps']['alert'] = mb_convert_encoding( $body['aps']['alert'], "UTF-8", $charset);
            }
        } else if (is_array($body['aps']['alert'])) {
            if (isset($body['aps']['alert']['body'])) {
                $charset = mb_detect_encoding( $body['aps']['alert']['body'] , $encodings);
                if ( $charset !='UTF-8' ) {
                    $body['aps']['alert']['body'] = mb_convert_encoding( $body['aps']['alert']['body'], "UTF-8", $charset);
                }
            }
        }
        $post['body'] = json_encode($body);
        $ret = $this->postData($post, $params);
        return $ret;
    }
 
    /**
     * 查看当天推送汇总信息
     * 
     * @param int $cert_id  证书序号
     * @return mix 成功json格式汇总信息，失败返回false
     */
    function getInfo($cert_id) {
        $post = array();
        $params = array();
        $params['act'] = "getinfo";
        $params['cert_id'] = intval($cert_id);
        $params['ak'] = $this->_accesskey;
        $ret = $this->postData($post, $params);
        return $ret;
    }
 
    /**
     * 取得错误码
     *
     * @return int 
     * @author Elmer Zhang
     */
    public function errno() {
        return $this->_errno;
    }
 
    /**
     * 取得错误信息
     *
     * @return string 
     * @author Elmer Zhang
     */
    public function errmsg() {
        return $this->_errmsg;
    }
 
    private function postData($post, $params) {
        $url = self::baseurl . '?' . http_build_query( $params );
        $s = curl_init();
        if (is_array($post)) {
            $post = http_build_query($post);
        }
        curl_setopt($s,CURLOPT_URL,$url);
        curl_setopt($s,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0);
        curl_setopt($s,CURLOPT_TIMEOUT,5);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($s,CURLINFO_HEADER_OUT, true);
        curl_setopt($s,CURLOPT_POST,true);
        curl_setopt($s,CURLOPT_POSTFIELDS,$post); 
        $ret = curl_exec($s);
        $info = curl_getinfo($s);
        curl_close($s);
 
        if (empty($info['http_code'])) {
            $this->_errno = -2;
            $this->_errmsg = "can not reach push service server";
        } else if ($info['http_code'] != 200) {
            $code = $info['http_code'];
            $this->_errno = -2;
            $this->_errmsg = "httpd code: $code";
        } else {
            if ($info['size_download'] == 0) { // get MailError header
                $this->_errno = -2;
                $this->_errmsg = "apple push service internal error";
            } else {
                $array = json_decode(trim($ret), true);
                if (is_array($array) && is_int($array['code']) && $array['code'] < 0) {
                    $this->_errno = -3;
                    $this->_errmsg = $array['message'];
                    return false;
                } else if (is_array($array) && is_int($array['code'])) {
                    $this->_errno = SAE_Success;
                    $this->_errmsg = 'OK';
                    return $array;
                } else {
                    $this->_errno = -3;
                    $this->errmsg = "service response error";
                    return false;
                }
            }
        }
        return false;
    }
}