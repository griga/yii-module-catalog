<?php

/**
 * This is the model class for table "{{product_rotation}}".
 *
 * The followings are the available columns in table '{{product_rotation}}':
 * @property integer $id
 * @property integer $product_id
 * @property integer $image_id
 * @property string $meta
 * @property integer $sort
 * @property string $create_time
 * @property string $update_time
 *
 * The followings are the available model relations:
 * @property Product $product
 */
class Rotation extends CrudActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{product_rotation}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			['product_id', 'required'],
			['product_id, filter_value_id, image_id, sort', 'numerical', 'integerOnly'=>true],
			['meta, create_time, update_time', 'safe'],
			['id, product_id, image_id, meta, sort, create_time, update_time', 'safe', 'on'=>'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
			'product' => [self::BELONGS_TO, 'Product', 'product_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'product_id' => t('Product'),
			'filter_value_id' => t('Filter value'),
			'image_id' => t('Image'),
			'meta' => t('Meta'),
			'sort' => t('Sort'),
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
		];
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

		$criteria=new CDbCriteria;
        
        $criteria->with = ['product'];

		$criteria->compare('id',$this->id);
		$criteria->compare('product.name',$this->product_id, true);
		$criteria->compare('image_id',$this->image_id);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Rotation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * return behaviors of component merged with parent component behaviors
     * @return array CBehavior[]
     */

    public function behaviors(){
    	return CMap::mergeArray(
    		parent::behaviors(),
    		array(
                'CTimestampBehavior' => [
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'setUpdateOnCreate'=>true,
                ],
                'upload'=>[
                    'class'=>'upload.components.UploadBehavior',
                    'folder'=>'rotations',
                    'defaultUploadField'=>'image_id',
                ],
    	));
    }
}
