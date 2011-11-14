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
                        'enableProfiling' => true,
		),

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

            //'CatalogCommand' => array('verbose' => true),
		
	),
);