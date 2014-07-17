<?php

/**
 * This is the model class for table "{{product_characteristics}}".
 *
 * The followings are the available columns in table '{{product_characteristics}}':
 * @property string $id
 * @property string $name
 * @property string $key
 * @property integer $type
 * @property string $data
 * @property ProductFilterValue[] $values
 * @property ProductCategory[] $models
 */
class ProductFilter extends CrudActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{product_filter}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			['name, key', 'required'],
            ['key','unique'],
			['type', 'numerical', 'integerOnly'=>true],
			['name, key', 'length', 'max'=>255],
			['data', 'safe'],
			['id, name, key, type, data', 'safe', 'on'=>'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
            'values'=>[self::HAS_MANY,'ProductFilterValue','filter_id'],
            'categories'=>[self::MANY_MANY,
                'ProductCategory',
                '{{product_category_to_filter}}(filter_id,category_id)'],
		];
	}

    public function getFiltersValuesDataProvider(){
        $dataProvider =  new CArrayDataProvider('ProductFilterValue');
        $dataProvider->setData($this->values);
        return $dataProvider;
    }

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => t('Name'),
			'key' => t('Key'),
			'type' => t('Type'),
			'data' => t('Data'),
			'categories' => t('Categories'),
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('key',$this->key,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('data',$this->data,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProductFilter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    const TYPE_RANGE = 1;
    const TYPE_SELECT = 2;

    public static function getTypes(){
        return [
            self::TYPE_RANGE => t('Value'),
            self::TYPE_SELECT => t('List of values'),
        ];
    }

    public function getTypeName(){
        $types = self::getTypes();
        return $types[$this->type];
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
                    'langTableName' => 'product_filter_lang',
                    'langForeignKey' => 'entity_id',
                    'localizedAttributes' => [
                        'name',
                    ],
                    'languages' => Lang::getLanguages(), // array of your translated languages. Example : ['fr' => 'Français', 'en' => 'English')
                    'defaultLanguage' => Lang::getDefault(), //your main language. Example : 'fr'
                    'dynamicLangClass' => true,
                ],
                'mtms'=>[
                    'class'=>'ManyToManySaveBehavior'
                ]
    	]);
    }

}
