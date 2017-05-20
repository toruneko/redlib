<?php
use Thrift\Transport\TSocket;
use Thrift\Transport\TFramedTransport;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Serializer\TBinarySerializer;
use Thrift\Exception\TException;
require_once("logchain/logchain.php");
require_once("logchain/Types.php");

class NetLog {

    public function __construct($secret)
    {
        $this->_secret = $secret;
        $host = getenv('BAE_ENV_LOG_HOST');
        if(!empty($host))
        {
            $this->_host = $host;
        }else{
            $this->_host = BAE_ENV_LOG_HOST;
        }
        $port = getenv('BAE_ENV_LOG_PORT');
        if(!empty($port))
        {
            $this->_port = $port;
        }else{
            $this->_port = BAE_ENV_LOG_PORT;
        }
        $appid = getenv('BAE_ENV_APPID');
        if(!empty($appid))
        {
            $this->_appid = $appid;
        }else{
            $this->_appid = BAE_ENV_APPID;
        }        
    }

    public function __destruct()
    {
        $this->_transport->close();
    }
    
    public function netlog_open()
    {
        $flag = false;
        try {
            $this->_socket = new TSocket($this->_host, $this->_port);  
            $this->_transport = new TFramedTransport($this->_socket);  
            $this->_protocol = new TBinaryProtocol($this->_transport);  
            $this->_client = new logchain\logchainClient($this->_protocol);  
            $this->_transport->open(); 
            $flag = true;
        } catch (TException $tx) {

        }

        return $flag;
    }
    
    public function netlog_send($level, $msg, $tag)
    {
        $appid = $this->_appid;
        try {
            $current_time = round(microtime(true) * 1000);
            $content = array('appid'=>$appid, 
                             'timestamp'=>$current_time,
                             'msg'=>$msg, 
                             'level'=>$level);
            if($tag !== null && $tag !== '')
            {
                $content['tag'] = $tag;
            }
                             
            $userEntry = new logchain\UserLogEntry($content);
            $logBin= TBinarySerializer::serialize($userEntry);    
            $log['content'] = $logBin; 
            $log['category'] = 'user';

            $logEntry = new logchain\BaeLogEntry($log); 
            $this->msgArray[] = $logEntry;

            $baeLog['messages'] = $this->msgArray;
            $secInfo['user'] = $this->_secret['user'];
            $secInfo['passwd'] = $this->_secret['passwd'];
            $secret = new logchain\SecretEntry($secInfo);
            $baeLog['secret'] = $secret;
            $baeEntry = new logchain\BaeLog($baeLog);
            
            $result = $this->_client->log($baeEntry);
            $this->_code = $result;
            if($result == logchain\BaeRet::OK)
            {
                $this->msgArray = array();
                return true; 
            }

            if(count($this->msgArray) >=256)
            {
                $this->msgArray = array();
            }
            
            return false;           
        } catch (TException $tx) {
            return false;
        }
    }

    public function netlog_code()
    {
        $errorCode = $this->_code;
        return $errorCode;
    }
    
    private $_appid;
    private $_code;
    private $_host;
    private $_port;
    private $_socket;
    private $_transport;
    private $_protocol;
    private $_client;
    private $_secret;
    private $msgArray = array();
}
