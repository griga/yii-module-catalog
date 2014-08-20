<?php

/**
 * This is the model class for table "{{product_category}}".
 *
 * The followings are the available columns in table '{{product_category}}':
 * @property string $id
 * @property string $name
 * @property string $content
 * @property string $short_content
 * @property string $alias
 * @property string $uid
 * @property integer $parent_id
 * @property integer $sort
 * @property integer $enabled
 * @property integer $status
 *
 * @property integer $remains_warning
 *
 * @property string $childrenCount
 * @property ProductCategory[]|LinkableBehavior[]|UploadBehavior[] $children
 *
 * @property ProductCategory $parent
 *
 * @property Product[] $products
 * @property ProductFilter[] $filters
 * @property ProductFilter[] $activeFilters
 */
class ProductCategory extends CrudActiveRecord
{
    /**
     * @param string $className
     * @return ProductCategory
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{product_category}}';
    }

    public function behaviors()
    {
        return [
            'CTimestampBehavior' => [
                'class' => 'zii.behaviors.CTimestampBehavior',
                'timestampExpression'=> new CDbExpression( DbHelper::timestampExpression()),
                'setUpdateOnCreate'=>true,
            ],
            'upload'=>[
                'class'=>'upload.components.UploadBehavior',
                'folder'=>'categories',
                'defaultUploadField'=>'image_id'
           ],
            'linkable'=>[
                'class'=>'LinkableBehavior',
                'urlRule'=>function($model){
                    return ($model->alias ? "/{$model->alias}/" : "/" ) . "c". $model->id;
                },
           ],
            'aliasBehavior'=>[
                'class'=>'AliasBehavior',
                'sourceAttribute'=>'name',
                'aliasAttribute'=>'alias',
            ],
            'ml' => [
                'class' => 'MultilingualBehavior',
                'langTableName' => 'product_category_lang',
                'langForeignKey' => 'entity_id',
                'localizedAttributes' => [
                    'name',
                    'content',
                    'short_content',
                ],
                'languages' => Lang::getLanguages(), // array of your translated languages. Example : ['fr' => 'Français', 'en' => 'English')
                'defaultLanguage' => Lang::getDefault(), //your main language. Example : 'fr'
                'dynamicLangClass' => true,
            ],
            'mtms'=>[
                'class'=>'ManyToManySaveBehavior'
            ],
            'customfield'=>[
                'class'=>'application.modules.customfield.components.CustomFieldBehavior',
            ],
        ];
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['remains_warning, enabled', 'numerical', 'integerOnly'=>true],
            ['parent_id', 'length', 'max' => 10],
            ['name, alias', 'length', 'max' => 255],
            ['sort', 'numerical', 'integerOnly' => true],
            ['content, short_content', 'safe'],
            ['id, parent_id, name, alias, sort', 'safe', 'on' => 'search'],
        ];
    }

    public function relations()
    {
        return [
            'parent' => [
                self::BELONGS_TO, 'ProductCategory', 'parent_id', 'alias' => 'parent'
            ],
            'children' => [self::HAS_MANY, 'ProductCategory', 'parent_id', 'alias' => 'children', 'together' => true, 'order' => 'children.sort ASC'],
            'childrenCount' => [self::STAT, 'ProductCategory', 'parent_id'],
            'products' => [self::HAS_MANY, 'Product', 'category_id', 'together' => true],
            'filters' => [self::MANY_MANY, 'ProductFilter', '{{product_category_to_filter}}(category_id, filter_id)'],
        ];
    }

    public function getFiltersDataProvider(){
        $dataProvider =  new CArrayDataProvider('ProductFilter');
        $dataProvider->setData($this->filters);
        return $dataProvider;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => t('Parent'),
            'name' => t('Name'),
            'alias' => t('Link'),
            'parent' => t('Parent'),
            'parentName' => t('Parent'),
            'enabled' => t('Enabled'),
            'status' => t('Status'),
            'sort' => t('Sort'),
            'filters' => t('Filters'),
            'remains_warning' => t('Remains warning'),
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {

        $criteria = new CDbCriteria;

        $criteria->order = 'sort';

        $criteria->compare('id', $this->id, true);
        $criteria->compare('parent_id', $this->parent_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('alias', $this->alias, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'pagination'=>[
               'pageSize'=> 1000
            ],

        ]);
    }

    public function getParentName()
    {
        return $this->parent ? $this->parent->name : null;
    }


    /*------------------------------------*\
      characteristics
    \*------------------------------------*/

