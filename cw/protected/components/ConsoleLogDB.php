<?php
/**
 * Класс для формирования коллекции SQL-запросов и их вывода в консоль, если надо
 */

class ConsoleLogDB extends CLogRoute
{
        public static $print = false;

	public function collectLogs($logger, $processLogs = false)
	{
		$logs=$logger->getLogs($this->levels,$this->categories);
		if(empty($logs)) $logs = array();
		$this->processLogs($logs);
	}

	public function processLogs($logs)
	{
		$app=Yii::app();

		//Checking for an DEBUG mode of running app
		if ( !DEFINED('YII_DEBUG') || YII_DEBUG == false ) return;

                // рекрусивная печать лога запросов
                if ( self::$print ) print_r( $this->formatDBQueries($logs) );
                
	}

        protected  function formatDBQueries($data)
	{

		$result = array();
		$result['panelTitle'] = 'Database Queries';
		$count = 0;
		$items = array();
		foreach ($data as $row)
		{
			if (substr($row[2],0,9) == 'system.db')
			{
				$items[] = $row;
				if ($row[2] == 'system.db.CDbCommand') $count++;
			}
		}

		if (count($items) > 0) $result['content'] = $items;

		$result['title'] = 'DB Query: '.$count;

		return $result;
	}
}