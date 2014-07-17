<?php
/** Created by griga at 21.02.14 | 11:42.
 * 
 */
Yii::import('ext.phpexcel.XPHPExcel');

class XlsService {

    public static function downloadProductsList(){
        $criteria = new CDbCriteria();
        $criteria->with = array('category');
        self::processCriteria($criteria);
    }


    public static function downloadZeroPricedProductsList()
    {
        $criteria = new CDbCriteria();
        $criteria->with = array('category');
        $criteria->addCondition('t.price=0');
        self::processCriteria($criteria);
    }

    public static function processCriteria($criteria){
        $excel = XPHPExcel::createPHPExcel();
        $excel->getProperties()->setCreator("Marlin Aquarium");
        $excel->getActiveSheet()->setTitle('Остатки');
        $excel->setActiveSheetIndex(0);

        $sheet =  $excel->getActiveSheet();

        $sheet->setCellValue('A1', 'Арикул' );
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->setCellValue('B1', 'Название' );
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getColumnDimension('B')->setWidth(100);
        $sheet->setCellValue('C1', 'Остаток' );
        $sheet->getStyle('C1')->getFont()->setBold(true);
        $sheet->setCellValue('D1', 'Цена (опт)' );
        $sheet->getStyle('D1')->getFont()->setBold(true);
        $sheet->setCellValue('E1', 'Цена (розница)' );
        $sheet->getStyle('E1')->getFont()->setBold(true);
        $sheet->setCellValue('F1', 'Зарезервированый товар' );
        $sheet->getStyle('F1')->getFont()->setBold(true);
        $sheet->setCellValue('G1', 'Незарезервированый товар' );
        $sheet->getStyle('G1')->getFont()->setBold(true);
        $products = Product::model()->findAll($criteria);
        foreach($products as $index => $product){
            /** @var Product $product */
            $cellIndex = $index + 2;
            $sheet->setCellValue('A'.$cellIndex, $product->article );
            $sheet->setCellValue('B'.$cellIndex, $product->fullName );
            $sheet->setCellValue('C'.$cellIndex, $product->remains );
            $sheet->setCellValue('D'.$cellIndex, $product->price );
            $sheet->setCellValue('E'.$cellIndex, $product->price_roznica );
            $sheet->setCellValue('F'.$cellIndex, $product->getBlockedRemains() );
            $sheet->setCellValue('G'.$cellIndex, $product->getNonBlockedRemains() );
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="file.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');

        app()->end();
    }
} 