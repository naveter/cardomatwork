<?php

// настройки, отдельные для каждого сервера
$ignore = include(dirname(__FILE__).'/ignore.php');

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$console = array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Cardomat work',

        'preload'=>array('log'),
    
	'import'=>array(
		'application.models.*',
		'application.components.*',
                'application.components.catalogreg.*',
	),        

	// application components
	'components'=>array(
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                            'class'=>'CFileLogRoute',
                            'levels'=>'info',
                    ),
//                    array(
//                        'class'=>'CEmailLogRoute',
//                        'levels'=>'error, warning',
//                        'emails'=>'admin@example.com',
//                    ),
                    array( // configuration for the toolbar
                      'class'=>'ConsoleLogDB',
                      'levels'=>'trace',
                     ),

                ),
            ),
		
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(

	),
);

// слияние конфигов
$console['params'] += $ignore['params'];
$console['components'] += $ignore['components'];






return $console;