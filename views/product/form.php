<?php
$this->widget('ext.yg.WellBlocksCollapsible');
$this->widget('ext.yg.NumberFieldWithHandlers');
/**
 * @var $this BackendController
 * @var $form \yg\tb\ActiveForm
 * @var Product $model
 */

$this->breadcrumbs = [
    t('Catalog') => '/admin/catalog/',
    t('Products') => '/admin/catalog/product',
    t($model->isNewRecord ? 'New record' : 'Update record'),
];


$form = $this->beginWidget('\yg\tb\ActiveForm', [
    'id' => 'product-form',
    'labelColWidth' => 4,
]);

?>

<h3><?= t($model->isNewRecord ? 'New record' : 'Update record') ?></h3>
<hr/>
<div class="row">
    <div class="col-sm-7">
        <div class="well">
            <h4><?= t('Product properties') ?></h4>
            <?=
            $form->textAreaControl($model, 'name', [
                'multilingual' => true
            ]); ?>
            <?= $form->textControl($model, 'article'); ?>
            <?php $this->widget('ext.chosen.TbChosen', [
                'model' => $model,
                'attribute' => 'category_id',
                'data' => CHtml::listData(ProductCategory::model()->findAll(), 'id', 'name'),
                'htmlOptions' => ['empty' => ''],
                'labelColWidth' => 4,
            ]); ?>
            <?php $this->widget('ext.chosen.TbChosen', [
                'model' => $model,
                'attribute' => 'manufacturer_id',
                'data' => CHtml::listData(Manufacturer::model()->findAll(), 'id', 'name'),
                'htmlOptions' => ['empty' => ''],
                'labelColWidth' => 4,
            ]); ?>
            <?= $form->textControl($model, 'price'); ?>
            <?= $form->textControl($model, 'remains', ['disabled' => true]); ?>
            <?= $form->textControl($model, 'remains_warning', ['class' => 'form-control ygnf-number-field']); ?>
        </div>
        <?php $this->renderPartial('_relatedProducts',['model'=>$model])?>
    </div>
    <div class="col-sm-5">
        <?php $this->renderPartial('_customFilters',['model'=>$model])?>
        <div class="well">
            <h4><?= t('Action') ?></h4>
            <?php $model->action_start = $model->action_start ? date('m/d/Y', strtotime($model->action_start)) : ''; ?>
            <?php $model->action_end = $model->action_end ? date('m/d/Y', strtotime($model->action_end)) : ''; ?>
            <?= $form->textControl($model, 'action_price'); ?>
            <?=
            $form->createRow($model, 'action_start', $this->widget('zii.widgets.jui.CJuiDatePicker', [
                'model' => $model,
                'attribute' => 'action_start',
                'options' => [],
                'htmlOptions' => [
                    'class' => 'form-control',
                    'style' => 'position:relative; z-index:10000;'
                ],
            ], true)) ?>
            <?=
            $form->createRow($model, 'action_end', $this->widget('zii.widgets.jui.CJuiDatePicker', [
                'model' => $model,
                'attribute' => 'action_end',
                'options' => [],
                'htmlOptions' => [
                    'class' => 'form-control',
                    'style' => 'position:relative; z-index:10000;'
                ],
            ], true)) ?>

            <?= $form->checkBoxControl($model, 'action_enabled');; ?>
            <?= $form->checkBoxControl($model, 'featured');; ?>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-sm-12">

        <div class="well">
            <h4><?= t('Pictures') ?></h4>

            <div class="form-group">
                <div class="col-sm-12">
                    <?php $this->widget('upload.widgets.UploadImagesWidget', [
                        'model' => $model
                    ]);?>
                </div>
            </div>
        </div>


        <div class="well">
            <h4><?= t('Content') ?></h4>
            <?php $this->widget('\yg\tb\RedactorWidget',[
                'model'=>$model,
                'attribute'=>'content',
                'options'=>[
                    'css' => Config::get('mainCssFile'),
                ],
            ]);?>
            <?= $form->error($model, 'content') ?>
        </div>
    </div>
</div>

<?= $form->actionButtons($model) ?>

<?php $this->endWidget(); ?>

