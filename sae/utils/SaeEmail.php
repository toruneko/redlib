<?php
/**
 * File: SaeEmail.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 15/1/15 21:52
 * Description: 邮件服务
 */
class SaeEmail extends CApplicationComponent{
    private $email;
    public $account;
    public $password;
    public $smtp_host = '';
    public $smtp_port = 25;
    public $smtp_tls = false;

    public $accesskey;
    public $secretkey;

    public function init(){
        parent::init();

        $this->email = new SaeMail();

        if(!empty($this->accesskey) && !empty($this->secretkey)){
            $this->email->setAuth($this->accesskey, $this->secretkey);
        }
    }

    /**
     * 快速发送邮件
     * @param $to
     * @param $subject
     * @param $msgbody
     * @return mixed
     */
    public function quickSend($to, $subject, $msgbody){
        $res = $this->email->quickSend($to, $subject, $msgbody, $this->account, $this->password,
            $this->smtp_host, $this->smtp_port, $this->smtp_tls);
        $this->clean();
        return $res;
    }

    /**
     * 清除上一次发送的数据
     * @return mixed
     */
    public function clean(){
        return $this->email->clean();
    }

    /**
     * 错误信息
     * @return mixed
     */
    public function errmsg(){
        return $this->email->errmsg();
    }

    /**
     * 错误码
     * @return mixed
     */
    public function errno(){
        return $this->email->errno();
    }

    /**
     * 设置发送参数，可以重置发送者信息
     * @param $options
     * @return mixed
     */
    public function setOpt($options){
        if(empty($options['smtp_host'])) $options['smtp_host'] = $this->smtp_host;
        if(empty($options['smtp_port'])) $options['smtp_port'] = $this->smtp_host;
        if(empty($options['smtp_username'])) $options['smtp_username'] = $this->smtp_host;
        if(empty($options['smtp_password'])) $options['smtp_password'] = $this->smtp_host;
        if(empty($options['tls'])) $options['tls'] = $this->smtp_host;
        return $this->email->setOpt($options);
    }

    /**
     * 设置发送附件
     * @param $attach
     * @return mixed
     */
    public function setAttach($attach){
        return $this->email->setAttach($attach);
    }

    /**
     * 发送邮件
     * @return mixed
     */
    public function send(){
        $res = $this->email->send();
        $this->clean();
        return $res;
    }
}