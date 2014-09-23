<?php
/**
 * @file: SaeUploadedFile.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeUploadedFile class file.
 */
class SaeUploadedFile extends CUploadedFile{
	
	private $_storage;
	
	public function __construct($name,$tempName,$type,$size,$error){
		parent::__construct($name, $tempName, $type, $size, $error);
		
		$this->_storage = new SaeStorage();
	}
	
	public function saveAs($domain,$file){
		if($this->_error==UPLOAD_ERR_OK){
			if(($url = $this->_storage->upload($domain,$file,$this->_tempName)) !== false){
				return $url;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}