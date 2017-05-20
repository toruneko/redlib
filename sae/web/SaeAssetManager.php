<?php

/**
 * @file: SaeAssetManager.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-10
 * @desc: SaeAssetManager class file.
 */
class SaeAssetManager extends CApplicationComponent
{
    const DEFAULT_BASEPATH = 'assets';
    private $_basePath;
    private $_baseUrl;
    private $_published = array();

    public function getBasePath()
    {
        if ($this->_basePath === null) {
            $request = Yii::app()->getRequest();
            $this->setBasePath(dirname($request->getScriptFile()) . DIRECTORY_SEPARATOR . self::DEFAULT_BASEPATH);
        }
        return $this->_basePath;
    }

    public function setBasePath($value)
    {
        if (($basePath = realpath($value)) !== false && is_dir($basePath))
            $this->_basePath = $basePath;
        else
            throw new CException(Yii::t('yii', 'CAssetManager.basePath "{path}" is invalid. Please make sure the directory exists and is writable by the Web server process.',
                array('{path}' => $value)));
    }

    public function getBaseUrl()
    {
        if ($this->_baseUrl === null) {
            $request = Yii::app()->getRequest();
            $this->setBaseUrl($request->getBaseUrl() . '/' . self::DEFAULT_BASEPATH);
        }
        return $this->_baseUrl;
    }

    public function setBaseUrl($value)
    {
        $this->_baseUrl = rtrim($value, '/');
    }

    public function publish($path, $hashByName = false, $level = -1, $forceCopy = null)
    {
        if (isset($this->_published[$path])) {
            return $this->_published[$path];
        } elseif (($src = realpath($path)) !== false) {
            if (is_file($src)) {
                $fileName = basename($src);
                return $this->_published[$path] = $this->getBaseUrl() . "/$fileName";
            } elseif (is_dir($src)) {
                return $this->_published[$path] = $this->getBaseUrl();
            }
        }

        throw new CException(Yii::t('yii', 'The asset "{asset}" to be published does not exist.',
            array('{asset}' => $path)));
    }

    public function getPublishedPath($path, $hashByName = false)
    {
        if (is_string($path) && ($path = realpath($path)) !== false) {
            $base = $this->getBasePath();
            return is_file($path) ? $base . DIRECTORY_SEPARATOR . basename($path) : $base;
        } else
            return false;
    }

    public function getPublishedUrl($path, $hashByName = false)
    {
        if (isset($this->_published[$path])) {
            return $this->_published[$path];
        }
        if (($path = realpath($path)) !== false) {
            $base = $this->getBaseUrl();
            return is_file($path) ? $base . '/' . basename($path) : $base;
        } else {
            return false;
        }
    }
}