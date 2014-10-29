redlib(<a href="http://www.toruneko.net">http://www.toruneko.net</a>)
======

部署时与yii同级目录。

## index.php 入口文件
<pre>
defined('YII_PATH') or define('YII_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR.'framework');
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
$red = dirname(__FILE__).'/redlib/red.php';
require_once($red);
// 可以根据Yii::craeteXApplication()加载对应的配置文件。
$config = dirname(__FILE__).'/protected/config/main.php';
Yii::createWebApplication($config)->run();
// YII::createRedWebApplication($config)->run();
// YII::createSaeWebApplication($config)->run();
// YII::createThriftWebApplication($config)->run();
</pre>

> 你只需要加载 red.php 便可以引入Yii，前提是需要在头文件中定义YII_PATH宏。

## packages.php 静态资源管理
<pre>
return array(
	'jquery'=>array(
		'js'=>array('jquery-1.11.1.min.js'),
		'baseUrl' => 'assets/jquery',
	),
	'admin'=>array(
		'js'=>array('jquery.admin.js'),
		'css'=>array('admin.css'),
		'depends'=>array('jquery'), //依赖关系
		'baseUrl'=>'assets/admin',
	)
);
</pre>

> <p>这个是一个简单的例子，将packages.php置于protected/config内，便可以轻松的使用以下代码进行访问，Yii会自动处理其中的依赖。</p>
<p>Yii::app()->clientScript->registerPackage('admin');</p>
<p>red大多数时候并不希望Yii注册jQuery，如果你发现Yii注册了jQuery，不妨加上 CClientScript::POS_END 试试。</p>

## RedAction action就像controller
<pre>
class Action extends RedAction{
    public function run(){
      $this->render('index');
    }
}
</pre>

> 你可以不需要先通过$this->controller访问controller的方法或者属性了。

## 两个filters 访问过滤
<pre>
class IsAjaxRequest extends CFilter{
	public function preFilter($filterChain){
		if(Yii::app()->request->getIsAjaxRequest()){
			return $filterChain->controller->allowAjaxRequest();
		}else{
			return $filterChain->controller->allowHttpRequest();
		}
	}
}
</pre>
<pre>
class IsGuest extends CFilter{
	protected function preFilter($filterChain) {
		if(Yii::app()->user->isGuest){
			return $filterChain->controller->allowGuest();
		}else{
			return true;
		}
	}
}
</pre>

> 你可以轻松自如的控制是否允许ajax、http访问，是否允许游客访问。

## 其他各种方法，请看代码吧

## Sae支持 - 全透明的哦~
<p>在Sae的支持上，主要做了Db、Cache、Log、Upload、Assets的策略修改。</p>
<p>维护了一个没有实际作用的redlib/sae/lib库，一切只是为了代码提示。</p>

## thrift支持 - Service
<p>你需要修改头文件</p>
<pre>
defined('YII_PATH') or define('YII_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR.'framework');
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
$red = dirname(__FILE__).'/redlib/red.php';
require_once($red);
// 可以根据Yii::craeteXApplication()加载对应的配置文件
$config = dirname(__FILE__).'/protected/config/thrift.php';
YII::createThriftApplication($config)->run();
</pre>
<p>继承TController，并实现接口</p>
<pre>
use com\zhubajie\test\dataobject\helloworld\paramDO;
use com\zhubajie\test\dataobject\helloworld\resultDO;
use com\zhubajie\test\interfaces\HelloworldServiceIf;
class HelloworldServiceController extends TController implements HelloworldServiceIf{
    public function test(ParamDO $param){
        $result = new resultDO();
        $result->result = 'recv:'.$param->param.'; send:helloworld';
        return $result;
    }
}
</pre>

> 这里需要注意的是，你不需要实现任何actionX，因为所有请求都交由actionIndex进行处理，他会帮你完成你想做的事情。

## thrift支持 - Client
<p>你需要在配置里加上thriftClient</p>
<pre>
'components'=>array(
    'thrift' => array(
        'class' => 'ThriftClient'
    ),
),
</pre>
<p>在代码里调用thrift</p>
<pre>
use com\zhubajie\test\dataobject\helloworld\paramDO;
use com\zhubajie\test\interfaces\HelloworldServiceClient;
class TestClientController extends RedController{
    public $serviceUrl = 'http://www.toruneko.com/index.php?r=helloworldService';

    public function actionTest(){
        $param = new paramDO();
        $param->param = 'get hello world';
        $client = new HelloworldServiceClient(null);
        $this->app->thrift->build($client);
        try{
            $result = $client->test2($param);
            var_dump($result);
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }
}
</pre>

> 为了解除框架对Client的耦合，这句 $this->app->thrift->build($client); 代理必不可少。
