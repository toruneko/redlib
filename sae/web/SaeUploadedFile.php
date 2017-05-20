<?php

/**
 * @file: SaeUploadedFile.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeUploadedFile class file.
 */
class SaeUploadedFile extends RedUploadedFile
{
    private $_storage = null;

    protected static function collectFilesRecursive($key, $names, $tmp_names, $types, $sizes, $errors)
    {
        if (is_array($names)) {
            foreach ($names as $item => $name)
                static::collectFilesRecursive($key . '[' . $item . ']', $names[$item], $tmp_names[$item], $types[$item], $sizes[$item], $errors[$item]);
        } else
            self::$_files[$key] = new SaeUploadedFile($names, $tmp_names, $types, $sizes, $errors);
    }

    public function __construct($name, $tempName, $type, $size, $error)
    {
        parent::__construct($name, $tempName, $type, $size, $error);

        if (!is_writable(Yii::getPathOfAlias('root'))) {
            $this->_storage = new SaeStorage();
        }
    }

    public function saveAs($file, $domain = '')
    {
        if ($this->getError() == UPLOAD_ERR_OK) {
            if ($this->_storage instanceof SaeStorage) {
                if (($url = $this->_storage->upload($domain, $file, $this->getTempName())) !== false) {
                    return $url;
                }
            } else {
                $dir = dirname($domain . '/' . $file);
                if (!file_exists($dir)) {
                    CFileHelper::createDirectory($dir, 0777, true);
                }
                if (parent::saveAs($domain . '/' . $file, true)) {
                    return str_replace(Yii::getPathOfAlias('root'), '', $domain . '/' . $file);
                }
            }
        }
        return false;
    }
}