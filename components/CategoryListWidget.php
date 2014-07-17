<?php
/** Created by griga at 28.11.13 | 22:57.
 * 
 */

class CategoryListWidget extends CWidget{
    public $categories = array();

    public function run(){
        $this->render('list');
    }
} 