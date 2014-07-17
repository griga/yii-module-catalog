<?php
/** Created by griga at 25.11.13 | 14:01.
 * @var $model ProductFilterValue
 */

$this->breadcrumbs = array(
    t('Catalog')=>'/admin/catalog',
    t('List of filter values')
);

$countAll = $model->search()->itemCount;

?>
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <h3><?php echo t('{n} filter value|{n} filter values|{n} filter values', $countAll); ?></h3>
            <a class="btn btn-success btn-xs" href="/admin/catalog/filterValue/create"><span class="glyphicon glyphicon-plus"></span> <?= t('add') ?></a>
            <?php

            $this->widget('\yg\tb\GridView', array(
                'id' => 'filter-value-grid',
                'dataProvider' => $model->search(),
                'filter' => $model,
                'columns' => array(
                    array(
                        'name'=>'name',
                        'type'=>'raw',
                        'value'=>'l($data->name, array("/catalog/filterValue/update","id"=>$data->id))',
                        'htmlOptions' => array('encodeLabel'=>false),
                    ),
                    'key',

                    array(
                        'class' => '\yg\tb\ButtonColumn',
                    ),
                )
            )); ?>

        </div>
    </div>