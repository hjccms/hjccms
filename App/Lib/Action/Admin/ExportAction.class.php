<?php
    
class ExportAction extends BaseAction{
    function __construct() {
        parent::__construct();
        
    }
        
    function dispatch(){
        $startTime = strtotime("2016-01-01");
        $endTime = strtotime("2016-04-18");
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
        if($startYear == $endYear){
            return false;
        }
        
        include './App/Lib/Extend/PHPExcel.php';    
        $objPHPExcel = new PHPExcel(); 
        
        //有几个月创建几个sheet
        for($i=$startMonth;$i<$endMonth;$i++){
            $objPHPExcel->createSheet(); 
        }
        
        //数据操作
        $flag = 0;
        for($i=$startMonth;$i<=$endMonth;$i++){
            
            //选择操作脚本
            $objPHPExcel->setActiveSheetIndex($flag);
            $objActSheet = $objPHPExcel->getActiveSheet();
            //设置当前活动sheet的名称
            $objActSheet->setTitle($i.'月'); 
            
            //获取当月有多少天
            $day = date("t",  strtotime($startYear.'-'.$i.'01'));
            for($j=1;$j<=$day;$j++){
                
            }
            
            $objPHPExcel->getActiveSheet()->getColumnDimension('A1')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A1')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A1')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A1')->setAutoSize(true);
            $objActSheet->setCellValue('A1', '')
                ->setCellValue('B1', '')
                ->setCellValue('C1', '')
                ->setCellValue('D1', '')
                ->setCellValue('E1', '');
            $flag++;
        }
        
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

