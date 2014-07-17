<?php

/**
 * This is the model class for table "{{product_related}}".
 *
 * The followings are the available columns in table '{{product_related}}':
 * @property integer $id
 * @property string $entity
 * @property integer $entity_id
 * @property integer $product_id
 * @property string $meta
 * @property integer $sort
 */
class ProductRelated extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{product_related}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('entity, entity_id, product_id', 'required'),
			array('entity_id, product_id, sort', 'numerical', 'integerOnly'=>true),
			array('entity', 'length', 'max'=>255),
			array('meta', 'safe'),
			array('id, entity, entity_id, product_id, meta, sort', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'entity' => 'Entity',
			'entity_id' => 'Entity',
			'product_id' => 'Product',
			'meta' => 'Meta',
			'sort' => 'Sort',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('entity',$this->entity,true);
		$criteria->compare('entity_id',$this->entity_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('sort',$this->sort);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProductRelated the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
