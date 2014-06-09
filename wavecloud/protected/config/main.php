<?php
return array(
    'name'=>'趣游科技运维支持平台',
    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.libvirt.*'
    ),

    'defaultController'=>'site',

    // application components
    'components'=>array(
        'CURL' =>array(
            'class' => 'application.extensions.curl.Curl',
            'options' => array('timeout' => 10),
        ),
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            'loginUrl' => array('site/login'),
        ),
        'session' => array (
            'cookieMode' => 'none'
        ),
        // uncomment the following to use a MySQL database
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=wavecloud',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'tablePrefix' => '',
        ),
        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
        'urlManager'=>array(
            'urlFormat'=>'path',
            'rules'=>array(
                'notice/<id:\d+>'=>'notice/shownotice',
            ),
            'caseSensitive'=>false,
        ),
        'session'=>array(  
            'class' => 'system.web.CDbHttpSession',
            'connectionID' => 'db',
            'timeout'=>3600*86400,
        ), 
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
                // uncomment the following to show log messages on web pages
                
                array(
                    'class'=>'CWebLogRoute',
                    'levels'=>'trace',     //级别为trace
                ),
                
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    // 'params'=>require(dirname(__FILE__).'/params.php'),
);
