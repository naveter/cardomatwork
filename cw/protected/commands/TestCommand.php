<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestCompany
 *
 * @author bass
 */
class TestCommand extends CConsoleCommand
{
    //public $verbose=true;

    // поиск количества компаний в определённых секторах
    // ./yiic test compcount --b=1909 --s=1911
    public function actionCompcount($b, $s) {
        $n = Company::model()->with('revision')
                             ->with(array('revision.sectors' => array(
                                        'select' => false,
                                        'condition' => '
                                             (sectors.b1 = :b AND sectors.s1 = :s)
                                             OR
                                             (sectors.b2 = :b AND sectors.s2 = :s)
                                             OR
                                             (sectors.b3 = :b AND sectors.s3 = :s)
                                            ',
                                            'params' => array(':b' => $b, ':s' => $s),
                              )))->count();

        print $n;

    }

    // поиск количества визиток в определённых секторах
    // ./yiic test cardcount --b=1941 --s=1946
    public function actionCardcount($b, $s) {

        $n = Card::model()->with(array('company.revision.sectors' => array(
                                        'select' => false,
                                        'condition' => '
                                             (sectors.b1 = :b AND sectors.s1 = :s)
                                             OR
                                             (sectors.b2 = :b AND sectors.s2 = :s)
                                             OR
                                             (sectors.b3 = :b AND sectors.s3 = :s)
                                            ',
                                            'params' => array(':b' => $b, ':s' => $s),
                              )))->count();

        print_r($n);

    }

    // работа с записями в CatalogSector
    // ./yiic test catalogsector --tid=1909 --parent=0
    public function actionCatalogSector($tid, $parent) {
        $catalog_sector = CatalogSector::model()->findByPk(array('tid'=>$tid, 'parent'=>$parent));

        if ( $catalog_sector ) {
            print $catalog_sector->url_translit ."\n";
        }
    }

    // проба транслитерации url
    // ./yiic test transliteration --name="просто русская строка!"
    public function actionTransliteration($name) {
        print UrlTransliterate::cleanString($name) ."\n";
    }

    // поиск компании и их визиток
    // ./yiic test company --id=1660
    public function actionCompany($id = NULL) {
	//$condition='status='.Post::STATUS_PUBLISHED.' OR status='.Post::STATUS_ARCHIVED;
        $condition = "";
        $company = Company::model()->with('revision','cards','revision.term_opf', 'revision.sectors')->findByPk($id, $condition);
                
        print "Название компании:". $company->revision->name ."\n";
        print "ОПФ:". ($company->revision->term_opf ? $company->revision->term_opf->name : '') ."\n";
        print_r($company->revision->sectors);

        if ( count($company->cards) > 0 ) {
            foreach ( $company->cards as $card ) {
                print $card->email ."\n";
                print $card->revision->lastname ."\n";
            }
        }

        // вывести данные о запросах БД
        ConsoleLogDB::$print = true;
        
        // запись в лог
        Yii::log("Запущено действие company", 'info', 'application.commands.'. __CLASS__ .'.'. __FUNCTION__);
    }

    // поиск ревизии компании
    // ./yiic test comprevision --id=1
    public function actionComprevision($id) {
        $comprevision = CompanyRevision::model()->with('sectors')->findByPk($id);


    }

    // работа с переменными
    // ./yiic test variable --name=yii_test --value=1000
    public function actionVariable($name = NULL, $value = NULL ) {
        // создание переменной
        if ( $name && !is_null($value) ) {
            Variable::setVariable($name, $value);
            print $name .":". $value;
        }
        // если указано название переменной
        else if ( $name ) {
            $value = Variable::getVariable($name);
            print $name .":";
            if ( $value ) print $value;
            else print "not found";
        }

    }

    /**
     * тестирование CatalogRegComp класса
     * ./yiic test catalogregcomp
     */
    public function actionCatalogregcomp() {

        //CatalogRegComp::getCountries();
        //print_r(CatalogRegComp::$countries);

        //получить список всех секторов
        $catalog_sectors = CatalogReg::getSectorsList();

        // создание объекта
        $obj = new CatalogRegComp($catalog_sectors[0], '2');

        print "tid:".$catalog_sectors[0]->tid. " parent:". $catalog_sectors[0]->parent."\n";

        // печать названия сектора
        print $obj->getSectorsName() ."\n";

        // получение условий
        print $obj->getSectorCondition() ."\n";

        // получение списка стран
        print_r( $obj->getRegionsForSearch() );

    }

    /**
     * Тестирование запроса поиска детей региона
     * ./yiic test gettermchild --tid=28767
     */
    public function actionGettermchild($tid) {
//        $model = TermData::model()->with(array('hierarchy' =>
//                                            array('condition' => 'hierarchy.parent = :parent',
//                                                  'params' => array(':parent' => $tid)),
//                                              ));
        $model = TermData::model()->with('childs')->findByPk($tid);

        //print $model->tid ." ". $model->hierarchy->parent ."\n";
        foreach ( $model->childs as $child ) print $child->tid ." ";
    }

    /**
     * тестирование catalogreg класса
     * ./yiic test catalogreg
     */
    public function actionCatalogreg() {
        //получить список всех секторов
        $catalog_sectors = CatalogReg::getSectorsList();

        // создание объекта
        $obj = new CatalogReg($catalog_sectors[0], 'comp_reg1');

        // печать названия сектора
        print $obj->getSectorsName() ."\n";

        // получение условий
        print $obj->getSectorCondition() ."\n";

    }


    /**
     * Тестовое  действие
     * @TODO проверка работы
     */
    public function actionTest() {
            $parent = new stdClass();
            print_r($parent);

        
    }

    /**
     * This method is invoked right after an action finishes execution.
     * You may override this method to do some postprocessing for the action.
     * @param string $action the action name
     * @param array $params the parameters to be passed to the action method.
     */
    protected function afterAction($action,$params)
    {
        $stats = Yii::app()->db->getStats();
        print "\n==========================\n";
        print "Query count:". $stats[0];
        print "\n";
    }
}
?>
