<?php
/**
 * @var $this BackendController
 * @var $form \yg\tb\ActiveForm
 * @var ProductCategory $model
 */

$this->breadcrumbs = [
    t('Catalog') => '/admin/catalog/',
    t('Categories') => '/admin/catalog/category',
    t($model->isNewRecord ? 'New record' : 'Update record'),
];

$this->widget('ext.yg.WellBlocksCollapsible');
$this->widget('ext.yg.NumberFieldWithHandlers');

$form = $this->beginWidget('\yg\tb\ActiveForm', [
    'id' => 'product-category-form',
    'labelColWidth'=>3,
])

?>
<h3><?= t($model->isNewRecord ? 'New record' : 'Update record') ?></h3>
<hr/>
<div class="row">
    <div class="col-sm-9">
        <div class="well"><h4><?= t('Category properties') ?></h4>
            <?= $form->textAreaControl($model, 'name', [
                'multilingual'=>true
            ]) ?>

            <?php $this->widget('ext.chosen.TbChosen', [
                'model'=>$model,
                'attribute'=> 'parent_id',
                'htmlOptions'=>[
                    'empty'=>'',
                    'preSelectedValues' => ($model->isNewRecord && isset($_GET['parent_id']) ? $_GET['parent_id'] : []),
                ],
                'labelColWidth'=>3,
                'data'=> ProductCategory::model()->getAsList($model->id),

            ]); ?>

            <?= $form->textControl($model, 'remains_warning', ['class' => 'form-control ygnf-number-field']) ?>

            <?php $this->widget('ext.chosen.TbChosen', [
                'model' => $model,
                'attribute' => 'filters',
                'multiple' => true,
                'data' => ProductFilter::model()->listData(),
                'labelColWidth' => 3,
            ]);?>

        </div>
    </div>
    <div class="col-sm-3">
        <div class="well">
            <div class="<?= $model->hasErrors('enabled') ? 'has-error' : '' ?>">
                <div class="checkbox col-md-offset-5">
                    <label>
                        <?= $form->checkBox($model, 'enabled') ?><b> <?= $model->getAttributeLabel('enabled') ?></b>
                    </label>
                    <?= $form->error($model, 'enabled') ?>
                </div>
            </div>
            <br/>
        </div>

        <?php if(app()->user->checkAccess('admin') && !$model->isNewRecord):?>
            <div class="well">
                <h4><?= t('Admin actions') ?></h4>

                <?= CHtml::ajaxLink( t('Delete category'),
                    app()->createUrl('catalog/category/delete',['id'=>$model->id]),
                    [
                        'type' => 'POST',
                        'data' => [],
                        'success'=>'js:function(){window.location.href="'.app()->createUrl('catalog/category/index').'";}'
                    ],[
                        'class'=>'btn btn-danger btn-block',
                        'confirm'=>"Are you sure want to delete this category? This will remove all related products and filtering data. "
                    ]); ?>

            </div>
        <?php endif;?>


    </div>
</div>

<div class="row">
    <div class="col-sm-9">
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
            <?php $this->widget('\yg\tb\RedactorWidget', [
                'model' => $model,
                'attribute' => 'content',
                'options'=>[
                    'minHeight'=>400,
                    'css' => Config::get('mainCssFile'),
                ],
            ]);?>
            <?= $form->error($model, 'content') ?>

        </div>

    </div>

    <div class="col-sm-3">
        <?php $this->widget('application.modules.customfield.widgets.CustomFieldAdminWidget',[
            'model'=>$model
        ]);?>    </div>
</div>

<div class="row">
    <div class="col-sm-9">
        <?= $form->actionButtons($model, 'right'); ?>
    </div>
</div>


<?php $this->endWidget() ?>


