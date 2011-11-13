<?php

/**
 * This is the model class for table "cf_compsector".
 *
 * The followings are the available columns in table 'cf_compsector':
 * @property string $companyid
 * @property string $b1
 * @property string $s1
 * @property string $b2
 * @property string $s2
 * @property string $b3
 * @property string $s3
 */
class Compsector extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Compsector the static model class
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
		return 'cf_compsector';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('companyid, b1, s1, b2, s2, b3, s3', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('companyid, b1, s1, b2, s2, b3, s3', 'safe', 'on'=>'search'),
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
			'companyid' => 'Companyid',
			'b1' => 'B1',
			's1' => 'S1',
			'b2' => 'B2',
			's2' => 'S2',
			'b3' => 'B3',
			's3' => 'S3',
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

		$criteria->compare('companyid',$this->companyid,true);
		$criteria->compare('b1',$this->b1,true);
		$criteria->compare('s1',$this->s1,true);
		$criteria->compare('b2',$this->b2,true);
		$criteria->compare('s2',$this->s2,true);
		$criteria->compare('b3',$this->b3,true);
		$criteria->compare('s3',$this->s3,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}