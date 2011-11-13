<?php

/**
 * This is the model class for table "variable".
 *
 * The followings are the available columns in table 'variable':
 * @property string $name
 * @property string $value
 */
class Variable extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Variable the static model class
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
		return 'variable';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('value', 'required'),
			array('name', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, value', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'value' => 'Value',
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

		$criteria->compare('name',$this->name,true);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        // поиск переменной
        public static function getVariable($name) {
            $model = self::model()->find('name=:name', array(':name'=>$name));

            if ( $model ) return unserialize($model->attributes['value']);
            else return null;
        }

        // установка новой или изменение существующей переменной
        public static function setVariable($name, $value) {
            $model = self::model()->find('name=:name', array(':name'=>$name));
            // если есть такая переменная
            if ( $model ) {
                $model->value = serialize($value);
            }
            // если нет
            else {
                $model = new Variable;
                $model->name = $name;
                $model->value = serialize($value);
            }
            
            $model->save();
        }

}