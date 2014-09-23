<?php
/**
 * file:red.php
 * author:Toruneko@outlook.com
 * date:2014-7-12
 * desc: Red
 */
defined('RED_PATH') or define('RED_PATH',dirname(__FILE__));

require(YII_PATH.'/YiiBase.php');
class Yii extends YiiBase{
	
	public static function createRedWebApplication($config=null){
		return self::createApplication('RedWebApplication',$config);
	}
	
	public static function createSaeWebApplication($config=null){
		return self::createApplication('SaeWebApplication',$config);
	}

    public static function createThriftApplication($config = null){
        return self::createApplication('TApplication',$config);
    }
	
	public static function autoload($className,$classMapOnly = false){
		if(isset(self::$_redClasses[$className])){
			include(RED_PATH.self::$_redClasses[$className]);
		}elseif($pos = strrpos($className,'\\')){ // thrift autoload
            switch(TRUE) {
                // service
                case preg_match('#(.+)(if|client|processor|rest)$#i', $className, $ns):
                case preg_match('#(.+)_[a-z0-9]+_(args|result)$#i', $className, $ns):
                    $file = self::getPathOfAlias('ext').'/'.str_replace('\\', '/', $ns[1]) . '.php';
                    break;
                // type
                default:
                    $dir = substr($className, 0, $pos);
                    $file = self::getPathOfAlias('ext').'/'.str_replace('\\', '/', $dir) . '/Types.php';
                    break;
            }
            if(is_file($file)){
                include($file);
            }else{
                parent::autoload($className,$classMapOnly);
            }
        }else{
			parent::autoload($className,$classMapOnly);
		}
	}
	
