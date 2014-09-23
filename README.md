redlib
======

部署时与yii同级目录。

- index.php

> defined('YII_PATH') or define('YII_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR.'framework');

> defined('YII_DEBUG') or define('YII_DEBUG',true);

> defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

> // change the following paths if necessary

> $red = dirname(__FILE__).'/redlib/red.php';

> $config = dirname(__FILE__).'/protected/config/main.php';

> require_once($red);

> Yii::createRedWebApplication($config)->run();