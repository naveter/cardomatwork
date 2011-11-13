<?php

/**
 * This is the model class for table "cf_company".
 *
 * The followings are the available columns in table 'cf_company':
 * @property string $id
 * @property integer $isarch
 * @property string $archdate
 * @property string $adddate
 * @property string $adduser
 * @property integer $isold
 * @property string $olddate
 * @property integer $revision_id
 */
class Company extends CActiveRecord
{

        const ISARCH_TRUE = 1;
        const ISARCH_FALSE = 0;
        const ISOLD_TRUE = 1;
        const ISOLD_FALSE = 0;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Company the static model class
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
		return 'cf_company';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, isarch, archdate, adddate, adduser, isold, olddate, revision_id', 'numerical', 'integerOnly'=>true),
			// Please remove those attributes that should not be searched.
			array('id, isarch, archdate, adddate, adduser, isold, olddate, revision_id', 'safe', 'on'=>'search'),
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
                    // получение текущей ревизии данной компании
                    'revision' => array(self::BELONGS_TO, 'CompanyRevision', 'revision_id'),
                    // получение всех ревизий компании
                    'revisions' => array(self::HAS_MANY, 'CompanyRevision', 'company', 'order'=>'revisions.editdate DESC'),
                    // связь с визитками данной компании
                    'cards' => array(self::HAS_MANY, 'Card', 'companyid', 'order'=>'cards.adddate DESC'),
                    // сколько визиток есть у компании
                    'cardsCount' => array(self::STAT, 'Card', 'companyid', 'condition'=>'isarch='.Card::ISARCH_FALSE),
		);
	}
        
        // именованные условия для получения данных
        public function scopes()
        {
            return array(
//                'published'=>array(
//                    'condition'=>'status=1',
//                ),
//                'recently'=>array(
//                    'order'=>'create_time DESC',
//                    'limit'=>5,
//                ),
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
			'adddate' => 'Adddate',
			'adduser' => 'Adduser',
			'isold' => 'Isold',
			'olddate' => 'Olddate',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('isarch',$this->isarch);
		$criteria->compare('archdate',$this->archdate,true);
		$criteria->compare('adddate',$this->adddate,true);
		$criteria->compare('adduser',$this->adduser,true);
		$criteria->compare('isold',$this->isold);
		$criteria->compare('olddate',$this->olddate,true);
		$criteria->compare('revision_id',$this->revision_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}