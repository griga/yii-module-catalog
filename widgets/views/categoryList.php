<?php
/** Created by griga at 20.05.2014 | 12:27.
 * @var boolean $sortableParent
 * @var ProductCategory[] $categories
 */ ?>

<ol <?= $sortableParent ? 'id="categories-list"' : '' ?>>
    <?php foreach($categories as $category):?>
        <li class="category-wrapper" id="category-<?= $category['id'] ?>">
            <div class="row category-item">
                <div class="col-sm-9">
                    <?php if($category['filename']):?>
                        <?= CHtml::image(Upload::model()->getThumb($category['filename'], 30, 30)); ?>
                    <?php endif;?>
                    <?= l($category['name'], array('/catalog/category/update', 'id' => $category['id']), array('class'=>'category-name'))?>
                    <?php if($category['product_count']>0):?>
                        (<?= l($category['product_count'],  array('/catalog/product/index', 'Product[category_id]' => $category['id'])) ?>)
                    <?php endif;?>
                    <a href="/admin/catalog/category/update/<?= $category['id'] ?>">
                        <i class="glyphicon glyphicon-pencil"></i>
                    </a>

                    <a href="<?= app()->createUrl('catalog/category/create',['parent_id'=>$category['id']]) ?>">
                        <i class="glyphicon glyphicon-plus-sign"></i>
                    </a>
                </div>
            </div>
            <?php if(count($category['children'])>0):?>
                <?php $this->widget('application.modules.catalog.widgets.CategoryListWidget', array(
                    'categories'=>$category['children'],
                ))?>
            <?php endif;?>
        </li>
    <?php endforeach;?>
</ol>