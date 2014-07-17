<?php

/**
 * This is the model class for table "{{product_filter_value}}".
 *
 * The followings are the available columns in table '{{product_filter_value}}':
 * @property integer $id
 * @property integer $filter_id
 * @property string $name
 * @property string $key
 */
class ProductFilterValue extends CrudActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{product_filter_value}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			['name, key, filter_id', 'required'],
			['filter_id', 'numerical', 'integerOnly'=>true],
			['name, key', 'length', 'max'=>255],
			['id, filter_id, name, key', 'safe', 'on'=>'search'],
		];
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'filter_id' => t('Filter'),
			'name' => t('Name'),
			'key' => t('Key'),
		];
	}

	/**
     * @param mixed $filter_id
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
	public function search($filter_id = false)
	{
        $criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
        if ($filter_id) {
            $criteria->compare('filter_id', $filter_id);
        } else {
            $criteria->compare('filter_id', $this->filter_id);
        }

		$criteria->compare('name',$this->name,true);
		$criteria->compare('key',$this->key,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
            'pagination'=>[
                'pageSize'=>100
            ]
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProductFilterValue the static model class
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
    		[
                'ml' => [
                    'class' => 'MultilingualBehavior',
                    'langTableName' => 'product_filter_value_lang',
                    'langForeignKey' => 'entity_id',
                    'localizedAttributes' => [
                        'name',
                    ],
                    'languages' => Lang::getLanguages(), // array of your translated languages. Example : ['fr' => 'Français', 'en' => 'English')
                    'defaultLanguage' => Lang::getDefault(), //your main language. Example : 'fr'
                    'dynamicLangClass' => true,
                ],
    	]);
    }
}
