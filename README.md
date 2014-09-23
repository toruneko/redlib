redlib
======

部署时与yii同级目录。

- index.php
<pre>
defined('YII_PATH') or define('YII_PATH',dirname(\_\_FILE\_\_).DIRECTORY_SEPARATOR.'framework');
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
// change the following paths if necessary</p>
$red = dirname(\_\_FILE\_\_).'/redlib/red.php';
$config = dirname(\_\_FILE\_\_).'/protected/config/main.php';
require_once($red);
Yii::createRedWebApplication($config)->run();
</pre>
