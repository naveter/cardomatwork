<?php

/**
 * This is the model class for table "cf_card".
 *
 * The followings are the available columns in table 'cf_card':
 * @property integer $id
 * @property integer $isarch
 * @property integer $archdate
 * @property integer $isold
 * @property integer $olddate
 * @property integer $adduser
 * @property integer $adddate
 * @property string $email
 * @property integer $companyid
 * @property integer $revision_id
 */
class Card extends CActiveRecord
{

        const ISARCH_TRUE = 1;
        const ISARCH_FALSE = 0;
        const ISOLD_TRUE = 1;
        const ISOLD_FALSE = 0;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Card the static model class
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
		return 'cf_card';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('isarch, archdate, isold, olddate, adduser, adddate, companyid, revision_id', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>100),
                        array('email','email'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, isarch, archdate, isold, olddate, adduser, adddate, email, companyid, revision_id', 'safe', 'on'=>'search'),
                        array('url','url'),
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
                    // получение текущей ревизии данной визитки
                    'revision' => array(self::BELONGS_TO, 'CardRevision', 'revision_id'),
                    // получение всех ревизий компании
                    'revisions' => array(self::HAS_MANY, 'CardRevision', 'parent', 'order'=>'revisions.editdate DESC'),
                    // принадлежность компании
                    'company' => array(self::BELONGS_TO, 'Company', 'companyid'),
		);
	}

        public function scopes()
        {
            return array(
                // визитки, которые не в архиве
                'noisarch'=>array(
                    'condition'=>'isarch='. Card::ISARCH_FALSE,
                ),
            );
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'isarch' => 'Isarch',
			'archdate' => 'Archdate',
			'isold' => 'Isold',
			'olddate' => 'Olddate',
			'adduser' => 'Adduser',
			'adddate' => 'Adddate',
			'email' => 'Email',
			'companyid' => 'Companyid',
			'revision_id' => 'Revision',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('isarch',$this->isarch);
		$criteria->compare('archdate',$this->archdate);
		$criteria->compare('isold',$this->isold);
		$criteria->compare('olddate',$this->olddate);
		$criteria->compare('adduser',$this->adduser);
		$criteria->compare('adddate',$this->adddate);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('companyid',$this->companyid);
		$criteria->compare('revision_id',$this->revision_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}