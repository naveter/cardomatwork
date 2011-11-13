<?php

/**
 * This is the model class for table "cf_catalog_count".
 *
 * The followings are the available columns in table 'cf_catalog_count':
 * @property string $tid
 * @property string $ptid
 * @property integer $comp
 * @property integer $card
 */
class CatalogCount extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CatalogCount the static model class
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
		return 'cf_catalog_count';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('comp, card', 'numerical', 'integerOnly'=>true),
			array('tid, ptid', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tid, ptid, comp, card', 'safe', 'on'=>'search'),
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'tid' => 'Tid',
			'ptid' => 'Ptid',
			'comp' => 'Comp',
			'card' => 'Card',
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
		$criteria->compare('ptid',$this->ptid,true);
		$criteria->compare('comp',$this->comp);
		$criteria->compare('card',$this->card);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}