<?php
    
class ExportAction extends BaseAction{
    function __construct() {
        parent::__construct();
        
    }
        
    function dispatch(){
        $site_id = $this->adminInfo->site_id;
        
        $startDate = '2016-01-01';
        $endDate = '2016-04-30';
        $startTime = strtotime($startDate);
        $endTime = strtotime($endDate);
        
        //如果开始时间结束时间为空返回false
        if(!$startTime || !$endTime){
            return false;
        }
        
        //获取年月日
        $startYear = date("Y",$startTime);
        $endYear = date("Y",$endTime);
        $startMonth = date("m",$startTime);
        $endMonth = date("m",$endTime);
        $startDay = date("d",$startTime);
        $endDay = date("d",$endTime);
        
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
        
        //获取数据
        $dispatch = D('Dispatch')->getDispatch("site_id={$site_id} and start_time>='{$startDate}' and end_time<='{$endDate}' and valid=1 and del is null");
        $questionnaire = D('Questionnaire')->getQuestionnaire("site_id={$site_id} and start_time>='{$startDate}' and end_time<='{$endDate}' and valid=1 and del is null");    
        $regionData = D('Region')->getRegionList($site_id);
        $region = null;
        foreach($regionData as $k=>$v){
            if($v['childs']){
                foreach($v['childs'] as $ck=>$cv){
                    $region[$cv['id']] = $cv;
                    $region[$cv['id']]['parent_id'] = $v['id'];
                    $region[$cv['id']]['parent_name'] = $v['name'];
                }
            }
        }
        foreach($region as $k=>$v){
            foreach($dispatch as $dk=>$dv){
                if($v['id']==$dv['region_id']){
                    $region[$k]['dispatch'][$dk] = $dv;
                }
            }
            foreach($questionnaire as $qk=>$qv){
                if($v['id']==$qv['region_id']){
                    $region[$k]['questionnaire'][$qk] = $qv;
                }
            }
        }
        $region_count = count($region);
        $flag = 0;//用于定位sheet
        for($i=$startMonth;$i<=$endMonth;$i++){
            //数据处理
            $new_dispatch = null;
            
            for($j=1;$j<=$day;$j++){
                
                foreach($dispatch as $k=>$v){
                    if($start<=strtotime($v['start_time']) && $end>=strtotime($v['end_time'])){
                        $new_dispatch[$j] = $v;
                    }
                }
            }
            
            //选择操作脚本
            $objPHPExcel->setActiveSheetIndex($flag);
            $objActSheet = $objPHPExcel->getActiveSheet();
            
            //设置当前活动sheet的名称
            $objActSheet->setTitle($i.'月'); 
            
            $objActSheet->setCellValue('A1', '')->setCellValue('B1', '')->setCellValue('C1', '')->setCellValue('D1', '')->setCellValue('E1', '');
            $objActSheet->setCellValue('A2', '')->setCellValue('B2', '')->setCellValue('C2', '')->setCellValue('D2', '')->setCellValue('E2', '');
            $objActSheet->setCellValue('A3', '')->setCellValue('B3', '')->setCellValue('C3', '')->setCellValue('D3', '')->setCellValue('E3', '合计');
            $objActSheet->setCellValue('A4', '当日计划派单数量合计')->setCellValue('B4', '')->setCellValue('C4', '')->setCellValue('D4', '');
            $objActSheet->setCellValue('C5', '派单地点及实际数量')->setCellValue('D5', '');
            
            //为excel加图片
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Photo');
            $objDrawing->setDescription('Photo');
            $objDrawing->setPath('./Public/Style/Admin/images/excel_logo.png');
            $objDrawing->setHeight(38);
            $objDrawing->setWidth(197);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($objActSheet);
            
            $str = "F";
            $num = 3;
            $day = date("t",  strtotime($startYear.'-'.$i.'-01'));//获取当月有多少天
            for($j=1;$j<=$day;$j++){
                $objActSheet->setCellValue("{$str}3", $j);
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');
                $num_row = 6;
                foreach($region as $k=>$v){
                    foreach($v['dispatch'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $objActSheet->setCellValue("{$str}{$num_row}", $dv['number']);
                        }
                    }
                    $objActSheet->setCellValue("A{$num_row}", '');
                    $objActSheet->setCellValue("B{$num_row}", '');
                    $objActSheet->setCellValue("C{$num_row}", $v['parent_name']);
                    $objActSheet->setCellValue("D{$num_row}", $v['name']);
                    $num_row++;
                }
                
                if(date("w",  strtotime($startYear.'-'.$i.'-'.$j)) == 6){
                    echo "{$str}{$num}<br>";
                    $objActSheet->getStyle("{$str}{$num}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objActSheet->getStyle("{$str}{$num}")->getFill()->getStartColor()->setRGB("89bfff");
                }
                if(date("w",  strtotime($startYear.'-'.$i.'-'.$j)) == 0){
                    $objActSheet->getStyle("{$str}{$num}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objActSheet->getStyle("{$str}{$num}")->getFill()->getStartColor()->setRGB("c2ffff");
                }
                if($j!=$day){
                    $str++;
                    $num++;
                }
            }
            //设置高height
            $objActSheet->getRowDimension('1')->setRowHeight(38);
            
            //合并单元格 
            $objActSheet->mergeCells('A1:'.$str.'1')->mergeCells('A2:'.$str.'2')->mergeCells('A3:D3')->mergeCells('A4:D4')->mergeCells('A5:B5')->mergeCells('C5:D5'); 
            
            //设置颜色
            $objActSheet->getStyle('A4:'.$str.'4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objActSheet->getStyle('A4:'.$str.'4')->getFill()->getStartColor()->setRGB("fdc30a");
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

