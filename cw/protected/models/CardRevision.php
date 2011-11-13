<?php

/**
 * This is the model class for table "cf_card_revision".
 *
 * The followings are the available columns in table 'cf_card_revision':
 * @property integer $id
 * @property integer $parent
 * @property integer $owner
 * @property string $firstname
 * @property string $lastname
 * @property string $middlename
 * @property string $position
 * @property integer $profarea1
 * @property integer $profarea2
 * @property integer $hierachyid
 * @property string $departament
 * @property string $phone1
 * @property string $phone2
 * @property integer $reg1
 * @property integer $reg2
 * @property integer $reg3
 * @property string $zipcode
 * @property string $address
 * @property integer $author
 * @property integer $editdate
 * @property string $skype
 * @property string $icq
 * @property string $url
 */
class CardRevision extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CardRevision the static model class
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
		return 'cf_card_revision';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('owner, firstname, lastname, position, hierachyid, reg1, author, editdate', 'required'),
			array('parent, owner, profarea1, profarea2, hierachyid, reg1, reg2, reg3, author, editdate', 'numerical', 'integerOnly'=>true),
			array('firstname, lastname, middlename, phone1, phone2', 'length', 'max'=>100),
			array('position, departament, address, url', 'length', 'max'=>250),
			array('zipcode', 'length', 'max'=>15),
			array('skype, icq', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parent, owner, firstname, lastname, middlename, position, profarea1, profarea2, hierachyid, departament, phone1, phone2, reg1, reg2, reg3, zipcode, address, author, editdate, skype, icq, url', 'safe', 'on'=>'search'),
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
                    'card' => array(self::BELONGS_TO, 'Card', 'parent'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent' => 'Parent',
			'owner' => 'Owner',
			'firstname' => 'Firstname',
			'lastname' => 'Lastname',
			'middlename' => 'Middlename',
			'position' => 'Position',
			'profarea1' => 'Profarea1',
			'profarea2' => 'Profarea2',
			'hierachyid' => 'Hierachyid',
			'departament' => 'Departament',
			'phone1' => 'Phone1',
			'phone2' => 'Phone2',
			'reg1' => 'Reg1',
			'reg2' => 'Reg2',
			'reg3' => 'Reg3',
			'zipcode' => 'Zipcode',
			'address' => 'Address',
			'author' => 'Author',
			'editdate' => 'Editdate',
			'skype' => 'Skype',
			'icq' => 'Icq',
			'url' => 'Url',
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
		$criteria->compare('parent',$this->parent);
		$criteria->compare('owner',$this->owner);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('middlename',$this->middlename,true);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('profarea1',$this->profarea1);
		$criteria->compare('profarea2',$this->profarea2);
		$criteria->compare('hierachyid',$this->hierachyid);
		$criteria->compare('departament',$this->departament,true);
		$criteria->compare('phone1',$this->phone1,true);
		$criteria->compare('phone2',$this->phone2,true);
		$criteria->compare('reg1',$this->reg1);
		$criteria->compare('reg2',$this->reg2);
		$criteria->compare('reg3',$this->reg3);
		$criteria->compare('zipcode',$this->zipcode,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('author',$this->author);
		$criteria->compare('editdate',$this->editdate);
		$criteria->compare('skype',$this->skype,true);
		$criteria->compare('icq',$this->icq,true);
		$criteria->compare('url',$this->url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}