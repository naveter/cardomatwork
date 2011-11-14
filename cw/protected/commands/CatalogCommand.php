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

        $sector_result = Yii::app()->db->createCommand("
                        SELECT child.tid, child.name, IFNULL(child.parent, 0) as parent, td.name as parentname
                        FROM (
                                SELECT td.tid tid, td.name name, th.parent parent
                                FROM term_data td, term_hierarchy th
                                WHERE td.tid = th.tid AND td.vid = ". Variable::getVariable('cfcompany_vocabulary_sector') ."
                        ) as child
                        LEFT JOIN term_data as td ON td.tid = child.parent")->query();

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
        if ( YII_DEBUG == false ) return true;

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



