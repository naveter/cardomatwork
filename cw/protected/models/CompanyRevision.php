<?php

/**
 * This is the model class for table "cf_company_revision".
 *
 * The followings are the available columns in table 'cf_company_revision':
 * @property string $id
 * @property string $company
 * @property string $opf
 * @property string $url
 * @property string $name
 * @property string $reg1
 * @property string $reg2
 * @property string $reg3
 * @property string $zipcode
 * @property string $employ
 * @property string $profit
 * @property string $phone1
 * @property string $phone2
 * @property string $fax
 * @property string $address
 * @property string $author
 * @property string $editdate
 */
class CompanyRevision extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CompanyRevision the static model class
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
		return 'cf_company_revision';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('company, opf, reg1, reg2, reg3, employ, profit, author, editdate', 'length', 'max'=>10),
//			array('url', 'length', 'max'=>150),
//			array('name, address', 'length', 'max'=>250),
//			array('zipcode', 'length', 'max'=>15),
//			array('phone1, phone2, fax', 'length', 'max'=>45),
//			// The following rule is used by search().
//			// Please remove those attributes that should not be searched.
//			array('id, company, opf, url, name, reg1, reg2, reg3, zipcode, employ, profit, phone1, phone2, fax, address, author, editdate', 'safe', 'on'=>'search'),
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
                    'company' => array(self::BELONGS_TO, 'Company', 'company'),

                    // связка с секторами
                    'sectors' => array(self::HAS_ONE, 'Compsector', 'companyid'),

                    // таксономия
                    'term_opf' => array(self::BELONGS_TO, 'TermData', 'opf'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'company' => 'Company',
			'opf' => 'Opf',
			'url' => 'Url',
			'name' => 'Name',
			'reg1' => 'Reg1',
			'reg2' => 'Reg2',
			'reg3' => 'Reg3',
			'zipcode' => 'Zipcode',
			'employ' => 'Employ',
			'profit' => 'Profit',
			'phone1' => 'Phone1',
			'phone2' => 'Phone2',
			'fax' => 'Fax',
			'address' => 'Address',
			'author' => 'Author',
			'editdate' => 'Editdate',
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
		$criteria->compare('company',$this->company,true);
		$criteria->compare('opf',$this->opf,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('reg1',$this->reg1,true);
		$criteria->compare('reg2',$this->reg2,true);
		$criteria->compare('reg3',$this->reg3,true);
		$criteria->compare('zipcode',$this->zipcode,true);
		$criteria->compare('employ',$this->employ,true);
		$criteria->compare('profit',$this->profit,true);
		$criteria->compare('phone1',$this->phone1,true);
		$criteria->compare('phone2',$this->phone2,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('editdate',$this->editdate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}