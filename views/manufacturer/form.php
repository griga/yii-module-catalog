<?php
/**
 * @var $this BackendController
 * @var $form \yg\tb\ActiveForm
 * @var ProductCategory $model
 */

$this->breadcrumbs = array(
    t('Catalog') => '/admin/catalog/',
    t('Manufacturers') => '/admin/catalog/manufacturer',
    t($model->isNewRecord ? 'New record' : 'Update record'),
);


$form = $this->beginWidget('\yg\tb\ActiveForm', array(
    'id' => 'manufacturer-form',
    'labelColWidth'=>3,
))

?>

<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <h3><?= t($model->isNewRecord ? 'New record' : 'Update record') ?></h3>
        <hr/>
        <div class="well"><h4><?= t('Manufacturer properties') ?></h4>
            <?= $form->textAreaControl($model, 'name', array(
                'multilingual'=>true
            )) ?>
            <?= $form->textAreaControl($model, 'short_name', array(
                'multilingual'=>true
            )) ?>
         </div>

        <div class="well">
            <h4><?= t('Pictures') ?></h4>
            <div class="form-group">
                <div class="col-sm-12">
                    <?php $this->widget('upload.widgets.UploadImagesWidget', array(
                        'model' => $model
                    ));?>
                </div>
            </div>
        </div>
        <?= $form->actionButtons($model); ?>
    </div>
</div>

<?php $this->endWidget() ?>


