<?php
namespace logchain;

/**
 * Autogenerated by Thrift Compiler (0.9.1)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TApplicationException;


final class BaeRet {
  const OK = 0;
  const RETRY = 1;
  const OLD_VERSION = 2;
  const AUTH_PARM_ERROR = 3;
  const AUTH_FAIL = 4;
  const AUTH_ASK_NOT_EXIST = 5;
  const AUTH_ASK_NOT_MATCH = 6;
  const AUTH_QUOTA_NOT_INIT = 7;
  const AUTH_QUOTA_EXCEED = 8;
  const AUTH_QUOTA_UPDATE_ERROR = 9;
  const AUTH_CONN_FAIL = 10;
  const INTERNAL_ERROR = 11;
  static public $__names = array(
    0 => 'OK',
    1 => 'RETRY',
    2 => 'OLD_VERSION',
    3 => 'AUTH_PARM_ERROR',
    4 => 'AUTH_FAIL',
    5 => 'AUTH_ASK_NOT_EXIST',
    6 => 'AUTH_ASK_NOT_MATCH',
    7 => 'AUTH_QUOTA_NOT_INIT',
    8 => 'AUTH_QUOTA_EXCEED',
    9 => 'AUTH_QUOTA_UPDATE_ERROR',
    10 => 'AUTH_CONN_FAIL',
    11 => 'INTERNAL_ERROR',
  );
}

final class BaeLogLevel {
  const FATAL = 1;
  const WARNING = 2;
  const NOTICE = 4;
  const TRACE = 8;
  const DEBUG = 16;
  static public $__names = array(
    1 => 'FATAL',
    2 => 'WARNING',
    4 => 'NOTICE',
    8 => 'TRACE',
    16 => 'DEBUG',
  );
}

class UserLogEntry {
  static $_TSPEC;

  public $appid = null;
  public $level = null;
  public $timestamp = null;
  public $msg = null;
  public $logid = null;
  public $tag = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'appid',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'level',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'timestamp',
          'type' => TType::I64,
          ),
        4 => array(
          'var' => 'msg',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'logid',
          'type' => TType::STRING,
          ),
        9 => array(
          'var' => 'tag',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['appid'])) {
        $this->appid = $vals['appid'];
      }
      if (isset($vals['level'])) {
        $this->level = $vals['level'];
      }
      if (isset($vals['timestamp'])) {
        $this->timestamp = $vals['timestamp'];
      }
      if (isset($vals['msg'])) {
        $this->msg = $vals['msg'];
      }
      if (isset($vals['logid'])) {
        $this->logid = $vals['logid'];
      }
      if (isset($vals['tag'])) {
        $this->tag = $vals['tag'];
      }
    }
  }

  public function getName() {
    return 'UserLogEntry';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->appid);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->level);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->timestamp);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->msg);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->logid);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 9:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->tag);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('UserLogEntry');
    if ($this->appid !== null) {
      $xfer += $output->writeFieldBegin('appid', TType::STRING, 1);
      $xfer += $output->writeString($this->appid);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->level !== null) {
      $xfer += $output->writeFieldBegin('level', TType::I32, 2);
      $xfer += $output->writeI32($this->level);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->timestamp !== null) {
      $xfer += $output->writeFieldBegin('timestamp', TType::I64, 3);
      $xfer += $output->writeI64($this->timestamp);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->msg !== null) {
      $xfer += $output->writeFieldBegin('msg', TType::STRING, 4);
      $xfer += $output->writeString($this->msg);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->logid !== null) {
      $xfer += $output->writeFieldBegin('logid', TType::STRING, 8);
      $xfer += $output->writeString($this->logid);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->tag !== null) {
      $xfer += $output->writeFieldBegin('tag', TType::STRING, 9);
      $xfer += $output->writeString($this->tag);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class SecretEntry {
  static $_TSPEC;

  public $user = null;
  public $passwd = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'user',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'passwd',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['user'])) {
        $this->user = $vals['user'];
      }
      if (isset($vals['passwd'])) {
        $this->passwd = $vals['passwd'];
      }
    }
  }

  public function getName() {
    return 'SecretEntry';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->user);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->passwd);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('SecretEntry');
    if ($this->user !== null) {
      $xfer += $output->writeFieldBegin('user', TType::STRING, 1);
      $xfer += $output->writeString($this->user);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->passwd !== null) {
      $xfer += $output->writeFieldBegin('passwd', TType::STRING, 2);
      $xfer += $output->writeString($this->passwd);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class BaeLogEntry {
  static $_TSPEC;

  public $category = null;
  public $content = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'category',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'content',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['category'])) {
        $this->category = $vals['category'];
      }
      if (isset($vals['content'])) {
        $this->content = $vals['content'];
      }
    }
  }

  public function getName() {
    return 'BaeLogEntry';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->category);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->content);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('BaeLogEntry');
    if ($this->category !== null) {
      $xfer += $output->writeFieldBegin('category', TType::STRING, 1);
      $xfer += $output->writeString($this->category);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->content !== null) {
      $xfer += $output->writeFieldBegin('content', TType::STRING, 2);
      $xfer += $output->writeString($this->content);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class BaeLog {
  static $_TSPEC;

  public $messages = null;
  public $secret = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'messages',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => '\logchain\BaeLogEntry',
            ),
          ),
        2 => array(
          'var' => 'secret',
          'type' => TType::STRUCT,
          'class' => '\logchain\SecretEntry',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['messages'])) {
        $this->messages = $vals['messages'];
      }
      if (isset($vals['secret'])) {
        $this->secret = $vals['secret'];
      }
    }
  }

  public function getName() {
    return 'BaeLog';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::LST) {
            $this->messages = array();
            $_size0 = 0;
            $_etype3 = 0;
            $xfer += $input->readListBegin($_etype3, $_size0);
            for ($_i4 = 0; $_i4 < $_size0; ++$_i4)
            {
              $elem5 = null;
              $elem5 = new \logchain\BaeLogEntry();
              $xfer += $elem5->read($input);
              $this->messages []= $elem5;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRUCT) {
            $this->secret = new \logchain\SecretEntry();
            $xfer += $this->secret->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('BaeLog');
    if ($this->messages !== null) {
      if (!is_array($this->messages)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('messages', TType::LST, 1);
      {
        $output->writeListBegin(TType::STRUCT, count($this->messages));
        {
          foreach ($this->messages as $iter6)
          {
            $xfer += $iter6->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->secret !== null) {
      if (!is_object($this->secret)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('secret', TType::STRUCT, 2);
      $xfer += $this->secret->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


