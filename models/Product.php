<?php

/**
 * This is the model class for table "{{product}}".
 *
 * The followings are the available columns in table '{{product}}':
 * @property int $id
 * @property string $name
 * @property string $uid
 * @property string $alias
 * @property string $article
 * @property string $enabled
 * @property string $unit
 * @property string $content
 * @property string $short_content
 * @property int $category_id
 * @property int $manufacturer_id
 * @property int $remains
 * @property int $remains_warning
 * @property double $price
 *
 * @property integer $out_of_stock_counter   click counter for products that are out of stock
 *
 * @property int $status
 *
 * @property bool $action_enabled
 * @property string $action_start
 * @property string $action_end
 * @property double $action_price
 *
 * @property string $create_time
 * @property string $update_time
 *
 * @property ProductCategory $category
 * @property ProductToFilterValue[] $filterValues
 * @property ProductRelated[] $relatedProducts
 *
 * @property $blockedRemains
 * @property $nonBlockedRemains
 *
 */
class Product extends CrudActiveRecord implements IECartPosition
{

    /**
     * @param string $className
     * @return Product | SiteSplitBehavior
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{product}}';
    }

    public function rules()
    {
        return [
            ['name, category_id', 'required'],
            ['price, action_price, priceMin, priceMax', 'numerical'],
            ['status, action_enabled, out_of_stock_counter, remains_warning, category_id, manufacturer_id, featured', 'numerical', 'integerOnly' => true],
            ['name, article, uid', 'length', 'max' => 127],
            ['unit, remains', 'length', 'max' => 10],
            ['action_start, action_end', 'date', 'format' => 'MM/dd/yyyy', 'except' => 'import'],
            ['content, short_content', 'safe'],
            ['action_end', 'comparisonTime'],
            ['showNewArrivals, showWithAction, id, pageSize, priceMin, priceMax, name, article, unit, category_id, comment, manufacturer_id, remains, price, uid, status', 'safe', 'on' => 'search'],
            ['name, article, content, short_content', 'safe', 'on' => 'frontSearch'],
        ];
    }

    public function comparisonTime($attribute, $params)
    {
        if (($this->action_end && !$this->action_start) ||
            (!$this->action_end && $this->action_start) ||
            (($this->action_start && $this->action_end) && (strtotime($this->action_start) >= strtotime($this->action_end)))
        ) {
            $this->addError('action_end', 'Дата конеца акции должна привышать дату начала акции');
        }
    }

    public function relations()
    {
        return [
            'category' => [self::BELONGS_TO, 'ProductCategory', 'category_id'],
            'manufacturer' => [self::BELONGS_TO, 'Manufacturer', 'manufacturer_id'],
            'filterValues' => [self::MANY_MANY, 'ProductFilterValue', '{{product_to_filter_value}}(product_id, value_id)'],
            'relatedProducts'=>[self::MANY_MANY, 'Product', '{{product_related}}(product_id, entity_id)', 'condition'=>'entity="Product" '],
       ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => t('Name'),
            'article' => t('Article'),
            'unit' => t('Unit'),
            'category_id' => t('Category'),
            'categoryName' => t('Category'),
            'manufacturer_id' => t('Manufacturer'),
            'remains' => t('Remains'),
            'remains_warning' => t('Remains warning'),
            'blockedRemains' => 'Зарезервированый товар',
            'nonBlockedRemains' => 'Незарезервированый товар',
            'price' => t('Price'),
            'uid' => '1C номер',
            'status' => t('Status'),
            'content' => t('Content'),
            'short_content' => t('Short content'),
            'action_price' => t('Action price'),
            'action_enabled' => t('Action enabled'),
            'action_start' => t('Action start'),
            'action_end' => t('Action end'),
            'actionDateRange' => t('Action date range'),
            'featured' => t('Featured'),

        ];
    }

    public function getCategoryName()
    {
        return $this->category ? $this->category->name : null;
    }

    protected function beforeDelete()
    {
        db()->createCommand()->delete('{{product_to_filter_value}}','product_id = :id', [
            ':id'=>$this->id,
        ]);
        db()->createCommand()->delete('{{product_related}}','entity="Product" AND entity_id = :id', [
            ':id'=>$this->id,
        ]);

        foreach($this->getUploads() as $upload ){
            $upload->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * @param $uid
     * @return Product
     */
    public static function getByUid($uid)
    {
        return self::model()->findByAttributes(['uid' => $uid]);
    }

