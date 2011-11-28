<?php

/**
 * This is the model class for table "cf_catalog_sector".
 *
 * The followings are the available columns in table 'cf_catalog_sector':
 * @property string $tid
 * @property string $url_translit
 * @property string $card_title
 * @property string $card_comment
 * @property string $comp_title
 * @property string $comp_comment
 * @property string $parent
 * @property string $ptitle
 */
class CatalogSector extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CatalogSector the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cf_catalog_sector';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tid, parent', 'numerical', 'integerOnly'=>true),
			array('url_translit, card_title, comp_title, ptitle', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tid, url_translit, card_title, card_comment, comp_title, comp_comment, parent, ptitle', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

        // переопределение первичного ключа
        public function primaryKey()
        {
            // Для составного первичного ключа следует использовать массив:
            return array('tid', 'parent');
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'tid' => 'Tid',
			'url_translit' => 'Url Translit',
			'card_title' => 'Card Title',
			'card_comment' => 'Card Comment',
			'comp_title' => 'Comp Title',
			'comp_comment' => 'Comp Comment',
			'parent' => 'Parent',
			'ptitle' => 'Ptitle',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('tid',$this->tid,true);
		$criteria->compare('url_translit',$this->url_translit,true);
		$criteria->compare('card_title',$this->card_title,true);
		$criteria->compare('card_comment',$this->card_comment,true);
		$criteria->compare('comp_title',$this->comp_title,true);
		$criteria->compare('comp_comment',$this->comp_comment,true);
		$criteria->compare('parent',$this->parent,true);
		$criteria->compare('ptitle',$this->ptitle,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}