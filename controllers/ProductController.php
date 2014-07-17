<?php

/** Created by griga at 07.11.13 | 14:49.
 *
 */
class ProductController extends CrudController
{

    public $model = 'Product';

    public function actionRelatedAutoComplete()
    {

        $data = [];
        if (Yii::app()->request->isAjaxRequest && isset($_GET['term'])) {
            $name = $_GET['term'];

            $data = db()->createCommand()->select('id as value, name as label')
                ->from('{{product}}')
                ->where('name LIKE :term', [":term" => "%$name%"])
                ->limit(20)->queryAll();
        }

        $this->renderJson($data);
    }

    /**
     *
     */
    public function actionDownload()
    {
        XlsService::downloadProductsList();
    }

    /**
     *
     */
    public function actionDownloadZeroPrised()
    {
        XlsService::downloadZeroPricedProductsList();
    }

    public function actionCategoryFiltersData($id)
    {
        $category = ProductCategory::model()->findByPk($id);
        $filters = [];
        foreach (db()->createCommand()
                     ->select('pf.id as filter_id, pf.name as filter_name, pfv.id as value_id, pfv.name as value_name')
                     ->from('{{product_category}} c')
                     ->leftJoin('{{product_category_to_filter}} pctf', 'c.id = pctf.category_id')
                     ->leftJoin('{{product_filter}} pf', 'pf.id = pctf.filter_id')
                     ->leftJoin('{{product_filter_value}} pfv', 'pfv.filter_id = pctf.filter_id')
                     ->where('c.id=:cid', [':cid' => $id])->order('pfv.id')->queryAll() as $queryRow) {
            if (!isset($filters[$queryRow['filter_id']])) {
                $filters[$queryRow['filter_id']] = [
                    'id' => $queryRow['filter_id'],
                    'name' => $queryRow['filter_name'],
                    'values' => []
                ];
            }
            $filters[$queryRow['filter_id']]['values'][] = [
                'id' => $queryRow['value_id'],
                'name' => $queryRow['value_name'],
            ];
        }

        $this->renderJson([
            'categoryName' => $category->name,
            'filters' => $filters,
        ]);
    }

    /**
     *
     */
    public function actionSelectedFilterValues($id)
    {
        $this->renderJson(db()->createCommand()->select('value_id')->from('{{product_to_filter_value}}')->where('product_id=:pid',[':pid'=>$id])->queryColumn());
    }

    /**
     *
     */
    public function actionFeaturedToggle()
    {

        db()->createCommand()->update('{{product}}', [
            'featured' => ($_POST['value'] == 'true' ? 1 : 0)
        ], 'id=:id', [':id' => $_POST['id']]);
        app()->end();
    }

    /**
     *
     */
    public function actionSort()
    {
        if(isset($_POST['data'])){
            $updateData = [];
            foreach($_POST['data'] as $index=>$id){
                $updateData[] = "($id, $index)";
            }
            $sql = 'INSERT INTO {{product}} (id, sort) VALUES '.implode(',',$updateData).' ON DUPLICATE KEY UPDATE sort=VALUES(sort);';
            db()->createCommand($sql)->execute();
            app()->end();
        }
    }

}