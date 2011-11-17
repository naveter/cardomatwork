<?php
// CatalogReg.php UTF-8 17.11.2011 17:38:20 rem
// базовый класс для пересчёта кол-ва компаний и визиток для каталога

class CatalogReg {

    protected $regarray = array(); // массив с коллекцией для записи

    protected $field = ''; // поле, которое будет записываться

    protected $CatalogSectorObj; // объект CatalogSector

    public static $countries; // список стран, в которых есть компании

    /**
     * конструктор
     * @param CatalogSector
     * @param string поле, которое будет записываться
     */
    public function __construct($CatalogSectorObj, $field) {
        if ( get_class($CatalogSectorObj) != 'CatalogSector' )
            throw new Exception("First parameter must be a catalogSector object");

        $this->CatalogSectorObj = $CatalogSectorObj;
        $this->field = $field;
    }

    /**
     * получение названия сектора
     * @param string название секторов
     */
    public function getSectorsName() {
        $sector_name = $this->CatalogSectorObj->title;
        $this->CatalogSectorObj->parent ? $sector_name = $this->CatalogSectorObj->ptitle ." -> ". $sector_name : 0;
        return $sector_name;
    }

    /**
     * возвращение условия для секторов
     * @return string условие для вставки в запрос
     */
    public function getSectorCondition() {
        $condition = "";
        $this->CatalogSectorObj->parent == 0 ?
                                  $condition = 'sectors.b1 = :s OR sectors.b2 = :s OR sectors.b3 = :s'
                                : $condition = '(sectors.b1 = :b AND sectors.s1 = :s)
                                                OR (sectors.b2 = :b AND sectors.s2 = :s)
                                                OR (sectors.b3 = :b AND sectors.s3 = :s)';
        return $condition;
    }

    /**
     * запись поля в cf_catalog_region
     */
    public function writeRegToDb() {
        if ( count($this->regarray) == 0 ) return;

        // есть ли такая запись в cf_company_region
        $model = CatalogRegion::model()->findByPk(array('tid' => $this->CatalogSectorObj->tid, 'ptid' => $this->CatalogSectorObj->parent));

        if ( $model ) {
            $model->$this->field = json_encode($this->regarray);
            $model->save();
            return;
        }

        $model = new CatalogRegion();
        $model->tid = $this->CatalogSectorObj->tid;
        $model->ptid = $this->CatalogSectorObj->parent;
        $model->$this->field = json_encode($this->regarray);
        $model->save();            
    }


    /**
     * возвращает список секторов из cf_catalog_sector
     * @return CatalogSector
     */
    public static function getSectorsList() {
        return CatalogSector::model()->findAll(array("order" => "ptitle, title"));
    }

    /**
     * возвращает список стран
     * @return TermData список стран
     */
    public static function getAllCountries() {
        return TermData::model()->with(array('hierarchy' => array(
                                            'select' => false,
                                            'condition' => 'hierarchy.parent = 0'
                                        )))->findAllByAttributes(array('vid' => Variable::getVariable('cfcompany_vocabulary_region')));
    }

    /**
     * метод для переопределения
     * возвращает список стран,  где есть компании
     */
    public static function getCountries() {

    }
    

}




?>
