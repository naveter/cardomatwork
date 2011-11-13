<?php

/**
 * This is the model class for table "cf_catalog_region".
 *
 * The followings are the available columns in table 'cf_catalog_region':
 * @property string $tid
 * @property string $ptid
 * @property string $comp_reg1
 * @property string $comp_reg2
 * @property string $comp_reg3
 * @property string $card_reg1
 * @property string $card_reg2
 * @property string $card_reg3
 */
class CatalogRegion extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CatalogRegion the static model class
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
		return 'cf_catalog_region';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('comp_reg1, comp_reg2, comp_reg3, card_reg1, card_reg2, card_reg3', 'required'),
			array('tid, ptid', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tid, ptid, comp_reg1, comp_reg2, comp_reg3, card_reg1, card_reg2, card_reg3', 'safe', 'on'=>'search'),
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
			'comp_reg1' => 'Comp Reg1',
			'comp_reg2' => 'Comp Reg2',
			'comp_reg3' => 'Comp Reg3',
			'card_reg1' => 'Card Reg1',
			'card_reg2' => 'Card Reg2',
			'card_reg3' => 'Card Reg3',
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
		$criteria->compare('comp_reg1',$this->comp_reg1,true);
		$criteria->compare('comp_reg2',$this->comp_reg2,true);
		$criteria->compare('comp_reg3',$this->comp_reg3,true);
		$criteria->compare('card_reg1',$this->card_reg1,true);
		$criteria->compare('card_reg2',$this->card_reg2,true);
		$criteria->compare('card_reg3',$this->card_reg3,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}