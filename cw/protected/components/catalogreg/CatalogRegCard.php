<?php
// CatalogRegCard.php UTF-8 17.11.2011 17:37:54 rem
// заполнение счётчика визиток в секторах


class CatalogRegCard extends CatalogReg {

    // числовое значение номера региона
    public $typenum;

    /**
     * конструктор
     * @param CatalogSector
     * @param integer  какой номер региона обрабатывать: 1, 2 или 3
     */
    public function  __construct($CatalogSectorObj, $typenum) {
        $typenum = (int) $typenum;
        if ( $typenum < 1 || $typenum > 3 )
                throw new Exception("Second perametr must be 1,2 or 3");
        
        parent::__construct($CatalogSectorObj, "card_reg". $typenum);

        $this->typenum = $typenum;
    }

    /**
     * исполнение абстрактного метода
     * получает список стран,  где есть визитки и помещает их в статику
     */
    public static function getCountries() {
        if ( is_array(self::$countries) ) return;

        $termcountries = self::getAllCountries();
        self::$countries = array();

        foreach ( $termcountries as $country ) {
            $count = Card::model()->with(array('revision' => array(
                                                    'select' => false,
                                                    'condition' => 'reg1 = :reg1',
                                                    'params' => array(':reg1' => $country->tid),
                                                  )))->count();

            if ( $count > 0 ) self::$countries['k0'][] = $country->tid;
        }
    }

    /**
     * Получение списка регионов, в которых нужно искать визитки
     * @return array of regions values
     */
    public function getRegionsForSearch() {
        // если это первый уровень регионов, то получаю список стран
        if ( $this->typenum == 1 ) {
            self::getCountries();
            return self::$countries;
        }
        else {
            $childs_arr = array(); // коллекция

            // получение массива с родительскими регионами
            $parentreg =  $this->getRegionsFromBD('card_reg'. ($this->typenum - 1) );
            if ( count($parentreg) == 0 ) return $childs_arr;

            foreach ( $parentreg as $preg ) {
                // получение детей
                $model = TermData::model()->with('childs')->findByPk($preg);

                if ( $model && $model->childs )
                    foreach ( $model->childs as $child ) $childs_arr['k'.$preg][] = $child->tid;
            }

            return $childs_arr;
        }
    }

    /**
     * Запуск проверки визитки в данном секторе
     */
     public function checkRegions() {
         // получение списка условий
         $conditions = $this->getSectorCondition();

         // получение списка регионов
         $regions = $this->getRegionsForSearch();

         if ( count($regions) == 0 ) return;

         // проход все подрегионов и регионов
         foreach ( $regions as $parentreg => $childarr ) {
             foreach ( $childarr as $reg ) {
                $count = Card::model()->with(array('revision' => array(
                                                        'select' => false,
                                                        'condition' => 'reg'. $this->typenum .' = :reg',
                                                        'params' => array(':reg' => $reg),
                                                   ),
                                                    'company.revision.sectors' => array(
                                                        'condition' => $conditions,
                                                        'params' => array(':s' => $this->CatalogSectorObj->tid, ':b' => $this->CatalogSectorObj->parent)
                                                   )
                                                   ))->count();

                if ( $count > 0 ) $this->regarray[ $parentreg ][ 'k'.$reg ] = $count;
             }
         }

         // сохранение данных
         $this->writeRegToDb();
     }
    


}


?>
