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

    // время начала опреации
    private $begin_time;

    // проверка, не появились ли новые записи в таксономии регионов.
    // если появились, генерация алиасов в таблице cf_catalog_sector
    // вызывается первым
    // ./yiic catalog sector
    public function actionSector() {
        $sector_result = TermData::model()->with('hierarchy2term')->findAllByAttributes(array('vid'=>7));
        
        foreach ($sector_result as $sector) {
            if ($sector->hierarchy2term) {
                foreach ($sector->hierarchy2term as $parent)
                        $this->_actionSector ($sector, $parent);
            } else {
                $this->_actionSector ($sector, null);
            }
        }

        // дабы не перегружать особливо
        sleep( Variable::getVariable('cfcatalog_batch_sleep') );
    }

    /**
     * вспомогательный метод для actionSector
     * @param TermData $child Data child
     * @param TermData $parent Data parent
     * @return null
     */
    private function _actionSector($child, $parent = null) {
        if ( is_null($parent) ) {
            $parent = new stdClass();
            $parent->name = '';
            $parent->tid = 0;
        }

        // есть ли такая пара в catalog_sector
        $catalog_sector = CatalogSector::model()->findByPk(array('tid'=>$child->tid, 'parent'=>$parent->tid));

        if ( $catalog_sector ) return;

        // если такой записи нет, создание
        $alias = UrlTransliterate::cleanString($child->name);

        // если сектор второго уровня
        if ( $parent->tid != 0 ) {
            $alias_parent = UrlTransliterate::cleanString($parent->name);
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
        $model->tid = $child->tid;
        $model->url_translit = $alias;
        $model->title = $child->name;
        $model->parent = $parent->tid;
        $model->ptitle = $parent->name;
        $model->save();

        $this->create_query++;
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
     * Пересчёт регионов для компаний и визиток
     * @param string comp or card
     * @param integer type of reg - 1, 2 or 3
     * @param integer onlycategory - генерить только эту категорию
     * ./yiic catalog regions --type=comp --reg=1
     */
    public function actionRegions($type, $reg, $onlycategory = NULL) {
        // получение секторов
        $sectors = CatalogReg::getSectorsList();

        foreach ($sectors as $sector) {
            // если нужно сгенерить только одну категорию
            if ( $onlycategory && $sector->tid != $onlycategory && $sector->parent != $onlycategory ) continue;

            if ( $type == 'comp' ) $cr = new CatalogRegComp($sector, $reg);
            else $cr = new CatalogRegCard($sector, $reg);
            
            $this->printMessage( $cr->getSectorsName() );
            $cr->checkRegions();
            
            // накомпление сохранённых и созданных записей
            $this->update_query += $cr->updated;
            $this->create_query += $cr->created;

            sleep( Variable::getVariable('cfcatalog_batch_sleep') );
        }

        ConsoleLogDB::$print = true;
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

        // подсчёт времени выполнения
        $worktime = time() - $this->begin_time;
        $diff = date('H', 1); // разница между началом эпохи
        $worktimestr =  " exec time: ".(date('H', $worktime) - $diff).":".date('i', $worktime) .":". date('s', $worktime)."\n";

        // запись в лог
        $string = "created:". $this->create_query ." updated:". $this->update_query ." query:". $stats[0]. $worktimestr;
        Yii::log($string, 'info', 'application.commands.'. __CLASS__ .'.'. $action);

        // если включён режим отладки
        if ( !$this->verbose ) return;

        $this->printMessage('=======================');
        $this->printMessage($string);
        $this->printMessage();
    }

    protected function  beforeAction($action, $params) {
        parent::beforeAction($action, $params);

        // инициализация времени начала
        $this->begin_time = time();

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
./yiic catalog regions --type=comp --reg=1 - пересчёт стран для компаний
./yiic catalog regions --type=comp --reg=2 - пересчёт регионов для компаний
./yiic catalog regions --type=comp --reg=3 - пересчёт городов для компаний
./yiic catalog regions --type=card --reg=1 - пересчёт стран для визиток
./yiic catalog regions --type=card --reg=2 - пересчёт регионов для визиток
./yiic catalog regions --type=card --reg=3 - пересчёт городов для визиток

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



