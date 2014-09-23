<?php
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TPhpStream;
use Thrift\Type\TMessageType;

/**
 * File: ThriftService.php.
 * User: daijianhao@zhubajie.com
 * Date: 14-7-25
 * Time: 下午12:41
 */

class ThriftService extends CApplicationComponent{
    private $_input = null;
    private $_output = null;
    private $_header = array();
    private $_controller = null;
    private $_interface  = array();

    function __construct(){
        $stream = new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W);
        $transport = new TBufferedTransport($stream);
        $this->_input = $this->_output = new TBinaryProtocol($transport, TRUE, TRUE);
    }


    public function setController($controller){
        $class = new ReflectionClass($controller);
        foreach($class->getInterfaceNames() as $if){
            if(!preg_match('/(.+)If$/', $if, $match)) {
                continue;
            }
            if(interface_exists($if, TRUE) == FALSE) {
                throw new CException(Yii::t('thrift','Api service interface {if} not exists',array( 'if' => $if)));
            }
            $this->_interface[] = $if;
        }
        $this->_controller = $controller;
    }

    public function getController(){
        return $this->_controller;
    }

    public function getArgsInstance(){
        $header = $this->getHeader();
        foreach($this->_interface as $interface) {
            $class = preg_replace('/If$/', '_'.$header['name'].'_args', $interface);
            if(class_exists($class)) {
                return new $class();
            }
        }
        return null;
    }

    public function getResultInstance() {
        $header = $this->getHeader();
        foreach($this->_interface as $interface) {
            $class = preg_replace('/If$/', '_'.$header['name'].'_result', $interface);
            if(class_exists($class)) {
                return new $class();
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getInput() {
        if(!$this->_input->getTransport()->isOpen()){
            $this->_input->getTransport()->open();
        }
        return $this->_input;
    }

    public function getOutput(){
        if(!$this->_output->getTransport()->isOpen()){
            $this->_output->getTransport()->open();
        }
        return $this->_output;
    }

    /**
     * @return array
     */
    public function getHeader(){
        if(empty($this->_header)){
            $this->getInput()->readMessageBegin(
                $this->_header['name'],
                $this->_header['type'],
                $this->_header['seqid']
            );
        }
        return $this->_header;
    }

    public function getRequestParams(){
        $input = $this->getInput();
        $class = $this->getArgsInstance();
        $class->read($input);
        $input->readMessageEnd();
        $params = array();
        if($class::$_TSPEC) {
            foreach($class::$_TSPEC as $spec) {
                $params[] = $class->$spec['var'];
            }
        }
        return $params;
    }

    public function response($data){
        $output = $this->getOutput();
        $header = $this->getHeader();
        $result = $this->getResultInstance();
        $result->success = $data;

        if($output instanceof TBinaryProtocolAccelerated && function_exists('thrift_protocol_write_binary')){
            thrift_protocol_write_binary($output, $header['name'], TMessageType::REPLY,
                $result, $header['seqid'], $output->isStrictWrite());
        }else{
            $output->writeMessageBegin($header['name'], TMessageType::REPLY, $header['seqid']);
            $result->write($output);
            $output->writeMessageEnd();
            $output->getTransport()->flush();
        }
    }
}