<?php

/**
 * Description of CatalogCommand
 *
 * @author bass
 */
class CatalogCommand extends CConsoleCommand
{
    // выводить отладочные сообщения, если YII_DEBUG == true
    // или если была задана опция --verbose=true
    public $verbose = false;

    // размер пакета для обработки счётчиков компаний и визиток в секторах
    public $package_count = 30;

    // сколько было обновлено
    private $update_query = 0;

    // сколько было добавлено
    private $create_query = 0;

    // проверка, не появились ли новые записи в таксономии регионов.
    // если появились, генерация алиасов в таблице cf_catalog_sector
    // вызывается первым
    // ./yiic catalog sector
    public function actionSector() {
        //$sector_result = TermData::model()->with('hierarchy2term')->findAllByAttributes(array('vid'=>7));
        //$sector_result = TermData::model()->with(array('hierarchy2term' => array('joinType'=>'INNER JOIN', 'condition' => 't.vid = 7')))->findAll();

        $sector_result = Yii::app()->db->createCommand("SELECT td.tid tid, td.name name, th.parent parent, IFNULL(td2.name, '') as parentname
                                                        FROM term_data td
                                                        LEFT JOIN term_hierarchy th ON td.tid = th.tid
                                                        LEFT JOIN term_data as td2 ON td2.tid = th.parent
                                                        WHERE
                                                        td.vid = ". Variable::getVariable('cfcompany_vocabulary_sector'))->queryAll();

        foreach($sector_result as $row) {

            // есть ли такая пара в catalog_sector
            $catalog_sector = CatalogSector::model()->findByPk(array('tid'=>$row['tid'], 'parent'=>$row['parent']));

            if ( $catalog_sector ) continue;
            
            // если такой записи нет, создание
            $alias = UrlTransliterate::cleanString($row['name']);

            // если сектор второго уровня
            if ( $row['parent'] != 0 ) {
                $alias_parent = UrlTransliterate::cleanString($row['parentname']);
                $alias = $alias_parent. '/' .$alias;
            }

            // проверка на дубли alias
            for ( $i = 0; $i < 10; $i++ ) {
                $isset_sector = CatalogSector::model()->find('url_translit=:alias', array(':alias' => $alias));
                
                if ( $isset_sector ) $alias = $alias.$i;
                else break;
            }

            // новая запись
            $model = new CatalogSector();
            $model->tid = $row['tid'];
            $model->url_translit = $alias;
            $model->title = $row['name'];
            $model->parent = $row['parent'];
            $model->ptitle = $row['parentname'];
            $model->save();

            $this->create_query++;
        }

        // дабы не перегружать особливо
        sleep( Variable::getVariable('cfcatalog_batch_sleep') );        
    }

    // пересчёт счётчиков компаний в секторах
    // ./yiic catalog compcount
    public function actionCompcount() {

        //все записи из cf_catalog_sector
        $catalog_sector = CatalogSector::model()->findAll();

        $count_to_sleep = 0;
        foreach ( $catalog_sector as $rowsector ) {
            // настройка condition
            $rowsector->parent == 0 ? $condition = 'sectors.b1 = :s OR sectors.b2 = :s OR sectors.b3 = :s'
                                    : $condition = '(sectors.b1 = :b AND sectors.s1 = :s)
                                                    OR (sectors.b2 = :b AND sectors.s2 = :s)
                                                    OR (sectors.b3 = :b AND sectors.s3 = :s)';

            $count = Company::model()->with(array('revision.sectors' => array(
                                                'select' => false,
                                                'condition' => $condition,
                                                'params' => array(':b' => $rowsector->parent, ':s' => $rowsector->tid),
                                      )))->count();
            
            // есть ли такая запись в cf_catalog_count
            $model = CatalogCount::model()->findByPk( array('tid' => $rowsector->tid, 'ptid' => $rowsector->parent) );
            if ( $model ) {
                $model->comp = $count;
                $model->save();
                $this->update_query++;
            }
            else {
                $model = new CatalogCount();
                $model->tid = $rowsector->tid;
                $model->ptid = $rowsector->parent;
                $model->comp = $count;
                $model->save();
                $this->create_query++;
            }

            // pause
            $count_to_sleep++;
            if ( $count_to_sleep >= $this->package_count ) {
                $this->printMessage("Выполнено ". $count_to_sleep);
                $count_to_sleep = 0;
                sleep( Variable::getVariable('cfcatalog_batch_sleep') );
            }            
        }        
    }

    // пересчёт счётчиков визиток в секторах
    // ./yiic catalog cardcount
    public function actionCardcount() {

        //все записи из cf_catalog_sector
        $catalog_sector = CatalogSector::model()->findAll();

        $count_to_sleep = 0;
        foreach ( $catalog_sector as $rowsector ) {
            // настройка condition
            $rowsector->parent == 0 ? $condition = 'sectors.b1 = :s OR sectors.b2 = :s OR sectors.b3 = :s'
                                    : $condition = '(sectors.b1 = :b AND sectors.s1 = :s)
                                                    OR (sectors.b2 = :b AND sectors.s2 = :s)
                                                    OR (sectors.b3 = :b AND sectors.s3 = :s)';

            $count = Card::model()->with(array('company.revision.sectors' => array(
                                                'select' => false,
                                                'condition' => $condition,
                                                'params' => array(':b' => $rowsector->parent, ':s' => $rowsector->tid),
                                      )))->count();

            // есть ли такая запись в cf_catalog_count
            $model = CatalogCount::model()->findByPk( array('tid' => $rowsector->tid, 'ptid' => $rowsector->parent) );
            if ( $model ) {
                $model->card = $count;
                $model->save();
                $this->update_query++;
            }
            else {
                $model = new CatalogCount();
                $model->tid = $rowsector->tid;
                $model->ptid = $rowsector->parent;
                $model->card = $count;
                $model->save();
                $this->create_query++;
            }

            // pause
            $count_to_sleep++;
            if ( $count_to_sleep >= $this->package_count ) {
                $this->printMessage("Выполнено ". $count_to_sleep);
                $count_to_sleep = 0;
                sleep( Variable::getVariable('cfcatalog_batch_sleep') );
            }
        }
    }

    /**
     * Пересчёт регионов для компаний
     * ./yiic catalog compreg2
     */
    public function actionCompreg1() {
        // формирование списка стран, в которых есть компании
//        $countries_isset = array();
//        $countries = TermData::model()->with(array('hierarchy' => array(
//                                            'select' => 'parent',
//                                            'condition' => 'hierarchy.parent = 0'
//                                        )))->findAllByAttributes(array('vid' => Variable::getVariable('cfcompany_vocabulary_region')));
//
//        foreach ( $countries as $country ) {
//            $count = Company::model()->with(array('revision' => array(
//                                                    'select' => false,
//                                                    'condition' => 'reg1 = :reg1',
//                                                    'params' => array(':reg1' => $country->tid),
//                                                  )))->count();
//
//            if ( $count > 0 ) array_push($countries_isset, $count->tid);
//        }

        // получение всех TID-ов секторов
        $catalog_sectors = CatalogSector::model()->findAll();

        // заполнение таблицы регионов
        foreach( $catalog_sectors as $catalog_sector ) {
            $sector_name = $catalog_sector->title;
            $catalog_sector->parent ? $sector_name = $catalog_sector->ptitle ." -> ". $sector_name : 0;
            $this->printMessage($sector_name);

            
            
        }

    }

    /**
     * This method is invoked right after an action finishes execution.
     * You may override this method to do some postprocessing for the action.
     * @param string $action the action name
     * @param array $params the parameters to be passed to the action method.
     */
    protected function afterAction($action,$params)
    {
        // статистика запросов
        $stats = Yii::app()->db->getStats();

        // запись в лог
        $string = "created:". $this->create_query ." updated:". $this->update_query ." query:". $stats[0];
        Yii::log($string, 'info', 'application.commands.'. __CLASS__ .'.'. $action);

        // если включён режим отладки
        if ( !$this->verbose ) return;

        $this->printMessage('=======================');
        $this->printMessage($string);
        $this->printMessage();
    }

    protected function  beforeAction($action, $params) {
        parent::beforeAction($action, $params);

        // показывать вывод только в режиме отладки
        if ( Yii::app()->params['verbose'] == false ) return true;

        $this->verbose = true;
        $this->printMessage("Running action ". $action ."...");

        return true;
    }

    // помощь по комманде
    public function  getHelp() {
        parent::getHelp();

        print 
"==================================================================
Эта команда выполняет операции по обслуживанию каталога cardomat.ru
Вызываются cron в следующем порядке:

./yiic catalog sector - пересчёт алиасов секторов
./yiic catalog compcount - пересчёт счётчиков компаний в секторах
./yiic catalog cardcount - пересчёт счётчиков визиток в секторах
./yiic catalog compreg1 - пересчёт стран для компаний
./yiic catalog compreg2 - пересчёт регионов для компаний
./yiic catalog compreg3 - пересчёт городов для компаний
./yiic catalog cardreg1 - пересчёт стран для визиток
./yiic catalog cardreg2 - пересчёт регионов для визиток
./yiic catalog cardreg3 - пересчёт городов для визиток

Параметры, доступные для любой команды:
verbose - отображать ли интерактивные сообщения. По-умолчанию, устанавливается
          в соответствии со значением глобальной настройки varbose из файла
          настроек config/ignore.php
          Возможные значение: true, false
package_count - размер пакета для команд compcount и cardcount.
                По-умолчанию: 30

Команда cron для выполнения всех операций сразу:
./yiic catalog sector && ./yiic catalog compcount && ./yiic catalog cardcount && ...

";
        
    }

    /**
     * Печать на экран
     * @param string $message - сообщение для печати
     */
    protected function printMessage($message = "") {
        if ( $this->verbose ) print $message ."\n";
    }
}