    public $priceMin;
    public $priceMax;
    public $pageSize;
    public $showNewArrivals;
    public $showWithAction;

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @param array $options indicates that method is called from the home page
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($options = [])
    {
        $criteria = new CDbCriteria;
        /** @var ProductCategory $category */

        $criteria->addCondition('t.price>0');

        $charColumns = [];
//        foreach (ProductFilter::model()->findAll() as $char) {
//            $charColumns[] = "MAX(IF(attributes.char_id = " . $char->id . ", attributes.value, null)) as filter_" . $char->id;
//        }
//        $criteria->with = ['characteristics' => [
//            'together' => true,
//            'alias' => 'attributes',
//            'select' => $charColumns,
//        ]);

        $criteria->with = ['defaultUpload'];
        $criteria->group = 't.id';

        $criteria->compare('article', $this->article, true);
        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('manufacturer_id', $this->manufacturer_id);

        $criteria->addBetweenCondition('price', $this->priceMin ? : 0, $this->priceMax ? : 1000000);

        if (isset($_GET['flt'])) {
            foreach ($_GET['flt'] as $charId => $value) {
                /** @var Characteristic $model */
                $id = ltrim(h($charId), 'f');
                $model = Characteristic::model()->findByPk($id);
                if ($model) {
                    if ($model->type == Characteristic::TYPE_SELECT && $value) {
                        $criteria->having .= ($criteria->having ? ' AND ' : '') . 'filter_' . $id . '=' . h($value);
                    } else if ($model->type == Characteristic::TYPE_RANGE) {
                        $criteria->having .= ($criteria->having ? ' AND ' : '') . 'filter_' . $id . ' BETWEEN ' . (isset($value['min']) ? intval(h($value['min'])) : 0) . ' AND ' . (isset($value['max']) ? intval(h($value['max'])) : 1000000);
                    }
                }
            }
        }

//        if ($this->showNewArrivals && $this->showWithAction) {
//            $criteria->addCondition($this->getNewArrivalsOrActionsCondition());
//        } elseif($this->showWithAction){
//            $criteria->addCondition($this->getWithActionCondition());
//        } elseif($this->showNewArrivals){
//            $criteria->addCondition($this->getNewArrivalsCondition());
//        } else {
//            if (isset($options['showNewArrivals']) && $options['showNewArrivals'] && isset($options['showWithAction']) && $options['showWithAction']) {
//                $criteria->addCondition($this->getNewArrivalsOrActionsCondition());
//            } elseif (isset($options['showNewArrivals']) && $options['showNewArrivals']) {
//                $criteria->addCondition($this->getNewArrivalsCondition());
//            } elseif (isset($options['showWithAction']) && $options['showWithAction']) {
//                $criteria->addCondition($this->getWithActionCondition());
//            }
//        }

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'pagination' => [
                'pageSize' => $this->pageSize ? : Config::get((isset($options['homePage']) ? 'newArrivalPageSize' : 'categoryPageSize')),
            ],
            'sort' => [
                'multiSort' => true,
                'defaultOrder' => 'article, price',
            ],
        ]);
    }

    public function adminSearch()
    {
        $criteria = new CDbCriteria;

        $criteria->with = ['category','manufacturer'];

        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('article', $this->article, true);
        $criteria->compare('manufacturer_id', $this->manufacturer_id);
        $criteria->compare('category_id', $this->category_id);

        if (isset($_GET['Filter'])) {

            if (isset($_GET['Filter']['action']))
                $criteria->addCondition($this->getWithActionCondition());
            if (isset($_GET['Filter']['not_processed']))
                $criteria->addCondition('t.status=' . self::STATUS_NOT_PROCESSED);
            if (isset($_GET['Filter']['warning_remains']))
                $criteria->addCondition('t.remains<= t.remains_warning OR t.remains <= category.remains_warning ');
            if (isset($_GET['Filter']['zero_remains']))
                $criteria->addCondition('t.remains=0');
        }

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'pagination' => [
                'pageSize' => $this->pageSize ?: (isset($_GET['Product']) ? 50 : 10),
            ],
            'sort' => [
                'multiSort' => true,
                'defaultOrder' => 't.sort, article, price',

            ]
        ]);
    }

    public function textSearch($query)
    {
        $criteria = new CDbCriteria;

        $criteria->compare('name', $query, true);
        $criteria->compare('article', $query, true, 'OR');
        $criteria->compare('content', $query, true, 'OR');
        $criteria->compare('short_content', $query, true, 'OR');


        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'pagination' => [
                'pageSize' => Config::get('categoryPageSize'),
            ],
        ]);
    }

    public function actionsProvider()
    {
        $criteria = Product::model()->withAction()->getDbCriteria();
        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
    }

    /**
     * return behaviors of component merged with parent component behaviors
     * @return array CBehavior[]
     */

    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            [
                'CTimestampBehavior' => [
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'timestampExpression'=> new CDbExpression( DbHelper::timestampExpression()),
                    'setUpdateOnCreate'=>true,
                ],
                'upload'=>[
                    'class'=>'upload.components.UploadBehavior',
                    'folder'=>'products',
                    'defaultUploadField'=>'image_id',
                ],
                'linkable'=>[
                    'class'=>'LinkableBehavior',
                    'urlPath'=>'/product/',
                    'urlAttribute'=>'alias',
                    'titleAttribute'=>'name',
                ],
                'aliasBehavior'=>[
                    'class'=>'AliasBehavior',
                    'sourceAttribute'=>'name',
                    'aliasAttribute'=>'alias',
                ],
                'ml' => [
                    'class' => 'MultilingualBehavior',
                    'langTableName' => 'product_lang',
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
                'pureContentBehavior'=>[
                    'class'=>'PureContentBehavior',
                    'fields'=>[
                        'name'=>'h1',
                        'image'=>'image',
                        'content'=>'div',
                        'price'=>function($data){
                                return CHtml::tag('div', [],Config::get('site_currency') . $data->price);
                            },
                    ]
                ]

            ]
        );
    }

    /*------------------------------------*\
      Content
    \*------------------------------------*/
    public function getTeaser()
    {
        return YgStringUtils::extractTeaser($this->content);
    }

    public function getSearchTeaser()
    {
        return YgStringUtils::extractSearchTeaser($this->short_content . '<br>' . $this->content, r()->getParam('query', ''), 100);
    }

    /*------------------------------------*\
      Status
    \*------------------------------------*/

    const STATUS_NOT_PROCESSED = 0;
    const STATUS_PROCESSED = 1;

    public static function getStatuses()
    {
        return [
            self::STATUS_NOT_PROCESSED => 'Не обработан',
            self::STATUS_PROCESSED => 'Обработан',
        ];
    }

    public function getStatusName()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status];
    }

    /*------------------------------------*\
      STATUS BADGES
    \*------------------------------------*/

    public function getStatusBadges()
    {
        $badges = '';
        if ($this->hasActionState())
            $badges .= '<span class="label label-warning">&nbsp;</span>';
        if ($this->hasRemainsWarningState())
            $badges .= '<span class="label label-danger">' . $this->remains . '</span>';
        if ($this->remains == 0)
            $badges .= '<span class="label label-default">&nbsp;</span>';
/*        if ($this->status == self::STATUS_NOT_PROCESSED)
            $badges .= '<span class="label label-info">&nbsp;</span>';*/
        return $badges;
    }

    private function hasRemainsWarningState()
    {
        if ($this->remains_warning && intval($this->remains_warning) >= intval($this->remains)) {
            return true;
        } elseif ($this->category && $this->category->remains_warning && intval($this->category->remains_warning) >= intval($this->remains)) {
            return true;
        } else {
            return false;
        }
    }


    /*------------------------------------*\
      related stuff
    \*------------------------------------*/
    protected function afterDelete()
    {
        db()->createCommand()->delete('{{product_related}}', '`product_id`=' . $this->id);
        parent::afterDelete();
    }

    public function getRelatedProducts()
    {
        $criteria = new CDbCriteria();
        $related = self::getRelatedIds($this->id);
        $criteria->addInCondition('id', $related);
        return Product::model()->findAll($criteria);
    }

    public static function getRelatedIds($id)
    {
        return db()->createCommand()->select('related_id')->from('{{product_related}}')->where('`product_id`=' . $id)->queryColumn();
    }


    /*------------------------------------*\
      ACTION STUFF
    \*------------------------------------*/

    public function hasActionState()
    {

        return ((bool)$this->action_enabled && strtotime($this->action_start) < time() && strtotime($this->action_end) > time());


    }

    protected function beforeSave()
    {
        if (!Yii::app() instanceof CConsoleApplication) {
            $dateAttributes = ['action_end','action_start'];
            foreach($dateAttributes as $attr){
                if ($this->{$attr}) {
                    $this->{$attr} = date("Y-m-d H:i:s", strtotime($this->{$attr}));
                } else {
                    $this->{$attr} = null;
                }
            }
        }

        return parent::beforeSave();
    }


    /**
     * @return Product
     */
    public function withAction()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition($this->getWithActionCondition());
        $this->dbCriteria->mergeWith($criteria);
        return $this;
    }

    private function getWithActionCondition(){
            return 'action_enabled = 1 AND DATE(action_start) < '.DbHelper::currentDateFunction().' AND  DATE(action_end) > '.DbHelper::currentDateFunction();

    }

    public function newArrivalsOrActions()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition($this->getNewArrivalsOrActionsCondition());
        $this->dbCriteria->mergeWith($criteria);
        return $this;
    }

    /*------------------------------------*\
      Price
    \*------------------------------------*/

    public function getUserPrice()
    {
        return ($this->hasActionState() && (bool)$this->action_price) ? $this->action_price : $this->price;
    }

    /*------------------------------------*\
      Cart
    \*------------------------------------*/
    function getId()
    {
        return 'Product' . $this->id;
    }

    function getPrice()
    {
        return $this->getUserPrice();
    }

    /*------------------------------------*\
      Blocked items
    \*------------------------------------*/
    /**
     * @var CMap
     */
    private static $blockedCache;

    public function getBlockedRemains()
    {
        if (!isset(self::$blockedCache)) {
            $this->initBlockedCache();
        }
        return self::$blockedCache->itemAt($this->id) ? : 0;
    }

    public function getNonBlockedRemains()
    {
        return $this->remains - $this->blockedRemains;
    }

    public function initBlockedCache()
    {
        $statuses = implode(',', [
            Order::STATUS_1C_NEW,
            Order::STATUS_1C_PAID,
        ]);
        $sql = <<< SQL
SELECT p.id, SUM(oi.amount) AS blocked FROM tbl_products AS p
    LEFT JOIN tbl_order_item AS oi ON oi.product_id = p.id
    LEFT JOIN tbl_order AS o ON oi.order_id = o.id
    WHERE o.status_1c IN($statuses)
    GROUP BY p.id
SQL;
        self::$blockedCache = new CMap();
        foreach (db()->createCommand($sql)->queryAll() as $product) {
            self::$blockedCache->add($product['id'], intval($product['blocked']));
        }

    }


}