	private static $_redClasses = array(
		'RedSecurityManager' => '/base/RedSecurityManager.php',
		'RedActiveRecord' => '/db/ar/RedActiveRecord.php',
		'RedAction' => '/web/actions/RedAction.php',
		'RedAuthAssignment' => '/web/auth/RedAuthAssignment.php',
		'RedAuthGroup' => '/web/auth/RedAuthGroup.php',
		'RedAuthItem' => '/web/auth/RedAuthItem.php',
		'RedAuthManager' => '/web/auth/RedAuthManager.php',
		'RedAuthOperation' => '/web/auth/RedAuthOperation.php',
		'RedAuthRole' => '/web/auth/RedAuthRole.php',
		'RedDbAuthManager' => '/web/auth/RedDbAuthManager.php',
		'RedUserIdentity' => '/web/auth/RedUserIdentity.php',
		'RedWebUser' => '/web/auth/RedWebUser.php',
		'IsAjaxRequest' => '/web/filters/IsAjaxRequest.php',
		'IsGuest' => '/web/filters/IsGuest.php',
		'RedLoginForm' => '/web/form/RedLoginForm.php',
		'RedLinkPager' => '/web/widget/pagers/RedLinkPager.php',
		'RedArrayDataProvider' => '/web/RedArrayDataProvider.php',
		'RedClientScript' => '/web/RedClientScript.php',
		'RedController' => '/web/RedController.php',
		'RedHttpRequest' => '/web/RedHttpRequest.php',
		'RedWebApplication' => '/web/RedWebApplication.php',
		'RedWebModule' => '/web/RedWebModule.php',
		
		'SaeStatePersister' => '/sae/base/SaeStatePersister.php',
		'SaeMemCache' => '/sae/caching/SaeMemCache.php',
		'SaeDbCommand' => '/sae/db/SaeDbCommand.php',
		'SaeDbConnection' => '/sae/db/SaeDbConnection.php',
		'SaeLogRoute' => '/sae/logging/SaeLogRoute.php',
		'SaeAssetManager' => '/sae/web/SaeAssetManager.php',
		'SaeClientScript' => '/sae/web/SaeClientScript.php',
		'SaeHttpSession' => '/sae/web/SaeHttpSession.php',
		'SaeUploadedFile' => '/sae/web/SaeUploadedFile.php',
		'SaeWebApplication' => '/sae/web/SaeWebApplication.php',

        'TApplication' => '/thrift/TApplication.php',
        'TController' => '/thrift/TController.php',
        'ThriftService'=>'/thrift/ThriftService.php',
        'ThriftClient'=>'/thrift/ThriftClient.php',
        'Thrift\Base\TBase' => '/thrift/base/TBase.php',
        'Thrift\Exception\TException' => '/thrift/exception/TException.php',
        'Thrift\Exception\TProtocolException' => '/thrift/exception/TProtocolException.php',
        'Thrift\Exception\TTransportException' => '/thrift/exception/TTransportException.php',
        'Thrift\Exception\TApplicationException' => '/thrift/exception/TApplicationException.php',
        'Thrift\Factory\TBinaryProtocolFactory' => '/thrift/factory/TBinaryProtocolFactory.php',
        'Thrift\Factory\TCompactProtocolFactory' => '/thrift/factory/TCompactProtocolFactory.php',
        'Thrift\Factory\TJSONProtocolFactory' => '/thrift/factory/TJSONProtocolFactory.php',
        'Thrift\Factory\TProtocolFactory' => '/thrift/factory/TProtocolFactory.php',
        'Thrift\Factory\TStringFuncFactory' => '/thrift/factory/TStringFuncFactory.php',
        'Thrift\Factory\TTransportFactory' => '/thrift/factory/TTransportFactory.php',
        'Thrift\Protocol\JSON\BaseContext' => '/thrift/protocol/JSON/BaseContext.php',
        'Thrift\Protocol\JSON\ListContext' => '/thrift/protocol/JSON/ListContext.php',
        'Thrift\Protocol\JSON\LookaheadReader' => '/thrift/protocol/JSON/LookaheadReader.php',
        'Thrift\Protocol\JSON\PairContext' => '/thrift/protocol/JSON/PairContext.php',
        'Thrift\Protocol\TBinaryProtocol' => '/thrift/protocol/TBinaryProtocol.php',
        'Thrift\Protocol\TBinaryProtocolAccelerated' => '/thrift/protocol/TBinaryProtocolAccelerated.php',
        'Thrift\Protocol\TCompactProtocol' => '/thrift/protocol/TCompactProtocol.php',
        'Thrift\Protocol\TJSONProtocol' => '/thrift/protocol/TJSONProtocol.php',
        'Thrift\Protocol\TProtocol' => '/thrift/protocol/TProtocol.php',
        'Thrift\Serializer\TBinarySerializer' => '/thrift/serializer/TBinarySerializer.php',
        'Thrift\Server\TForkingServer' => '/thrift/server/TForkingServer.php',
        'Thrift\Server\TServer' => '/thrift/server/TServer.php',
        'Thrift\Server\TServerSocket' => '/thrift/server/TServerSocket.php',
        'Thrift\Server\TServerTransport' => '/thrift/server/TServerTransport.php',
        'Thrift\Server\TSimpleServer' => '/thrift/server/TSimpleServer.php',
        'Thrift\StringFunc\Core' => '/thrift/stringfunc/core.php',
        'Thrift\StringFunc\Mbstring' => '/thrift/stringfunc/Mbstring.php',
        'Thrift\StringFunc\TStringFunc' => '/thrift/stringfunc/TStringFunc.php',
        'Thrift\Transport\TBufferedTransport' => '/thrift/transport/TBufferedTransport.php',
        'Thrift\Transport\TFramedTransport' => '/thrift/transport/TFramedTransport.php',
        'Thrift\Transport\THttpClient' => '/thrift/transport/THttpClient.php',
        'Thrift\Transport\TMemoryBuffer' => '/thrift/transport/TMemoryBuffer.php',
        'Thrift\Transport\TNullTransport' => '/thrift/transport/TNullTransport.php',
        'Thrift\Transport\TPhpStream' => '/thrift/transport/TPhpStream.php',
        'Thrift\Transport\TSocket' => '/thrift/transport/TSocket.php',
        'Thrift\Transport\TSocketPool' => '/thrift/transport/TSocketPool.php',
        'Thrift\Transport\TTransport' => '/thrift/transport/TTransport.php',
        'Thrift\Type\TType' => '/thrift/type/TType.php',
        'Thrift\Type\TMessageType' => '/thrift/type/TMessageType.php',
    );
}
spl_autoload_unregister(array('YiiBase','autoload'));
spl_autoload_register(array('Yii','autoload'));
?>