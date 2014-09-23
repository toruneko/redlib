<?php
/**
 * file:RedLoginForm.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc:RedLoginForm
 */
class RedLoginForm extends CFormModel{
	public $username;
	public $password;
	public $remember;
	
	protected $_identity;
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules(){
		$labels = $this->attributeLabels();
		return array(
			// username and password are required
			array('username', 'required','message' => '请输入'.$labels['username']),
			array('password', 'required','message' => '请输入'.$labels['password']),
			// password needs to be authenticated
			array('password', 'authenticate'),
			array('remember', 'required')
		);
	}
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels(){
		return array(
			'username' => '用户名',
			'password' => '密码',
			'remember' => '记住我'
		);
	}
	
	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params){
		
	}
	
	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login(){
		if($this->_identity->errorCode === CUserIdentity::ERROR_NONE){
			if($this->remember){
				Yii::app()->user->login($this->_identity, 30*24*60*60);
			}else{
				Yii::app()->user->login($this->_identity);
			}
			return true;
		}else{
			return false;
		}
	}
}