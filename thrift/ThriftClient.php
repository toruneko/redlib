<?php
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\THttpClient;

/**
 * File: ThriftClient.php.
 * User: daijianhao@zhubajie.com
 * Date: 14-7-28
 * Time: 上午10:50
 */
class ThriftClient extends CApplicationComponent{
    private $_output = null;
    private $_parse_url = array();
    private $_client = null;
    private $_controller = null;

    public $service;

    public function init(){
        parent::init();

        $this->_controller = Yii::app()->getController();
    }

    public function getOutput($serviceUrl) {
        if($this->_output === null){
            $parse = $this->parseUrl($serviceUrl);
            $socket = new THttpClient($parse['host'], $parse['port'], $parse['path'], $parse['scheme']);
            $transport = new TBufferedTransport($socket, 1024, 1024);
            $this->_output = new TBinaryProtocol($transport);
        }
        if(!$this->_output->getTransport()->isOpen()){
            $this->_output->getTransport()->open();
        }
        return $this->_output;
    }

    public function build(&$client, $serviceUrl = null){
        if(!is_object($client)) {
            throw new CException("Invalid Parameter of Client");
        }
        $class = get_class($client);
        if(is_string($serviceUrl)){
            $this->_client = new $class($this->getOutput($serviceUrl));
        }else {
            $params = $this->service;
            $key = explode("\\", $class);
            $key = strtolower(str_replace("Client", '', $key[count($key) - 1]));
            if (!isset($params[$key])){
                throw new CException("ServiceURL Not Found in App Config");
            }
            $this->_client = new $class($this->getOutput($params[$key]));
        }

        $client = $this;
    }

    public function __call($method, $arguments){
        $result = call_user_func_array(array($this->_client, $method), $arguments);
        $this->getOutput(null)->getTransport()->close();
        return $result;
    }

    private function parseUrl($parse){
        if(empty($this->_parse_url)){
            $parse = parse_url($parse);
            if(isset($parse['query'])){
                $parse['path'] = $parse['path'].'?'.$parse['query'];
            }
            $this->_parse_url = array(
                'scheme' => isset($parse['scheme']) ? $parse['scheme'] : 'http',
                'host' => $parse['host'],
                'port' => isset($parse['port']) ? $parse['port'] : '80',
                'path' => isset($parse['path']) ? $parse['path'] : '/'
            );
        }
        return $this->_parse_url;
    }
}