<?php
/** @var ProductCategory $model
 * @var $this BackendController
 */

$this->breadcrumbs = array(
    t('Catalog')=>'/admin/catalog',
    t('List of categories')
);



//$countAll = $model->search()->totalItemCount;


$this->widget('ext.nested-sortable.NestedSortableWidget');


/** @var ProductCategory[] $categories */


$data = ProductCategory::model()->getDataForRecursiveRender();
?>

<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <h3><?php echo t('{n} category|{n} categories|{n} categories', ProductCategory::model()->count()); ?></h3>
        <a class="btn btn-success btn-xs" href="/admin/catalog/category/create"><span class="glyphicon glyphicon-plus"></span> <?= t('add') ?></a>

        <hr/>

        <?php $this->widget('webroot.themes.commerce.back.widgets.SortableModelsWidget', array(
            'models'=>$data,
            'sortableParent'=>true,
            'controllerUrl'=>'/catalog/category/',
        ));?>


    </div>
</div>