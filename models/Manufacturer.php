<?php

/**
 * This is the model class for table "{{product_manufacturer}}".
 *
 * The followings are the available columns in table '{{product_manufacturer}}':
 * @property string $id
 * @property string $name
 * @property string $short_name
 */
class Manufacturer extends CrudActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{product_manufacturer}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['name', 'required'],
			['name, short_name', 'length', 'max'=>255],
			['id, name, short_name', 'safe', 'on'=>'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
            'products' => [self::HAS_MANY, 'Product', 'manufacturer_id'],
            'productsCount' => [self::STAT, 'Product', 'manufacturer_id'],
        ];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => t('Name'),
			'short_name' => t('Short name'),
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

        $criteria->with = ['productsCount'];
		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('short_name',$this->short_name,true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Manufacturer the static model class
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
                'upload'=>[
                    'class'=>'upload.components.UploadBehavior',
                    'folder'=>'manufacturers',
                    'defaultUploadField'=>'image_id',
                ],
                'aliasBehavior'=>[
                    'class'=>'AliasBehavior',
                    'sourceAttribute'=>'name',
                    'aliasAttribute'=>'alias',
                ],
                'ml' => [
                    'class' => 'MultilingualBehavior',
                    'langTableName' => 'product_manufacturer_lang',
                    'langForeignKey' => 'entity_id',
                    'localizedAttributes' => [
                        'name',
                        'short_name',
                    ],
                    'languages' => Lang::getLanguages(), // array of your translated languages. Example : ['fr' => 'Français', 'en' => 'English')
                    'defaultLanguage' => Lang::getDefault(), //your main language. Example : 'fr'
                    'dynamicLangClass' => true,
                ],
    	]);
    }


}
