<?php

/**
 * This is the model class for table "{{product_to_filter}}".
 *
 * The followings are the available columns in table '{{product_to_filter}}':
 * @property string $product_id
 * @property string $value_id
 * @property ProductFilterValue $productFilter
 */
class ProductToFilterValue extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{product_to_filter_value}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, value_id', 'required'),
			array('product_id, value_id', 'length', 'max'=>10),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'productFilterValue'=>array(self::BELONGS_TO, 'ProductFilterValue', 'value_id')
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProductToFilter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public static function valuesByProduct($id){
        return db()->createCommand()->select('pfv.id, pfv.name')
            ->from('{{product_to_filter_value}} ptfv')
            ->join('{{product_filter_value}} pfv', 'pfv.id = ptfv.value_id')
            ->where('product_id=:pid',[':pid'=>$id])
            ->order('pfv.name')
            ->queryAll();
    }

}
