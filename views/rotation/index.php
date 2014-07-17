<?php
/** @var ProductCategory $model
 * @var $this BackendController
 */

$this->breadcrumbs = [
    t('Catalog') => '/admin/catalog',
    t('List of rotations')
];


$countAll = $model->search()->itemCount;

?>
<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <h3><?= t('{n} rotation|{n} rotations|{n} rotations', $countAll); ?></h3>
        <a class="btn btn-success btn-xs" href="/admin/catalog/rotation/create"><span
                class="glyphicon glyphicon-plus"></span> <?= t('add') ?></a>
        <?php

        $this->widget('\yg\tb\GridView', [
            'id' => 'filter-grid',
            'dataProvider' => $model->search(),
            'filter' => $model,
            'columns' => [
                [
                    'name' => 'product_id',
                    'type' => 'raw',
                    'value' => function ($data) {
                        return l($data->product->name, ["/catalog/rotation/update", "id" => $data->id]);
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