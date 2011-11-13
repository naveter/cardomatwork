<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Cardomat work',

        'preload'=>array('log'),
    
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	// application components
	'components'=>array(
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=dp3',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '123456',
			'charset' => 'utf8',
		),

            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                            'class'=>'CFileLogRoute',
                            'levels'=>'error, warning',
                    ),
                    array('class' => 'CProfileLogRoute', 'enabled' => true),


                ),
            ),
		
	),
);