<?php
/** Created by griga at 28.11.13 | 23:00.
 * @var CategorySitemapWidget $this
 */

?>
<ul class="sitemap-list">
    <?php foreach($this->categories as $cat ):?>
        <?php $children = $cat->siteVersionChildren?>
        <li>
            <?= (count($children) ? '<b>'.$cat->name.'</b>' : CHtml::link($cat->name, $cat->getUrl()) )?>
            <?php if(count($children)) $this->widget('CategorySitemapWidget', array(
                'categories'=>$children,
            ));?></li>
    <?php endforeach;?>
</ul>


