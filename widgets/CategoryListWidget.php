<?php


class CategoryListWidget extends CWidget{
    public $categories;
    public $sortableParent = false;

    public function run()
    {
        $this->render('categoryList', array(
            'categories'=>$this->categories,
            'sortableParent'=>$this->sortableParent,
        ));
    }


}