    public function getActiveFilters()
    {
        $out = [];
        foreach ($this->filters as $filter) {
            $criteria = new CDbCriteria;
            $criteria->with = ['filters'];
            $criteria->together = true;
            $criteria->compare('category_id', $this->id);
            $criteria->addColumnCondition([
                'filters.filter_id' => $filter->id,
            ]);
            $criteria->addCondition('filters.value>0');
            if (count(Product::model()->findAll($criteria)))
                $out[] = $filter;
        }

        return $out;

    }


    /**
     * @param ProductCategory $category
     * @param array $filters
     */
    public function saveFilters($category, $filters)
    {
        $command = db()->createCommand();

        $command->reset()->delete('{{product_category_to_filter}}', 'category_id = :category_id', [
            ':category_id' => $category->id,
        ]);

        foreach ($filters as $filter_id)
            $command->reset()->insert('{{product_category_to_filter}}', [
                'category_id' => $category->id,
                'filter_id' => $filter_id,
            ]);

        foreach ($category->children as $child) {
            $this->saveFilters($child, $filters);
        }

    }

    protected function beforeDelete()
    {
        $command = db()->createCommand();
        foreach ($this->children as $child)
            $child->delete();
        foreach ($this->products as $product)
            $product->delete();

        $command->reset()->delete('{{product_category_to_filter}}', 'category_id = :category_id', [
            ':category_id' => $this->id,
        ]);

        return parent::beforeDelete();
    }


    private $recursiveCache;
    private $cachedRawData;

    private function getRawData(){
        if(isset($this->cachedRawData))
            return $this->cachedRawData;

        $this->cachedRawData =db()->createCommand()
            ->select('pc.id, CONCAT("c", pc.id) as url_id, pc.alias, pc.name, pc.sort, pc.parent_id, u.filename, p.product_count')
            ->from('{{product_category}} pc')
            ->leftJoin('{{upload}} u','u.id=pc.image_id')
            ->leftJoin('(SELECT COUNT(`id`) as `product_count`, `category_id` FROM {{product}}
	GROUP BY `category_id`
) `p`',' p.category_id=pc.id')
            ->order('pc.parent_id')
            ->queryAll();
        return $this->cachedRawData;
    }


    private function getProductsRawData(){
        return db()->createCommand()
            ->select('p.id, CONCAT("p",p.id) as url_id, p.alias, p.name, p.sort, p.category_id as parent_id, u.filename, (0) as product_count')
            ->from('{{product}} p')
            ->leftJoin('{{upload}} u','u.id=p.image_id')
            ->order('p.category_id')
            ->queryAll();
    }

    public function getCategoryRawData($categoryId){
        foreach($this->getRawData() as $category){
            if($category['id']==$categoryId){
                return $category;
            }
        }
    }

    public function getDataForRecursiveRender($withProducts = false){
        if(isset($this->recursiveCache))
            return $this->recursiveCache;
        function category_sorter(&$categories){
            usort($categories, function($a, $b) {
                return $a['sort'] - $b['sort'];
            });
        }

        function category_searcher(&$categories, $rawData){
            foreach($categories as &$category){
                $category['children'] = array_filter($rawData, function($item) use ($category){
                    return $item['parent_id']==$category['id'];
                });
                if(count($category['children'])>0){
                    category_sorter($category['children']);
                    category_searcher($category['children'], $rawData);
                }
            }
        }

        if ($withProducts) {
            $rawData = array_merge($this->getRawData(), $this->getProductsRawData());
        } else {
            $rawData = $this->getRawData();
        }
        $categories = array_filter($rawData, function($item){
            return $item['parent_id']=='0';
        });

        category_sorter($categories);
        category_searcher($categories, $rawData);
        $this->recursiveCache = $categories;
        return $categories;
    }

    protected function afterValidate()
    {
        if($this->isNewRecord){
            $sort = db()->createCommand()->select('MAX(sort)')->from($this->tableName())->where('parent_id=:pid',[
                ':pid'=>$this->parent_id ?: 0,
            ])->queryScalar();
            $this->sort = intval($sort) + 1;
        }
        parent::afterValidate();
    }

    public function complementProductsFor($ids){
        if(!is_array($ids))
            $ids = [$ids];

        $criteria = new CDbCriteria();
        $criteria->addNotInCondition('id',$ids);
        $criteria->addColumnCondition(['category_id'=>$this->id]);

        return Product::model()->findAll($criteria);
    }

}
