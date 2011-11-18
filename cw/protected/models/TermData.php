<?php

/**
 * This is the model class for table "term_data".
 *
 * The followings are the available columns in table 'term_data':
 * @property string $tid
 * @property string $vid
 * @property string $name
 * @property string $description
 * @property integer $weight
 */
class TermData extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TermData the static model class
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
		return 'term_data';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('weight', 'numerical', 'integerOnly'=>true),
			array('vid', 'length', 'max'=>10),
			array('name', 'length', 'max'=>255),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tid, vid, name, description, weight', 'safe', 'on'=>'search'),
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
                    // связь term_data с term_data через term_hierarchy
                    'hierarchy2term' => array(self::MANY_MANY, 'TermData', 'term_hierarchy(tid, parent)'),
                    // получить родителя
                    'hierarchy' => array(self::HAS_ONE, 'TermHierarchy', 'tid'),
                    // получить список детей
                    'childs' => array(self::HAS_MANY, 'TermHierarchy', 'parent'),
		);

                /*
                SELECT td.tid tid, td.name name, th.parent parent, td2.name as parentname
                FROM term_data td
                LEFT JOIN term_hierarchy th ON(td.tid = th.tid)
                LEFT JOIN term_data as td2 ON td2.tid = th.parent
                WHERE
                td.vid = 7
                 */
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'tid' => 'Tid',
			'vid' => 'Vid',
			'name' => 'Name',
			'description' => 'Description',
			'weight' => 'Weight',
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
		$criteria->compare('vid',$this->vid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('weight',$this->weight);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}