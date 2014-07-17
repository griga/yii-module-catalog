<?php /** @var $form \yg\tb\ActiveForm */
/** @var ProductFilter $model */

$this->breadcrumbs = [
    t('Catalog') => '/admin/catalog/',
    t('Filters') => '/admin/catalog/filter',
    t($model->isNewRecord ? 'New record' : 'Update record'),
];

$form = $this->beginWidget('\yg\tb\ActiveForm', [
    'id' => 'product-filter-form',
    'labelColWidth' => 3
]); ?>


<h3><?= t($model->isNewRecord ? 'New record' : 'Update record') ?></h3>
<hr/>

<div class="well">
    <h4><?= t('Filter properties') ?></h4>
    <?= $form->hiddenField($model, 'id') ?>
    <?= $form->textControl($model, 'name', [
        'multilingual'=>true
    ]) ?>
    <?= $form->textControl($model, 'key') ?>
    <?php $this->widget('ext.chosen.TbChosen', [
        'model' => $model,
        'attribute' => 'categories',
        'multiple' => true,
        'data' => ProductCategory::model()->listData(),
        'labelColWidth' => 3,
    ]);?>
</div>

<div class="well">
    <h4><?= t('Filter values') ?>&nbsp;
        <?php $this->widget('\yg\tb\ModalRemoteLink', [
            'href' => app()->createUrl('catalog/filter-value/create', ['ProductFilterValue[filter_id]' => $model->id]),
            'label' => t('add'),
            'htmlOptions' => [
                'id' => 'filter-value-add-launcher',
                'class' => 'btn btn-success btn-xs',
                'disabled' => $model->isNewRecord,
                'data-modal-success-rise'=>'valueAdded',
//                'data-modal-height'=>,
            ]
        ]);?>
    </h4>
    <?php $this->widget('\yg\tb\GridView', [
        'id' => 'filter-value-grid',
        'dataProvider' => $model->getFiltersValuesDataProvider(),
        'enableHistory' => false,
        'template'=>'{items}{pager}',
        'columns' => [
            [
                'name' => 'name',
                'type' => 'raw',
            ],
            [
                'name' => 'key',
            ],
            [
                'class' => '\yg\tb\AjaxButtonColumn',
                'controllerId' => 'filterValue',
                'moduleId' => 'catalog',
                'updateModalSuccessRise'=>'valueAdded',
            ],
        ]
    ]); ?>
</div>

<?= $form->actionButtons($model); ?>

<?php $this->endWidget(); ?>

<script type="text/javascript">
    $(function(){
        $('#product-filter-form').on('valueAdded', function(event, data){
            var $launcher = $(event.target);
            if(data.close===false){
                var modelUrl = '/admin/catalog/filter-value/update/'+data.modelId;
                var $modal = $launcher.data('yg.modal');
                $modal.find('form').attr('action',modelUrl+'?ajax=1');
                jQuery('#product-filter-value-form').data('settings').validationUrl = modelUrl;
            }
            $.fn.yiiGridView.update("filter-value-grid");
        });
    })
</script>

