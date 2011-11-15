<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(

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
		
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
                'verbose' => true,
	),
);