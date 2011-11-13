<?php

/**
 * This is the model class for table "vocabulary".
 *
 * The followings are the available columns in table 'vocabulary':
 * @property string $vid
 * @property string $name
 * @property string $description
 * @property string $help
 * @property integer $relations
 * @property integer $hierarchy
 * @property integer $multiple
 * @property integer $required
 * @property integer $tags
 * @property string $module
 * @property integer $weight
 */
class Vocabulary extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Vocabulary the static model class
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
		return 'vocabulary';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('relations, hierarchy, multiple, required, tags, weight', 'numerical', 'integerOnly'=>true),
			array('name, help, module', 'length', 'max'=>255),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('vid, name, description, help, relations, hierarchy, multiple, required, tags, module, weight', 'safe', 'on'=>'search'),
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
                    'terms' => array(self::HAS_MANY, 'TermData', 'vid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'vid' => 'Vid',
			'name' => 'Name',
			'description' => 'Description',
			'help' => 'Help',
			'relations' => 'Relations',
			'hierarchy' => 'Hierarchy',
			'multiple' => 'Multiple',
			'required' => 'Required',
			'tags' => 'Tags',
			'module' => 'Module',
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

		$criteria->compare('vid',$this->vid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('help',$this->help,true);
		$criteria->compare('relations',$this->relations);
		$criteria->compare('hierarchy',$this->hierarchy);
		$criteria->compare('multiple',$this->multiple);
		$criteria->compare('required',$this->required);
		$criteria->compare('tags',$this->tags);
		$criteria->compare('module',$this->module,true);
		$criteria->compare('weight',$this->weight);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}