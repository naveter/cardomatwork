<?php
// CatalogRegComp.php UTF-8 17.11.2011 17:37:54 rem
// заполнение счётчика компаний в секторах


class CatalogRegComp extends CatalogReg {

    /**
     * конструктор
     * @param CatalogSector
     * @param string  какой вид региона обрабатывать: reg1, reg2, reg3
     */
    public function  __construct($CatalogSectorObj, $regtype) {
        if ( !in_array($regtype, array('reg1', 'reg2', 'reg3')) )
                throw new Exception("Second perametr must be reg1, reg2 or reg3");
        
        parent::__construct($CatalogSectorObj, "comp_".$regtype);
    }

    /**
     * исполнение абстрактного метода
     * получает список стран,  где есть компании и помещает их в статику
     */
    public static function getCountries() {
        if ( is_array(self::$countries) ) return;

        $termcountries = self::getAllCountries();
        self::$countries = array();

        foreach ( $termcountries as $country ) {
            $count = Company::model()->with(array('revision' => array(
                                                    'select' => false,
                                                    'condition' => 'reg1 = :reg1',
                                                    'params' => array(':reg1' => $country->tid),
                                                  )))->count();

            if ( $count > 0 ) array_push(self::$countries, $country->tid);
        }
    }


}


?>
