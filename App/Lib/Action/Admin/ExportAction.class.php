<?php
    
class ExportAction extends BaseAction{
    function __construct() {
        parent::__construct();
        
    }
        
    function dispatch(){
        $site_id = $this->adminInfo->site_id;
        
        $startDate = '2016-04-01';
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
            
            //设置边框颜色 
            $objBorderA5 =  $objActSheet->getStyle('A1')->getBorders(); 
            $objBorderA5->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
            $objBorderA5->getTop()->getColor()->setRGB('000000') ; // 边框color 
            $objBorderA5->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
            $objBorderA5->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
            $objBorderA5->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

            
            //设置单元格
            $objActSheet->setCellValue('A1', '')->setCellValue('B1', '')->setCellValue('C1', '')->setCellValue('D1', '')->setCellValue('E1', '');
            $objActSheet->setCellValue('A2', '')->setCellValue('B2', '')->setCellValue('C2', '')->setCellValue('D2', '')->setCellValue('E2', '');
            $objActSheet->setCellValue('A3', '')->setCellValue('B3', '')->setCellValue('C3', '')->setCellValue('D3', '')->setCellValue('E3', '合计');
            $objActSheet->setCellValue('A4', '当日计划派单数量合计')->setCellValue('B4', '')->setCellValue('C4', '')->setCellValue('D4', '');
            $objActSheet->setCellValue('C5', '派单地点及实际数量')->setCellValue('D5', '');
            
            //加logo
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Photo');
            $objDrawing->setDescription('Photo');
            $objDrawing->setPath('./Public/Style/Admin/images/excel_logo.png');
            $objDrawing->setHeight(38);
            $objDrawing->setWidth(197);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($objActSheet);
            
            //设置第一行高height
            $objActSheet->getRowDimension('1')->setRowHeight(38);
            
            //设置宽width
            $objActSheet->getColumnDimension('D')->setWidth(20);
            
            $column = "F";//开始操作列
            $start_column = $column;
            $day = date("t",  strtotime($startYear.'-'.$i.'-01'));//获取当月有多少天
            for($j=1;$j<=$day;$j++){
                $objActSheet->setCellValue("{$column}3", $j);
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                //实际派单数量
                $row = 6;//开始操作行
                $start_row = $row;//开始操作行
                $day_plan_number = 0;
                foreach($region as $k=>$v){
                    foreach($v['dispatch'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $objActSheet->setCellValue("{$column}{$row}", $dv['number']);
                            $day_plan_number = $day_plan_number+$dv['plan_number'];
                        }
                    }
                    $objActSheet->setCellValue("A{$row}", '');
                    $objActSheet->setCellValue("B{$row}", '');
                    $objActSheet->setCellValue("C{$row}", $v['parent_name']);
                    $objActSheet->setCellValue("D{$row}", $v['name']);
                    $row++;
                }
                //当日计划派单数量
                $objActSheet->setCellValue("{$column}4", $day_plan_number);
                //当日实际派单数量
                $objActSheet->setCellValue("{$column}5", "=SUM({$column}{$start_row}:{$column}{$row})");
                
                $row--;
                if($j!=$day){
                    $column++;
                }
            }
            
            //实际派单数量按月统计
            $tmp_1 = $start_row;
            foreach($region as $k=>$v){
                $objActSheet->setCellValue("E{$tmp_1}", "=SUM({$start_column}{$tmp_1}:{$column}{$tmp_1})");
                $tmp_1 ++;
            }
            
            
            //计划派单数量合计
            $objActSheet->setCellValue('E4', "=SUM({$start_column}4:{$column}4)");
            //实际派单数量总计
            $objActSheet->setCellValue('E5', "=SUM({$start_column}5:{$column}5)");
            
            
            if($row>6){
                $objActSheet->setCellValue("A5", '实际派单数量');
                $objActSheet->mergeCells("A5:B{$row}");
            }
            
            
            
            //合并单元格 
            $objActSheet->mergeCells('A1:'.$column.'1')
                ->mergeCells('A2:'.$column.'2')
                ->mergeCells('A3:D3')
                ->mergeCells('A4:D4')
                ->mergeCells('C5:D5'); 
            
            //从指定的单元格复制样式信息. 
            $objActSheet->duplicateStyle($objActSheet->getStyle('A1'), 'A1:'.$column.$row); 
            
            
            //实际获取调卷数量
            $column = "F";//开始操作列
            $start_column = $column;
            $row++;
            $tmp_2 = $row;
            $objActSheet->setCellValue('A'.$row, '当日计划获取调卷数量合计');
            $objActSheet->mergeCells('A'.$row.':D'.$row);
            
            
            $row++;
            $tmp_3 = $row;
            $objActSheet->setCellValue('C'.$row, '调卷地点及实际获取数量');
            
            $row++;
            for($j=1;$j<=$day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                //实际派单数量
                $row_2 = $row;
                $start_row_2 = $row_2;//开始操作行
                $day_plan_number = 0;
                foreach($region as $k=>$v){
                    foreach($v['questionnaire'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $objActSheet->setCellValue("{$column}{$row_2}", $dv['number']);
                            $day_plan_number = $day_plan_number+$dv['plan_number'];
                        }
                    }
                    $objActSheet->setCellValue("A{$row_2}", '');
                    $objActSheet->setCellValue("B{$row_2}", '');
                    $objActSheet->setCellValue("C{$row_2}", $v['parent_name']);
                    $objActSheet->setCellValue("D{$row_2}", $v['name']);
                    //当日计划获取调卷数量
                    $objActSheet->setCellValue("{$column}{$tmp_2}", $day_plan_number);
                    //当日实际获取调卷数量
                    $objActSheet->setCellValue("{$column}{$tmp_3}", "=SUM({$column}{$start_row_2}:{$column}{$row_2})");
                    $row_2++;
                }
                $row_2--;
                if($j!=$day){
                    $column++;
                }
            }
            
            //实际获取调卷量按月统计
            $tmp_4 = $start_row_2;
            foreach($region as $k=>$v){
                $objActSheet->setCellValue("E{$tmp_4}", "=SUM({$start_column}{$tmp_4}:{$column}{$tmp_4})");
                $tmp_1 ++;
            }
            
            $objActSheet->setCellValue('E'.$tmp_2, "=SUM({$start_column}{$tmp_2}:{$column}{$tmp_2})");//计划获取调卷数量合计
            $objActSheet->setCellValue('E'.$tmp_3, "=SUM({$start_column}{$tmp_3}:{$column}{$tmp_3})");//实际获取调卷数量合计

            
            if($row_2>$row){
                $objActSheet->setCellValue("A".$tmp_3, '实际获取调卷数量');
                $objActSheet->mergeCells("A".$tmp_3.":B{$row_2}");
            }
            
            //从指定的单元格复制样式信息. 
            $objActSheet->duplicateStyle($objActSheet->getStyle('A1'), 'A1:'.$column.$row_2); 
            
            //当日计划兼职派单人数
            $column = "F";//开始操作列
            $start_column = $column;
            $row_2++;
            $objActSheet->setCellValue('A'.$row_2, '当日计划兼职派单人数');
            $objActSheet->mergeCells('A'.$row_2.':D'.$row_2);
            
            for($j=1;$j<=$day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $day_parttimer_number = 0;
                foreach($region as $k=>$v){
                    foreach($v['dispatch'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $objActSheet->setCellValue("{$column}{$row}", $dv['number']);
                            $day_parttimer_number = $day_parttimer_number+$dv['parttimer_number'];
                        }
                    }
                }
                $objActSheet->setCellValue("{$column}{$row_2}", $day_parttimer_number);
                if($j!=$day){
                    $column++;
                }
            }
            $objActSheet->setCellValue('E'.$row_2, "=SUM({$start_column}{$row_2}:{$column}{$row_2})");//计划兼职派单人数合计

            //当日学校督导人员及工作安排（批注填写）
            
            $flag++;
        }
        //输出xls
        $outputFileName = "北外少儿教育市场活动计划监测表test.xls"; 
        ob_end_clean() ;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$outputFileName.'.xls');
        header('Cache-Control: No-cache');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}

