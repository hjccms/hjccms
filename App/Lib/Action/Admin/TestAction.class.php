<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TestAction extends Action {
    
    
    function test(){
        echo date("w",  strtotime('2016-01-03'));
        
    }
    
    function testexcel(){
        include './App/Lib/Extend/PHPExcel.php';
        //创建一个处理对象实例       
        $objExcel = new PHPExcel();  

        //新增sheet
        //$objExcel->createSheet();
        
        //选择操作脚本
        $objExcel->setActiveSheetIndex(0); 
        $objActSheet = $objExcel->getActiveSheet(); 

        //设置当前活动sheet的名称 
        $objActSheet->setTitle('1月'); 

        //设置单元格内容 由PHPExcel根据传入内容自动判断单元格内容类型 
        $objActSheet->setCellValue('A1', ''); 
        $objActSheet->setCellValue('B1', '市场活动计划监测表'); 
        $objActSheet->setCellValue('C1', ''); 
        $objActSheet->setCellValue('D1', '');
        $objActSheet->setCellValue('E1', ''); 
        $objActSheet->setCellValue('F1', ''); 
        $objActSheet->setCellValue('G1', '');
        $objActSheet->setCellValue('H1', '');
        $objActSheet->setCellValue('I1', '');
        $objActSheet->setCellValue('J1', '');
        $objActSheet->setCellValue('K1', ''); 
        $objActSheet->setCellValue('L1', ''); 
        
        //设置宽width
        //$objActSheet->getColumnDimension('A1')->setAutoSize(true);
        //$objActSheet->getColumnDimension('A1')->setWidth(12);
        
        //设置高height
        $objActSheet->getRowDimension('1')->setRowHeight(38);

        //合并单元格 
        $objActSheet->mergeCells('A1:L1'); 
        
        //为excel加图片
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Photo');
        $objDrawing->setDescription('Photo');
        $objDrawing->setPath('./Public/Style/Admin/images/excel_logo.png');
        $objDrawing->setHeight(38);
        $objDrawing->setWidth(197);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($objActSheet);
        
        
        
        //设置单元格内容 由PHPExcel根据传入内容自动判断单元格内容类型 
        $objActSheet->setCellValue('A2', ''); 
        $objActSheet->setCellValue('B2', ''); 
        $objActSheet->setCellValue('C2', ''); 
        $objActSheet->setCellValue('D2', '');
        $objActSheet->setCellValue('E2', ''); 
        $objActSheet->setCellValue('F2', ''); 
        $objActSheet->setCellValue('G2', '');
        $objActSheet->setCellValue('H2', '');
        $objActSheet->setCellValue('I2', '');
        $objActSheet->setCellValue('J2', '');
        $objActSheet->setCellValue('K2', ''); 
        $objActSheet->setCellValue('L2', ''); 
        //合并单元格 
        $objActSheet->mergeCells('A2:L2'); 
        
        //设置单元格内容 由PHPExcel根据传入内容自动判断单元格内容类型 
        $objActSheet->setCellValue('A3', ''); 
        $objActSheet->setCellValue('B3', ''); 
        $objActSheet->setCellValue('C3', ''); 
        $objActSheet->setCellValue('D3', '');
        $objActSheet->setCellValue('E3', '合计'); 
        $objActSheet->setCellValue('F3', '1'); 
        $objActSheet->setCellValue('G3', '2');
        $objActSheet->setCellValue('H3', '3');
        $objActSheet->setCellValue('I3', '4');
        $objActSheet->setCellValue('J3', '5');
        $objActSheet->setCellValue('K3', '6'); 
        $objActSheet->setCellValue('L3', '7'); 
        //合并单元格 
        $objActSheet->mergeCells('A3:D3');
        
        //设置单元格颜色
        $objActSheet->getStyle('K3:K10')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objActSheet->getStyle('K3:K10')->getFill()->getStartColor()->setRGB("89bfff");
        $objActSheet->getStyle('L3:L10')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objActSheet->getStyle('L3:L10')->getFill()->getStartColor()->setRGB("c2ffff");
        
        //设置单元格内容 由PHPExcel根据传入内容自动判断单元格内容类型 
        $objActSheet->setCellValue('A4', '当日计划派单数量合计'); 
        $objActSheet->setCellValue('B4', ''); 
        $objActSheet->setCellValue('C4', ''); 
        $objActSheet->setCellValue('D4', '');
        $objActSheet->setCellValue('E4', ''); 
        $objActSheet->setCellValue('F4', ''); 
        $objActSheet->setCellValue('G4', '');
        $objActSheet->setCellValue('H4', '');
        $objActSheet->setCellValue('I4', '');
        $objActSheet->setCellValue('J4', '');
        $objActSheet->setCellValue('K4', ''); 
        $objActSheet->setCellValue('L4', ''); 
        //合并单元格 
        $objActSheet->mergeCells('A4:D4');
        $objActSheet->getStyle('A4:L4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objActSheet->getStyle('A4:L4')->getFill()->getStartColor()->setRGB("fdc30a");
                
        
        //设置边框 
        $objBorderA5 =  $objActSheet->getStyle('A5')->getBorders(); 
        $objBorderA5->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        $objBorderA5->getTop()->getColor()->setARGB('FFFF0000') ; // 边框color 
        $objBorderA5->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        $objBorderA5->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        $objBorderA5->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
        
        //从指定的单元格复制样式信息. 
        $objActSheet->duplicateStyle($objActSheet->getStyle('A5'), 'A9:L9'); 
        
        //设置对齐方式
        $objActSheet->getStyle("A1:E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平方向居中
        $objActSheet->getStyle("A1:E1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向居中
        
//        $objExcel->setActiveSheetIndex(1); 
//        $objActSheet2 = $objExcel->getActiveSheet(); 
//
//        //设置当前活动sheet的名称 
//        $objActSheet2->setTitle('北外少儿教育市场活动计划监测表2'); 
//
//        //设置单元格内容 由PHPExcel根据传入内容自动判断单元格内容类型 
//        $objActSheet2->setCellValue('A1', '市场活动计划监测表2'); 
//        $objActSheet2->setCellValue('B1', ''); 
//        $objActSheet2->setCellValue('C1', ''); 
//        $objActSheet2->setCellValue('D1', '');
//        $objActSheet2->setCellValue('E1', ''); 
//        $objActSheet2->setCellValue('F1', ''); 
//        $objActSheet2->setCellValue('G1', '');
//        $objActSheet2->setCellValue('H1', '');
//        $objActSheet2->setCellValue('I1', '');
//        $objActSheet2->setCellValue('J1', '');
//        $objActSheet2->setCellValue('K1', ''); 
//        $objActSheet2->setCellValue('L1', ''); 
//
//        //合并单元格 
//        $objActSheet2->mergeCells('A1:L1'); 
        
       
        //设置批注
        $objExcel->getActiveSheet()->getComment('E6')->setAuthor('PHPExcel');  
        $objCommentRichText = $objExcel->getActiveSheet()->getComment('E6')->getText()->createTextRun('PHPExcel:');  
        $objCommentRichText->getFont()->setBold(true);  
        $objExcel->getActiveSheet()->getComment('E6')->getText()->createTextRun("\r\n");  
        $objExcel->getActiveSheet()->getComment('E6')->getText()->createTextRun('Total amount on the current invoice, excluding VAT.');
        
        
        //输出xls
        $outputFileName = "北外少儿教育市场活动计划监测表test.xls"; 
        ob_end_clean() ;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$outputFileName.'.xls');
        header('Cache-Control: No-cache');
        $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
        $objWriter->save('php://output');
        
    }
    
    
    
}
