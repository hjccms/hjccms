<?php  
class Excel{  
    function xls($file_name,$title,$data){
        
        include './App/Lib/Extend/PHPExcel.php';
        
        //创建一个处理对象实例       
        $objExcel = new PHPExcel();
        
        //选择操作脚本
        $objExcel->setActiveSheetIndex(0); 
        $objActSheet = $objExcel->getActiveSheet();
        
        $column = "A";
        $row = 1;
        foreach($title as $k=>$v){
            $objActSheet->setCellValue($column.$row, $v);
            $column++;
        }
        
        
        $row++;
        foreach($data as $k=>$v){
            $column = "A";
            foreach($v as $vk=>$vv){
                $objActSheet->setCellValue($column.$row, $vv);
                $column++;
            }
            $row++;
        }
        
        //设置对齐方式
        $objActSheet->getStyle("A1:{$column}{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平方向居中
        $objActSheet->getStyle("A1:{$column}{$row}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向居中
        
        ob_end_clean() ;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$file_name.'.xls');
        header('Cache-Control: No-cache');
        $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
        $objWriter->save('php://output');
        
    }
}  
?>