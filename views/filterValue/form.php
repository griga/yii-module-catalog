<?php /** @var $form \yg\tb\ActiveForm */
/** @var ProductFilter $model */

$this->breadcrumbs = [
    t('Catalog') => '/admin/catalog/',
    t('Filter values') => '/admin/catalog/filter-value',
    t($model->isNewRecord ? 'New record' : 'Update record'),
];

$form = $this->beginWidget('\yg\tb\ActiveForm', [
    'id' => 'product-filter-value-form',
    'labelColWidth'=> 3
]); ?>
<h3><?= t($model->isNewRecord ? 'New record' : 'Update record') ?></h3>
<hr/>
<div class="well">
    <h4><?= t('Filter value propertiesm') ?></h4>
    <?= $form->hiddenField($model, 'id') ?>
    <?=
    $form->textControl($model, 'name', [
        'encode' => false,
        'multilingual' => true,
    ]) ?>
    <?= $form->textControl($model, 'key') ?>

    <?php $this->widget('ext.chosen.TbChosen', [
        'model' => $model,
        'attribute' => 'filter_id',
        'data' => ProductFilter::model()->listData(),
        'htmlOptions' => [
            'id' => 'chosen-' . time(),
        ],
        'labelColWidth'=> 3
    ]);?>
</div>
<?= $form->actionButtons($model); ?>
<?php $this->endWidget(); ?>
