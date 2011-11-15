<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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

    // сколько было обновлено
    private $update_query = 0;

    // сколько было добавлено
    private $create_query = 0;

    // проверка, не появились ли новые записи в таксономии регионов.
    // если появились, генерация алиасов в таблице cf_catalog_sector
    // ./yiic catalog sector
    public function actionSector() {
        $sector_result = TermData::model()->with('parent')->findAllByAttributes(array('vid'=>7));

        foreach($sector_result as $row) {
            $ptid = 0;
            $pname = "";
            if ( $row->parent ) {
                $ptid = $row->parent[0]->tid;
                $pname = $row->parent[0]->name;
            }

            // есть ли такая пара в catalog_sector
            $catalog_sector = CatalogSector::model()->findByPk(array('tid'=>$row->tid, 'parent'=>$ptid));

            if ( $catalog_sector ) continue;
            
            // если такой записи нет, создание
            $alias = UrlTransliterate::cleanString($row->name);

            // если сектор второго уровня
            if ( $ptid != 0 ) {
                $alias_parent = UrlTransliterate::cleanString($pname);
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
            $model->tid = $row->tid;
            $model->url_translit = $alias;
            $model->title = $row->name;
            $model->parent = $ptid;
            $model->ptitle = $pname;
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
            
        }
/*

    $count = 0;
    while ( $sector = db_fetch_object($sector_result) ) {
        $query = "
            SELECT COUNT(*)
            FROM {cf_company} cfcomp, {cf_company_revision} cfcompr, {cf_compsector} cfcomps
            WHERE cfcomp.revision_id = cfcompr.id AND cfcomp.isarch = 0 AND cfcomps.companyid = cfcompr.id
            AND
        ";

        $sector->parent == 0 ? $query .= " (cfcomps.b1 = ". $sector->tid ." or cfcomps.b2 = ". $sector->tid ." or cfcomps.b3 = ". $sector->tid .")"
                             : $query .= " (
                                 (cfcomps.b1 = ". $sector->parent ." AND cfcomps.s1 = ". $sector->tid .")
                                 OR
                                 (cfcomps.b2 = ". $sector->parent ." AND cfcomps.s2 = ". $sector->tid .")
                                 OR
                                 (cfcomps.b3 = ". $sector->parent ." AND cfcomps.s3 = ". $sector->tid .")
                                 )";

        $sector_count = db_result(db_query($query));
        $context['results']['count']++;

        // есть ли такая запись в cf_catalog_count
        $isset_record_data_result = db_query("SELECT * FROM {cf_catalog_count}
                                              WHERE tid = %d AND ptid = %d", $sector->tid, $sector->parent);
        $context['results']['count']++;
        $isset_record_data = db_fetch_object($isset_record_data_result);

        if ( $isset_record_data->tid ) {
            if ( $isset_record_data->comp != $sector_count ) {
                db_query("
                    UPDATE {cf_catalog_count}
                    SET comp = %d
                    WHERE tid = %d AND ptid = %d
                ", $sector_count, $sector->tid, $sector->parent);
                $context['results']['count']++;
            }
        }
        // вставка новой записи
        else {

            db_query("INSERT INTO {cf_catalog_count} (tid, ptid, comp, card) VALUES (%d, %d, %d, %d)"
                    , $sector->tid, $sector->parent, $sector_count, 0);
            $context['results']['count']++;
        }
    }

    // дабы не перегружать особливо
    sleep( variable_get('cfcatalog_batch_sleep', '2') );

    // Сообщение выводимое под прогресс баром после окончания текущей операции
    $context['message'] = 'Заполнение счётчиков компаний для секторов.';
 *
 */
        
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

        
        print "\n----------------------\n";
        print $string;
        print "\n\n";
    }

    protected function  beforeAction($action, $params) {
        parent::beforeAction($action, $params);

        // показывать вывод только в режиме отладки
        if ( Yii::app()->params['verbose'] == false ) return true;

        $this->verbose = true;

        return true;
    }

    // помощь по комманде
    public function  getHelp() {
        parent::getHelp();

        print
"==================================================================
Эта команда выполняет операции по обслуживанию каталога cardomat.ru

./yiic catalog sector - пересчёт алиасов секторов
./yiic catalog compcount - пересчёт счётчиков компаний в секторах
./yiic catalog cardcount - пересчёт счётчиков визиток в секторах

";
        
    }
}



