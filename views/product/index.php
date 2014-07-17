<?php
/** @var Product $model */
/** @var ProductController $this */

$this->breadcrumbs = [
    t('Catalog')=>'/admin/catalog',
    t('List of products')
];
$countAll = $model->adminSearch()->totalItemCount;

$this->widget('ext.yg.GridFilterClearButtons', [
    'gridId'=>'product-grid',
])

?>

<div class="row">
    <div class="col-sm-12">
        <h3><?php echo t('{n} product|{n} products|{n} products', $countAll); ?></h3>
        <a class="btn btn-success btn-xs" href="/admin/catalog/product/create"><span class="glyphicon glyphicon-plus"></span> <?= t('add') ?></a>
        <?php $this->widget('\yg\tb\GridView', [
                'id' => 'product-grid',
                'dataProvider' => $model->adminSearch(),
                'ajaxUpdate' => false,
                'filter' => $model,
                'columns' => [
                    [
                        'name' => 'article',
                        'headerHtmlOptions'=>[
                            'class'=>'col-sm-1',
                        ],
                    ],
                    [
                        'name' => 'name',
                        'type' => 'raw',
                        'value' => function ($product) {
                            return l($product->name, ["/catalog/product/update", "id" => $product->id]);
                        },
                        'htmlOptions' => ['encodeLabel' => false],
                        'headerHtmlOptions'=>[
                            'class'=>'col-sm-4',
                        ],
                   ],
                    [
                        'name' => 'category_id',
                        'type' => 'raw',
                        'value' => function ($product) {
                            return l($product->category->name, ["/catalog/product/index", "Product[category_id]" => $product->category_id]);
                        },
                        'filter' => CHtml::listData(ProductCategory::model()->findAll(),'id','name'),
                        'htmlOptions' => ['encodeLabel' => false],
                        'headerHtmlOptions'=>[
                            'class'=>'col-sm-3',
                        ],
                    ],
                    [
                        'name' => 'remains',
                        'filter' => false,
                        'headerHtmlOptions'=>[
                            'class'=>'col-sm-1',
                        ],
                    ],
//                    [
//                        'name' => 'blockedRemains',
//                        'filter' => false,
//                        'header' => 'Зарезерви-рованый товар',
//                        'headerHtmlOptions' => [
//                            'class' => 'small-column-header'
//                        ),
//                    ),
//                    [
//                        'name' => 'nonBlockedRemains',
//                        'filter' => false,
//                        'header' => 'Незарезерви-рованый товар',
//                        'headerHtmlOptions' => [
//                            'class' => 'small-column-header'
//                        ),
//                    ),
                    [
                        'name' => 'manufacturer_id',
                        'filter' => CHtml::listData(Manufacturer::model()->findAll(),'id','name'),
                        'value'=> function($product){
                            return $product->manufacturer ? $product->manufacturer->name : '';
                        },
                        'headerHtmlOptions'=>[
                            'class'=>'col-sm-2',
                        ],
                    ],
                    [
                        'name'=>'featured',
                        'class'=>'\yg\tb\CheckboxColumn',
                        'action'=>'/admin/catalog/product/featured-toggle',
                    ],
                    [
                        'name'=>'sort',
                        'class'=>'\yg\tb\SortColumn',
                        'action'=>'/admin/catalog/product/sort',
                    ],
                    [
                        'name' => 'status',
                        'value' => '$data->getStatusBadges()',
                        'type' => 'raw',
                        'filter' => false,
                    ],
                    [
                        'class' => '\yg\tb\ButtonColumn',
                    ],
                ]
            ]); ?>

        <h4><?= t('Filters') ?></h4>
        <form class="custom-filters row" id="custom-filters">
            <div class="col-sm-10">
                <label class="checkbox" for="action_filter"><input name="Filter[action]" id="action_filter"
                                                                                  type="checkbox"/><span
                        class="label label-warning">акция</span></label>
<!--                <label class="checkbox" for="not_processed_filter"><input name="Filter[not_processed]"
                                                                                         id="not_processed_filter"
                                                                                         type="checkbox"/><span
                        class="label label-info">импортирован</span></label>-->
                <label class="checkbox" for="zero_remains_filter"><input name="Filter[zero_remains]"
                                                                                        id="zero_remains_filter"
                                                                                        type="checkbox"/><span
                        class="label label-default">нет в наличии</span></label>
                <label class="checkbox" for="warning_remains_filter"><input name="Filter[warning_remains]"
                                                                                           id="warning_remains_filter"
                                                                                           type="checkbox"/><span
                        class="label label-danger">заканчивается</span></label><?= l(t('Clear filters'), '/'.r()->pathInfo) ?></div>
        </form>
        <?=
        CHtml::activeDropDownList($model, 'pageSize', [
            '10' => '10',
            '15' => '15',
            '20' => '20',
            '30' => '30',
            '50' => '50',
            '100' => '100',
        ], [
            'id' => 'pageSizeSelect'
       ]);?>


        <script type="text/javascript">
            $(function () {
                var $sf = $('#custom-filters');

                $sf.on('change', 'input', function (event) {
                    SearchFunc(event.target);
                    event.preventDefault();
                });
                $('#pageSizeSelect').width(60).appendTo($('#product-grid thead tr:last td:last').empty())

                $sf.find('input').each(function () {
                    if (document.URL.indexOf(encodeURIComponent($(this).prop('name'))) !== -1) {
                        $(this).attr('checked', 'checked');
                    }
                });

                function SearchFunc(element) {
                    var url = document.URL;
                    var name = encodeURIComponent($(element).prop('name'));
                    var regExp = new RegExp('&?' + name + '=1');

                    url = url.replace(regExp, '');
                    if ($(element).prop('checked'))
                        url = url + (url.indexOf('?') !== -1 ? '&' : '?') + (name + '=1');
                    if ($(element).is('select'))
                        url = url + (url.indexOf('?') !== -1 ? '&' : '?') + (name + '=' + $(element).val());
                    window.location.href = url;
                }
            })
        </script>
    </div>
</div>