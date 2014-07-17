<?php

class CategoryController extends CrudController
{

    public $model = 'ProductCategory';

    public function actionIndex()
    {
        $this->render('index');
    }



//    public function actionToggle($id, $attribute)
//    {
//        /** @var ProductCategory $model */
//        $model = ProductCategory::model()->findByPk($id);
//
//        if ($model) {
//            if ($model->enabled == 1)
//                $this->recursivePublishedUpdate($id, false);
//            else
//                $this->recursivePublishedUpdate($id, true);
//        }
//    }
//
//    public function recursivePublishedUpdate($id, $enabled = true)
//    {
//        if ($enabled) {
//            db()->createCommand()->update('{{product_categories}}', array(
//                'enabled' => 1,
//            ), 'id = :id', array(':id' => $id));
//        } else {
//            db()->createCommand()->update('{{product_categories}}', array(
//                'enabled' => 0,
//            ), 'id = :id', array(':id' => $id));
//        }
//        foreach (db()->createCommand()
//                     ->select('id, parent_id')
//                     ->from('{{product_categories}}')
//                     ->where('parent_id = :pid', array(
//                         ':pid' => $id
//                     ))->queryAll() as $category) {
//            $this->recursivePublishedUpdate($category['id'], $enabled);
//        }
//    }

    /**
     *
     */
    public function actionSort()
    {
        $updateData = array();
        if(isset($_POST['data'])){
            $retrieve_data = function($items, $parentId) use (&$updateData, &$retrieve_data){
                foreach($items as $index=>$item){
                    $updateData[] = "({$item['id']},$parentId, $index)";
                    if (isset($item['children']) && is_array($item['children'])) {
                        $retrieve_data($item['children'], $item['id']);
                    }
                }
            };
            $retrieve_data($_POST['data'], 0);
        }



        $sql = 'INSERT INTO {{product_category}} (id, parent_id, sort) VALUES '.implode(',',$updateData).' ON DUPLICATE KEY UPDATE parent_id=VALUES(parent_id),sort=VALUES(sort);';
        db()->createCommand($sql)->execute();
        app()->end();
    }
}
