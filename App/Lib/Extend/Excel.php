<?php  
class Excel{  
    function xls($file_name,$title,$data){
        
        include './App/Lib/Extend/PHPExcel.php';
        
        //创建一个处理对象实例       
        $objExcel = new PHPExcel();
        
        //选择操作脚本
        $objExcel->setActiveSheetIndex(0); 
        $objActSheet = $objExcel->getActiveSheet();
        
        //设置边框颜色 
        $objBorderA5 =  $objActSheet->getStyle('A1')->getBorders(); 
        $objBorderA5->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        $objBorderA5->getTop()->getColor()->setRGB('000000') ; // 边框color 
        $objBorderA5->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        $objBorderA5->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        $objBorderA5->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        
        $column = "A";
        $row = 1;
        $count = count($title);
        $num = 1;
        foreach($title as $k=>$v){
            $objActSheet->setCellValue($column.$row, $v);
            if($num != $count){
                $column++;
            }
            $num++;
        }
        
        $row++;
        foreach($data as $k=>$v){
            $column = "A";
            $num = 1;
            foreach($v as $vk=>$vv){
                $objActSheet->setCellValue($column.$row, $vv);
                if($num != $count){
                    $column++;
                }
                $num++;
            }
            $row++;
        }
        $row--;
        
        //从指定的单元格复制样式信息. 
        $objActSheet->duplicateStyle($objActSheet->getStyle('A1'), 'A1:'.$column.$row); 
        
        //设置对齐方式
        $objActSheet->getStyle("A1:{$column}{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平方向居中
        $objActSheet->getStyle("A1:{$column}{$row}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向居中
        
        //设置填充的样式和背景色
        $objActSheet->getStyle("A1:{$column}1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objActSheet->getStyle("A1:{$column}1")->getFill()->getStartColor()->setRGB('fdc30a');
        
        ob_end_clean() ;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$file_name.'.xls');
        header('Cache-Control: No-cache');
        $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
        $objWriter->save('php://output');
        
    }
    
    function html($file_name,$title,$data){
            
        include './App/Lib/Extend/PHPExcel.php';
        
        //创建一个处理对象实例       
        $objExcel = new PHPExcel();
        
        //选择操作脚本
        $objExcel->setActiveSheetIndex(0); 
        $objActSheet = $objExcel->getActiveSheet();
        
        //设置边框颜色 
        $objBorderA5 =  $objActSheet->getStyle('A1')->getBorders(); 
        $objBorderA5->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        $objBorderA5->getTop()->getColor()->setRGB('000000') ; // 边框color 
        $objBorderA5->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        $objBorderA5->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        $objBorderA5->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        
        $column = "A";
        $row = 1;
        $count = count($title);
        $num = 1;
        foreach($title as $k=>$v){
            $objActSheet->setCellValue($column.$row, $v);
            if($num != $count){
                $column++;
            }
            $num++;
        }
        
        $row++;
        foreach($data as $k=>$v){
            $column = "A";
            $num = 1;
            foreach($v as $vk=>$vv){
                $objActSheet->setCellValue($column.$row, $vv);
                if($num != $count){
                    $column++;
                }
                $num++;
            }
            $row++;
        }
        $row--;
        
        //从指定的单元格复制样式信息. 
        $objActSheet->duplicateStyle($objActSheet->getStyle('A1'), 'A1:'.$column.$row); 
        
        //设置对齐方式
        $objActSheet->getStyle("A1:{$column}{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平方向居中
        $objActSheet->getStyle("A1:{$column}{$row}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向居中
        
        //设置填充的样式和背景色
        $objActSheet->getStyle("A1:{$column}1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objActSheet->getStyle("A1:{$column}1")->getFill()->getStartColor()->setRGB('fdc30a');
        
        //将$objPHPEcel对象转换成html格式的
        $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'HTML');  
        $objWriter->save('php://output');
        
    }
}  
?>