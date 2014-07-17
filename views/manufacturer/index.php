<?php
/** Created by griga at 25.11.13 | 14:01.
 * @var $model Manufacturer
 */

$this->breadcrumbs = [
    t('Catalog') => '/admin/catalog',
    t('List of manufacturers')
];

$countAll = $model->search()->itemCount;

?>
<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <h3><?= t('{n} manufacturer|{n} manufacturers|{n} manufacturers', $countAll); ?></h3>
        <a class="btn btn-success btn-xs" href="/admin/catalog/manufacturer/create"><span
                class="glyphicon glyphicon-plus"></span> <?= t('add') ?></a>
        <?php
        $this->widget('\yg\tb\GridView', [
            'id' => 'manufacturer-grid',
            'dataProvider' => $model->search(),
            'filter' => $model,
            'columns' => [
                [
                    'name' => 'name',
                    'type' => 'raw',
                    'value' => function ($data) {
                        return l($data->name, ["/catalog/manufacturer/update", "id" => $data->id]) . ' (' . l( $data->productsCount, ["/catalog/product/index", "Product[manufacturer_id]" => $data->id]) . ')';
                    },
                    'htmlOptions' => ['encodeLabel' => false],
                ],
                [
                    'class' => '\yg\tb\ButtonColumn',
                ],
            ]
        ]); ?>

    </div>
</div>