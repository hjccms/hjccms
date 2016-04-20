<?php
    
class ExportAction extends BaseAction{
    function __construct() {
        parent::__construct();
        
    }
        
    function dispatch(){
        $startTime = strtotime("2016-01-01");
        $endTime = strtotime("2016-01-18");
        $now = time();
        
        //如果开始时间结束时间为空返回false
        if(!$startTime || !$endTime){
            return false;
        }
        
        //获取年份
        $startYear = date("Y",$startTime);
        $endYear = date("Y",$endTime);
        $nowYear = date("Y",$now);
        
        //获取月份
        $startMonth = date("m",$startTime);
        $endMonth = date("m",$endTime);
        $nowMonth = date("m",$now);
        
        //获取日
        $startDay = date("d",$startTime);
        $endDay = date("d",$endTime);
        $nowDay = date("d",$now);
        
        //如果不是同一年返回false
        if($startYear != $endYear){
            return false;
        }
        
        include './App/Lib/Extend/PHPExcel.php';    
        $objPHPExcel = new PHPExcel(); 
        
        //有几个月创建几个sheet
        for($i=$startMonth;$i<$endMonth;$i++){
            $objPHPExcel->createSheet(); 
        }
        
        $dispatch = D('Dispatch')->getDispatch('start_time<='.$startTime && 'end_time>='.$endTime);
        $questionnaire = D('Questionnaire')->getQuestionnaire('start_time<='.$startTime && 'end_time>='.$endTime);
        
        //数据操作
        $flag = 0;
        for($i=$startMonth;$i<=$endMonth;$i++){
            
//            //选择操作脚本
//            $objPHPExcel->setActiveSheetIndex($flag);
//            $objActSheet = $objPHPExcel->getActiveSheet();
//            //设置当前活动sheet的名称
//            $objActSheet->setTitle($i.'月'); 
//            
//            //获取当月有多少天
//            $day = date("t",  strtotime($startYear.'-'.$i.'-01'));
//            
//            $objActSheet->setCellValue('A1', '')->setCellValue('B1', '')->setCellValue('C1', '')->setCellValue('D1', '')->setCellValue('E1', '');
//            $objActSheet->setCellValue('A2', '')->setCellValue('B2', '')->setCellValue('C2', '')->setCellValue('D2', '')->setCellValue('E2', '');
//            $objActSheet->setCellValue('A3', '')->setCellValue('B3', '')->setCellValue('C3', '')->setCellValue('D3', '')->setCellValue('E3', '合计');
//            $objActSheet->setCellValue('A4', '当日计划派单数量合计')->setCellValue('B4', '')->setCellValue('C4', '')->setCellValue('D4', '');
//            $objActSheet->setCellValue('C5', '派单地点及实际数量')->setCellValue('D5', '');
//            
//            //为excel加图片
//            $objDrawing = new PHPExcel_Worksheet_Drawing();
//            $objDrawing->setName('Photo');
//            $objDrawing->setDescription('Photo');
//            $objDrawing->setPath('./Public/Style/Admin/images/excel_logo.png');
//            $objDrawing->setHeight(38);
//            $objDrawing->setWidth(197);
//            $objDrawing->setCoordinates('A1');
//            $objDrawing->setWorksheet($objActSheet);
//            
//            $str = "F";
//            for($j=1;$j<=$day;$j++){
//                $objActSheet->setCellValue($str.'1', '')->setCellValue($str.'2', '')->setCellValue($str.'3', $j);
//                if(date("w",  strtotime($startYear.'-'.$i.'-'.$j)) == 6){
//                    $objActSheet->getStyle($str.'3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//                    $objActSheet->getStyle($str.'3')->getFill()->getStartColor()->setRGB("89bfff");
//                }
//                if(date("w",  strtotime($startYear.'-'.$i.'-'.$j)) == 0){
//                    $objActSheet->getStyle($str.'3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//                    $objActSheet->getStyle($str.'3')->getFill()->getStartColor()->setRGB("c2ffff");
//                }
//                if($j!=$day){
//                    $str++;
//                }
//            }
//            //设置高height
//            $objActSheet->getRowDimension('1')->setRowHeight(38);
//            
//            //合并单元格 
//            $objActSheet->mergeCells('A1:'.$str.'1')->mergeCells('A2:'.$str.'2')->mergeCells('A3:D3')->mergeCells('A4:D4')->mergeCells('A5:B5')->mergeCells('C5:D5'); 
//            
//            //设置颜色
//            $objActSheet->getStyle('A4:'.$str.'4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//            $objActSheet->getStyle('A4:'.$str.'4')->getFill()->getStartColor()->setRGB("fdc30a");
            $flag++;
        }
        //输出xls
//        $outputFileName = "北外少儿教育市场活动计划监测表test.xls"; 
//        ob_end_clean() ;
//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename='.$outputFileName.'.xls');
//        header('Cache-Control: No-cache');
//        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//        $objWriter->save('php://output');
    }
}

