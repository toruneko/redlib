<?php
/**
 * SAE Channel 服务
 * demo 地址：{@link https://github.com/xiaosier/sae-php-channel}
 *
 * <code>
 * <?php
 * $channel = new SaeChannel();
 * $connection = $channel->createChannel('test',100);
 * $message_content = 'hello,sae';
 * // Send message
 * $ret = $channel->sendMessage('test',$message_content);
 *
 * ?>
 * </code>
 *
 * 错误码参考：
 *  - errno: 0         成功
 *  - errno: 101     参数错误
 *  - errno: 500     服务内部错误
 *
 */
 
class SaeChannel extends SaeObject
{
    const end_ = 'http://channel.sae.sina.com.cn/v1/';
    const MAXIMUM_CLIENT_ID_LENGTH_ = 256;
    const MAXIMUM_TOKEN_DURATION_SECONDS_ = 1440;
    const MAXIMUM_MESSAGE_LENGTH_ = 4096;
    private $_accesskey  = '';
    private $_secretkey  = '';
    private $errMsg     = 'success';
    private $errNum     = 0;
 
    /**
     */
    function __construct()
    {
        $this->_accesskey = SAE_ACCESSKEY;
        $this->_secretkey = SAE_SECRETKEY;
    }
 
    /**
     * 创建一个channel信道
     *
     * @param string $name channel的名称
     * @param int $duration channel的有效时间，单位为秒，默认为1小时
     * @return string 
     * @author Lazypeople
     */
    public function createChannel( $name, $duration = 3600)
    {
        $check_ret = $this->check_client_id($name) && $this->check_duration($duration);
        if ( !$check_ret ) {
            return false;
        }
        $request_url = SaeChannel::end_.'create_channel';
        $post_data = array('client_id'=>$name, 'duration'=>$duration);
    $post_data = http_build_query($post_data);
        $ret = $this->postData( $request_url, $post_data);
        return $ret;
    }
 
    /**
     * 发送一条消息
     *
     * 上行发送一条消息,向客户端推送消息,最大可以发送4k的消息，最好不要直接发送二进制的数据。
     *
     * @param string $name channel的名称
     * @param string $message 需要发送的消息内容
     * @return boolean 
     * @author Lazypeople
     */
    public function sendMessage( $name, $message)
    {
        $check_ret = $this->check_client_id($name) && $this->check_message($message);
        if ( !$check_ret ) {
            return false;
        }
        $request_url = SaeChannel::end_.'send_message';
        $post_data = array('client_id'=>$name, 'message'=>$message);
    $post_data = http_build_query($post_data);
        $ret = $this->postData( $request_url, $post_data );
        return $ret&&true;
    }
 
    /**
     * 取得错误信息
     *
     * @return string 
     * @author Lazypeople
     */
    public function errmsg()
    {
        $ret = $this->errMsg;
        $this->errMsg = 'success';
        return $ret;
    }
 
    /**
     * 取得错误码
     *
     * @return int 
     * @author Lazypeople
     */
    public function errno()
    {
        $ret = $this->errNum;
        $this->errNum = 0;
        return $ret;
    }
 
    /**
     * @ignore
     */
    private function set_error( $errno, $errmsg )
    {
        $this->errNum = $errno;
        $this->errMsg = $errmsg;
    }
 
    /**
     * @ignore
     */
    private function check_client_id( $client_id )
    {
        if ( strlen( $client_id ) > SaeChannel::MAXIMUM_CLIENT_ID_LENGTH_ ) {
            $errmsg = sprintf('Client id length %d is greater than max length %d',strlen($client_id), SaeChannel::MAXIMUM_CLIENT_ID_LENGTH_ );
            $this->set_error( 101 ,$errmsg);
            return false;
        }
        return true;
    }
 
    /**
     * @ignore
     */
    private function check_duration( $duration ) 
    {
        if (!$duration) {
            $errmsg = NULL;
            if ($duration < 1) {
                $errmsg = 'Argument duration must not be less than 1';
            } elseif (strlen($duration) > SaeChannel::MAXIMUM_TOKEN_DURATION_SECONDS_) {
                $errmsg = sprintf('Argument duration must be less than %d',(SaeChannel::MAXIMUM_TOKEN_DURATION_SECONDS_ + 1));
            }
            if ( !is_null($errmsg)) {
                $this->set_error(101,$errmsg);
                return false;
            }
        }
        return true;
    }
 
    /**
     * @ignore
     */
    private function check_message( $message ) 
    {
        $errmsg = NULL;
        if ( is_null($message) ) {
            $errmsg = 'Argument message must not be null';
        } elseif ( strlen($message) > SaeChannel::MAXIMUM_MESSAGE_LENGTH_) {
            $errmsg = sprintf('Message must be no longer than %d chars' ,SaeChannel::MAXIMUM_MESSAGE_LENGTH_);
        }
        if (!is_null($errmsg)) {
            $this->set_error(101,$errmsg);
            return false;
        }
        return true;
    }
 
    /**
     * @ignore
     */
    private function postData( $url, $post ) 
    {
        $s = curl_init();
        curl_setopt($s,CURLOPT_URL,$url);
        curl_setopt($s,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0);
        curl_setopt($s,CURLOPT_TIMEOUT,5);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($s,CURLOPT_HEADER, 0);
        curl_setopt($s,CURLINFO_HEADER_OUT, true);
        curl_setopt($s,CURLOPT_HTTPHEADER, $this->genReqestHeader($post));
        curl_setopt($s,CURLOPT_POST,true);
        curl_setopt($s,CURLOPT_POSTFIELDS,$post);
        $ret = curl_exec($s);
        $info = curl_getinfo($s);
        curl_close($s);
        if ( empty($info['http_code']) ) {
            $errmsg = "Channel service segment fault";
            $this->set_error( SAE_ErrInternal, $errmsg);
        } else if($info['http_code'] != 200) {
            $errmsg = "Channel service internal error";
            $this->set_error( SAE_ErrInternal, $errmsg);
        } else {
            $ret = json_decode($ret,true);
            if ($ret && array_key_exists('data',$ret)) {
                return $ret['data'];
            } else {
                $this->set_error(102,$ret['error']);
            }
            return $ret['data'];
        }
        return false;
    }
 
    /**
     * @ignore
     */
    private function genSignature($content, $secretkey) 
    {
        $sig = base64_encode(hash_hmac('sha256',$content,$secretkey,true));
        return $sig;
    }
 
    /**
     * @ignore
     */
    private function genReqestHeader($post)
    {
        $timestamp = date('Y-m-d H:i:s');
        $cont1 = "ACCESSKEY".$this->_accesskey."TIMESTAMP".$timestamp;
        $reqhead = array("TimeStamp: $timestamp","AccessKey: ".$this->_accesskey, "Signature: " . $this->genSignature($cont1, $this->_secretkey));
        return $reqhead;
    }
}