<?php
/**
 * @file: SaeUploadedFile.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeUploadedFile class file.
 */
class SaeUploadedFile extends CUploadedFile{
    static private $_files;

    private $_name;
    private $_tempName;
    private $_type;
    private $_size;
    private $_error;

	private $_storage;

    public static function getInstance($model, $attribute)
    {
        return self::getInstanceByName(CHtml::resolveName($model, $attribute));
    }

    public static function getInstances($model, $attribute)
    {
        return self::getInstancesByName(CHtml::resolveName($model, $attribute));
    }

    public static function getInstanceByName($name)
    {
        if(null===self::$_files)
            self::prefetchFiles();

        return isset(self::$_files[$name]) && self::$_files[$name]->getError()!=UPLOAD_ERR_NO_FILE ? self::$_files[$name] : null;
    }

    public static function getInstancesByName($name)
    {
        if(null===self::$_files)
            self::prefetchFiles();

        $len=strlen($name);
        $results=array();
        foreach(array_keys(self::$_files) as $key)
            if(0===strncmp($key, $name.'[', $len+1) && self::$_files[$key]->getError()!=UPLOAD_ERR_NO_FILE)
                $results[] = self::$_files[$key];
        return $results;
    }

    public static function reset()
    {
        self::$_files=null;
    }

    protected static function prefetchFiles()
    {
        self::$_files = array();
        if(!isset($_FILES) || !is_array($_FILES))
            return;

        foreach($_FILES as $class=>$info)
            self::collectFilesRecursive($class, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error']);
    }

    protected static function collectFilesRecursive($key, $names, $tmp_names, $types, $sizes, $errors){
        if(is_array($names))
        {
            foreach($names as $item=>$name)
                self::collectFilesRecursive($key.'['.$item.']', $names[$item], $tmp_names[$item], $types[$item], $sizes[$item], $errors[$item]);
        }
        else
            self::$_files[$key] = new SaeUploadedFile($names, $tmp_names, $types, $sizes, $errors);
    }

    public function __construct($name,$tempName,$type,$size,$error)
    {
        $this->_name=$name;
        $this->_tempName=$tempName;
        $this->_type=$type;
        $this->_size=$size;
        $this->_error=$error;
        $this->_storage = new SaeStorage();
    }

    public function __toString()
    {
        return $this->_name;
    }

    public function saveAs($file, $domain){
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

    public function getName()
    {
        return $this->_name;
    }

    public function getTempName()
    {
        return $this->_tempName;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getSize()
    {
        return $this->_size;
    }

    public function getError()
    {
        return $this->_error;
    }

    public function getHasError()
    {
        return $this->_error!=UPLOAD_ERR_OK;
    }

    public function getExtensionName()
    {
        if(($pos=strrpos($this->_name,'.'))!==false)
            return (string)substr($this->_name,$pos+1);
        else
            return '';
    }
}