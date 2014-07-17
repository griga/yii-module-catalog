<?php
/** Created by griga at 23.12.13 | 16:30.
 * @property ProductCategory[] $categories
 */

class CategorySitemapWidget extends CWidget{

    public $categories;

    public function run(){
        $this->render('sitemap');
    }
} 