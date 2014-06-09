<?php
header('Content-Type:text/html;charset=utf-8');

// include Yii bootstrap file
$yii = dirname(__FILE__).'/../framework/yii.php';
$config = dirname(__FILE__).'/protected/config/main.php';

//defined('YII_DEBUG') or define('YII_DEBUG',true);  
//// specify how many levels of call stack should be shown in each log message  
//defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);  

// create a Web application instance and run
require($yii);
Yii::createWebApplication($config)->run();
