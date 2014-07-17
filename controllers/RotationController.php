<?php
/** Created by griga at 02.07.2014 | 1:18.
 * 
 */

class RotationController extends CrudController {

    public $model = 'Rotation';

    /**
     *
     */
    public function actionFilterValues($id)
    {
        $this->renderJson(ProductToFilterValue::valuesByProduct($id));
    }

} 