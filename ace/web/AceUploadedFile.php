<?php

/**
 * @file: AceUploadedFile.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: AceUploadedFile class file.
 */
class AceUploadedFile extends RedUploadedFile
{

    protected static function collectFilesRecursive($key, $names, $tmp_names, $types, $sizes, $errors)
    {
        if (is_array($names)) {
            foreach ($names as $item => $name)
                static::collectFilesRecursive($key . '[' . $item . ']', $names[$item], $tmp_names[$item], $types[$item], $sizes[$item], $errors[$item]);
        } else
            self::$_files[$key] = new AceUploadedFile($names, $tmp_names, $types, $sizes, $errors);
    }

    public function saveAs($file, $domain = '')
    {
        if ($this->getError() == UPLOAD_ERR_OK) {
            $storage = Alibaba::Storage(array(
                'id' => 'WwsvYYVjyGUey9yA',
                'key' => 'LBIEPXGYlTb8UYOtBYZQ9loAK6ButU',
                'bucket' => 'toruneko'
            ));
            if ($storage->saveFile($file, $this->getTempName()) !== false) {
                return 'http://static.toruneko.net/' . $file;
            }
        }
        return false;
    }
}