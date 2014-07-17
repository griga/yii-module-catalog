<?php
/**
 * @var $this BackendController
 * @var $form \yg\tb\ActiveForm
 * @var Rotation $model
 */

$this->breadcrumbs = [
    t('Catalog') => '/admin/catalog/',
    t('Rotations') => '/admin/catalog/rotation',
    t($model->isNewRecord ? 'New record' : 'Update record'),
];

$this->widget('ext.yg.WellBlocksCollapsible');

$form = $this->beginWidget('\yg\tb\ActiveForm', [
    'id' => 'rotation-form',
    'labelColWidth' => 3,
])

?>
<h3><?= t($model->isNewRecord ? 'New record' : 'Update record') ?></h3>
<hr/>
<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <div class="well"><h4><?= t('Rotation properties') ?></h4>
            <?php $this->widget('ext.chosen.TbChosen', [
                'model' => $model,
                'attribute' => 'product_id',
                'htmlOptions' => [
                    'empty' => '',
                ],
                'labelColWidth' => 3,
                'data' => Product::model()->listData(),
            ]); ?>

            <?php $this->widget('ext.chosen.TbChosen', [
                'model' => $model,
                'attribute' => 'filter_value_id',
                'htmlOptions' => [
                    'empty' => '',
                ],
                'labelColWidth' => 3,
                'data' => $model->product_id ?  CHtml::listData(ProductToFilterValue::valuesByProduct($model->product_id), 'id', 'name') : ProductFilterValue::model()->listData(),
            ]); ?>

        </div>
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

        <?= $form->actionButtons($model, 'right'); ?>

    </div>
</div>


<?php $this->endWidget() ?>

<script type="text/javascript">
    $(function(){
        $('#Rotation_product_id').on('change',function(e){
            $.getJSON('/admin/catalog/rotation/filter-values/'+$(this).val()).then(function(data){
                var $rfvi = $('#Rotation_filter_value_id');
                $('option', $rfvi).remove();
                $rfvi.append($('<option value></option>'))
                $.each(data, function(index, option){
                    $rfvi.append($('<option value='+option.id+'>'+ option.name +'</option>'))
                });
                $rfvi.trigger("chosen:updated");
            })
        })

    });


</script>


