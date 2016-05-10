<?php
    
class ExportAction extends BaseAction{
    
    public $site_id;
        
    function __construct() {
        parent::__construct();
        $this->site_id = $this->adminInfo->site_id;
        
    }
        
    function dispatch(){
        
        load("@.form");
        
        $post_start_date = $this->_post('start_date');
        $post_end_date = $this->_post('end_date');
        $post_site_id = $this->_post('site_id');

        $site_id = $this->site_id == 1?($post_site_id?$post_site_id:$this->site_id):$this->site_id;
        
        if($post_start_date && $post_end_date){
            $startDate = $post_start_date?$post_start_date:date("Y-m-01");
            $endDate = $post_end_date?$post_end_date:date("Y-m-d");
        }else{
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-d");
        }
        
        //如果开始时间大于结束时间返回false
        if($startDate>$endDate){
            return false;
        }
        $sites = D('Site')->getSite("valid=1");
        $this->assign('sites',$sites);
        $this->assign('site_id',$this->site_id);
        $this->assign('post_site_id',$site_id);
        $this->assign('post_start_date',$startDate);
        $this->assign('post_end_date',$endDate);
        $this->display();
              
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
        
        if(!$regionData){
            return false;
        }
        
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
        
        //开始绘制表格
        $sheet = 0;//用于定位当前sheet
        for($i=$startMonth;$i<=$endMonth;$i++){
            
            //选择操作脚本
            $objPHPExcel->setActiveSheetIndex($sheet);
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

            //设置单元格--派单数量start
            $row = 1;
            $objActSheet->setCellValue('A'.$row, $startYear.'年'.$i.'月')->setCellValue('B'.$row, '')->setCellValue('C'.$row, '')->setCellValue('D'.$row, '')->setCellValue('E'.$row, '');           
//            $objDrawing = new PHPExcel_Worksheet_Drawing();//加logo
//            $objDrawing->setName('Photo');
//            $objDrawing->setDescription('Photo');
//            $objDrawing->setPath("./Public/Style/Admin/images/excel_logo.png");
//            $objDrawing->setHeight(38);
//            $objDrawing->setWidth(197);
//            $objDrawing->setCoordinates('A1');
//            $objDrawing->setWorksheet($objActSheet);
//            $objActSheet->getRowDimension('1')->setRowHeight(38);//设置第一行高height
//            $objActSheet->getColumnDimension('D')->setWidth(20);//设置宽width
            
            $row++;
            $objActSheet->setCellValue('A'.$row, '市场活动计划监测表')->setCellValue('B'.$row, '')->setCellValue('C'.$row, '')->setCellValue('D'.$row, '')->setCellValue('E'.$row, '');
            
            $row++;
            $objActSheet->setCellValue('A'.$row, '')->setCellValue('B'.$row, '')->setCellValue('C'.$row, '')->setCellValue('D'.$row, '')->setCellValue('E'.$row, '合计');
            
            $row++;
            $color_row_1 = $row;
            $objActSheet->setCellValue('A'.$row, '当日计划派单数量合计')->setCellValue('B'.$row, '')->setCellValue('C'.$row, '')->setCellValue('D'.$row, '');
            
            $row++;
            $color_row_2 = $row;
            $color_row_12_start = $row;
            $objActSheet->setCellValue('C'.$row, '派单地点及实际数量')->setCellValue('D'.$row, '');
            
            $row++;
            $column = "F";//开始操作列
            $start_column = $column;
            if($i == $startMonth){
                $start_day = $startDay;
            }else{
                $start_day = 1;//获取当月有多少天
            }
            if($i == $endMonth){
                $end_day = $endDay;
            }else{
                $end_day = date("t",  strtotime($startYear.'-'.$i.'-01'));//获取当月有多少天
            }
            for($j=$start_day;$j<=$end_day;$j++){
                $objActSheet->setCellValue("{$column}3", $j);
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                //实际派单数量
                $handle_row = $row;//开始操作行
                $day_number = null;//每日实际派单总计
                $day_plan_number = null;//每日计划派单总计
                foreach($region as $k=>$v){
                    foreach($v['dispatch'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $day_number = $day_number+$dv['number'];
                            $day_plan_number = $day_plan_number+$dv['plan_number'];
                        }
                    }
                    $objActSheet->setCellValue("A{$handle_row}", '');
                    $objActSheet->setCellValue("B{$handle_row}", '');
                    $objActSheet->setCellValue("C{$handle_row}", $v['parent_name']);//一级地址
                    $objActSheet->setCellValue("D{$handle_row}", $v['name']);//二级地址
                    
                    $objActSheet->setCellValue("{$column}{$handle_row}", $day_number);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                $color_row_12_end = $handle_row;
                $objActSheet->setCellValue("{$column}4", $day_plan_number);//当日计划派单数量
                $objActSheet->setCellValue("{$column}5", "=SUM({$column}{$row}:{$column}{$handle_row})");//当日实际派单数量
                
                if($j!=$end_day){
                    $column++;
                }
            }
            
            $tmp_row = $row;
            foreach($region as $k=>$v){
                $objActSheet->setCellValue("E{$tmp_row}", "=SUM({$start_column}{$tmp_row}:{$column}{$tmp_row})");//实际派单数量按月统计
                $tmp_row ++;
            }
            
            $objActSheet->setCellValue('E4', "=SUM({$start_column}4:{$column}4)");//计划派单数量合计
            $objActSheet->setCellValue('E5', "=SUM({$start_column}5:{$column}5)");//实际派单数量总计
            
            if($handle_row>$row){
                $objActSheet->setCellValue("A5", '实际派单数量');
                $objActSheet->mergeCells("A5:B{$handle_row}");
            }
            
            $objActSheet->mergeCells('A1:'.$column.'1')->mergeCells('A2:'.$column.'2')->mergeCells('A3:D3')->mergeCells('A4:D4')->mergeCells('C5:D5');//合并单元格 
            
            $row = $handle_row;
            //设置单元格--派单数量end
            
            //设置单元格--获取调卷数量start
            $column = "F";
            $start_column = $column;
            $row++;
            $tmp_row_1 = $row;
            $color_row_3 = $row;
            $objActSheet->setCellValue('A'.$row, '当日计划获取调卷数量合计');
            $objActSheet->mergeCells('A'.$row.':D'.$row);
            
            $row++;
            $tmp_row_2 = $row;
            $color_row_4 = $row;
            $color_row_13_start = $row;
            $objActSheet->setCellValue('C'.$row, '调卷地点及实际获取数量');
            $objActSheet->mergeCells('C'.$row.':D'.$row);
            
            $row++;
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                //实际调卷数量
                $handle_row = $row;
                $day_number = null;//每日实际调卷总计
                $day_plan_number = null;//每日计划调卷总计
                foreach($region as $k=>$v){
                    foreach($v['questionnaire'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $day_number = $day_number+$dv['number'];
                            $day_plan_number = $day_plan_number+$dv['plan_number'];
                        }
                    }
                    $objActSheet->setCellValue("A{$handle_row}", '');
                    $objActSheet->setCellValue("B{$handle_row}", '');
                    $objActSheet->setCellValue("C{$handle_row}", $v['parent_name']);//一级地址
                    $objActSheet->setCellValue("D{$handle_row}", $v['name']);//二级地址
                    
                    $objActSheet->setCellValue("{$column}{$handle_row}", $day_number);
                    
                    $objActSheet->setCellValue("{$column}{$tmp_row_1}", $day_plan_number);//当日计划获取调卷数量
                    $objActSheet->setCellValue("{$column}{$tmp_row_2}", "=SUM({$column}{$row}:{$column}{$handle_row})");//当日实际获取调卷数量
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                $color_row_13_end = $handle_row;
                if($j!=$end_day){
                    $column++;
                }
            }
            
            $tmp_row = $row;
            foreach($region as $k=>$v){
                $objActSheet->setCellValue("E{$tmp_row}", "=SUM({$start_column}{$tmp_row}:{$column}{$tmp_row})");//实际获取调卷量按月统计
                $tmp_row ++;
            }
            
            $objActSheet->setCellValue('E'.$tmp_row_1, "=SUM({$start_column}{$tmp_row_1}:{$column}{$tmp_row_1})");//计划获取调卷数量合计
            $objActSheet->setCellValue('E'.$tmp_row_2, "=SUM({$start_column}{$tmp_row_2}:{$column}{$tmp_row_2})");//实际获取调卷数量合计

            if($handle_row>$row){
                $objActSheet->setCellValue("A".$tmp_row_2, '实际获取调卷数量');
                $objActSheet->mergeCells("A".$tmp_row_2.":B{$handle_row}");
            }
            $row = $handle_row;
            //设置单元格--获取调卷数量end
            
            //设置单元格--当日计划兼职派单人数start
            $column = "F";
            $start_column = $column;
            $row++;
            $color_row_5 = $row;
            $objActSheet->setCellValue('A'.$row, '当日计划兼职派单人数');
            $objActSheet->mergeCells('A'.$row.':D'.$row);
            
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $day_parttimer_number = null;//每日派单兼职人数总计
                foreach($region as $k=>$v){
                    foreach($v['dispatch'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $day_parttimer_number = $day_parttimer_number+$dv['parttimer_number'];
                        }
                    }
                }
                $objActSheet->setCellValue("{$column}{$row}", $day_parttimer_number);
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue('E'.$row, "=SUM({$start_column}{$row}:{$column}{$row})");//计划兼职派单人数合计
            //设置单元格--当日计划兼职派单人数end

            //设置单元格--当日学校督导人员及工作安排（批注填写）start
            $column = "F";//开始操作列
            $start_column = $column;
            $row++;
            $color_row_6 = $row;
            $objActSheet->setCellValue('A'.$row, '当日学校督导人员及工作安排（批注填写）');
            $objActSheet->mergeCells('A'.$row.':E'.$row);
            
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $day_parttimer_number = null;//每日派单兼职人数总计
                $supervisor = null;
                foreach($region as $k=>$v){
                    foreach($v['dispatch'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $day_parttimer_number = $day_parttimer_number+$dv['parttimer_number'];
                            $supervisor = $dv['supervisor']?$dv['supervisor']:$supervisor;
                        }
                    }
                }
                $objActSheet->getComment("{$column}{$row}")->getText()->createTextRun("学校人员姓名:{$supervisor}");//设置批注 
                $objActSheet->getComment("{$column}{$row}")->getText()->createTextRun("\r\n");  
                $objActSheet->getComment("{$column}{$row}")->getText()->createTextRun("带领兼职人数:{$day_parttimer_number}");//设置批注  
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue('E'.$row, "=SUM({$start_column}{$row}:{$column}{$row})");//计划兼职派单人数合计
            //设置单元格--当日学校督导人员及工作安排（批注填写）end
               
            //设置单元格--兼职派单人员工作小时start
            $column = "F";
            $start_column = $column;
            $row++;
            $tmp_row_1 = $row;
            $color_row_7 = $row;
            $color_row_14_start = $row;
            $objActSheet->setCellValue('C'.$row, '实际兼职派单人员工作小时合计');
            $objActSheet->mergeCells('C'.$row.':D'.$row);
            
            $row++;
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $handle_row = $row;
                $day_parttimer_time = null;//每日派单兼职小时总计
                foreach($region as $k=>$v){
                    foreach($v['dispatch'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $day_parttimer_time = $day_parttimer_time+$dv['parttimer_time'];
                        }
                    }
                    $objActSheet->setCellValue("{$column}{$handle_row}", $day_parttimer_time);
                    $objActSheet->setCellValue("A{$handle_row}", '');
                    $objActSheet->setCellValue("B{$handle_row}", '');
                    $objActSheet->setCellValue("C{$handle_row}", $v['parent_name']);
                    $objActSheet->setCellValue("D{$handle_row}", $v['name']);
                    //实际兼职派单人员工作小时合计
                    $objActSheet->setCellValue("{$column}{$tmp_row_1}", "=SUM({$column}{$row}:{$column}{$handle_row})");
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                $color_row_14_end = $handle_row;
                if($j!=$end_day){
                    $column++;
                }
            }
            
            $tmp_row = $row;
            foreach($region as $k=>$v){
                $objActSheet->setCellValue("E{$tmp_row}", "=SUM({$start_column}{$tmp_row}:{$column}{$tmp_row})");//实际兼职派单人员工作小时合计按月统计
                $tmp_row ++;
            }
            
            $objActSheet->setCellValue("E".$tmp_row_1, "=SUM({$start_column}{$tmp_row_1}:{$column}{$tmp_row_1})");//实际兼职派单人员工作小时合计
            
            if($handle_row>$row){
                $objActSheet->setCellValue("A".$tmp_row_1, '兼职派单人员工作小时');
                $objActSheet->mergeCells("A".$tmp_row_1.":B{$handle_row}");
            }
            $row = $handle_row;
            //设置单元格--兼职派单人员工作小时end
            
            //设置单元格--单页成本start
            $column = "F";
            $start_column = $column;
            $row++;
            $page_row = $row;
            $color_row_8 = $row;
            $objActSheet->setCellValue('A'.$row, '单页成本');
            $objActSheet->mergeCells('A'.$row.':D'.$row);
            
            for($j=$start_day;$j<=$end_day;$j++){
                $objActSheet->setCellValue("{$column}{$row}", "={$column}5*0.5");
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue('E'.$row, "=SUM({$start_column}{$row}:{$column}{$row})");//单页成本合计
            //设置单元格--单页成本start
                        
            //设置单元格--兼职工资start
            $column = "F";
            $start_column = $column;
            $row++;
            $color_row_9 = $row;
            $objActSheet->setCellValue('A'.$row, '兼职工资');
            $objActSheet->mergeCells('A'.$row.':D'.$row);
            
            for($j=$start_day;$j<=$end_day;$j++){
                $objActSheet->setCellValue("{$column}{$row}", "={$column}{$tmp_row_1}*18");
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue('E'.$row, "=SUM({$start_column}{$row}:{$column}{$row})");//兼职工资合计
            //设置单元格--兼职工资end
                        
            //设置单元格--物料/租金等成本start
            $column = "F";
            $start_column = $column;
            $row++;
            $materiel_row = $row;
            $color_row_10 = $row;
            $objActSheet->setCellValue('A'.$row, '物料/租金等成本');
            $objActSheet->mergeCells('A'.$row.':D'.$row);
            
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $day_materiel_cost = null;//每日物料/租金总计
                foreach($region as $k=>$v){
                    foreach($v['dispatch'] as $dk=>$dv){
                        if($start<=strtotime($dv['start_time']) && $end>=strtotime($dv['end_time'])){
                            $day_materiel_cost = $day_materiel_cost+$dv['materiel_cost'];
                        }
                    }
                }
                $objActSheet->setCellValue("{$column}{$row}", $day_materiel_cost);
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue('E'.$row, "=SUM({$start_column}{$row}:{$column}{$row})");//物料/租金等成本合计
            //设置单元格--物料/租金等成本end
    
            //设置单元格--派单总成本start
            $column = "F";
            $start_column = $column;
            $row++;
            $color_row_11 = $row;
            $objActSheet->setCellValue('A'.$row, '派单总成本');
            $objActSheet->mergeCells('A'.$row.':D'.$row);
            
            for($j=$start_day;$j<=$end_day;$j++){
                $objActSheet->setCellValue("{$column}{$row}", "=SUM({$column}{$page_row}:{$column}{$materiel_row})");
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue('E'.$row, "=SUM({$start_column}{$row}:{$column}{$row})");//派单总成本
            //设置单元格--派单总成本end
            
            //从指定的单元格复制样式信息. 
            $objActSheet->duplicateStyle($objActSheet->getStyle('A1'), 'A1:'.$column.$row); 
            
            
            
            //设置填充的样式和背景色
            $objActSheet->getStyle("A1:{$column}{$row}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $column = "F";
            for($j=$start_day;$j<=$end_day;$j++){
                if(date("w",strtotime($startYear.'-'.$i.'-'.$j)) == '6'){
                    $objActSheet->getStyle("{$column}3:{$column}{$row}")->getFill()->getStartColor()->setRGB('89bfff');
                }
                if(date("w",strtotime($startYear.'-'.$i.'-'.$j)) == '0'){
                    $objActSheet->getStyle("{$column}3:{$column}{$row}")->getFill()->getStartColor()->setRGB('c2ffff');
                }
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->getStyle("E4:E{$row}")->getFill()->getStartColor()->setRGB('b3b3b3');
            $objActSheet->getStyle("A{$color_row_1}:{$column}{$color_row_1}")->getFill()->getStartColor()->setRGB('fdc30a');
            $objActSheet->getStyle("A{$color_row_2}:{$column}{$color_row_2}")->getFill()->getStartColor()->setRGB('535187');
            $objActSheet->getStyle("A{$color_row_3}:{$column}{$color_row_3}")->getFill()->getStartColor()->setRGB('fdc30a');
            $objActSheet->getStyle("A{$color_row_4}:{$column}{$color_row_4}")->getFill()->getStartColor()->setRGB('535187');
            $objActSheet->getStyle("A{$color_row_5}:{$column}{$color_row_5}")->getFill()->getStartColor()->setRGB('fdc30a');
            $objActSheet->getStyle("A{$color_row_6}:{$column}{$color_row_6}")->getFill()->getStartColor()->setRGB('fdc30a');
            $objActSheet->getStyle("A{$color_row_7}:{$column}{$color_row_7}")->getFill()->getStartColor()->setRGB('535187');
            $objActSheet->getStyle("A{$color_row_8}:{$column}{$color_row_8}")->getFill()->getStartColor()->setRGB('c080ff');
            $objActSheet->getStyle("A{$color_row_9}:{$column}{$color_row_9}")->getFill()->getStartColor()->setRGB('c080ff');
            $objActSheet->getStyle("A{$color_row_10}:{$column}{$color_row_10}")->getFill()->getStartColor()->setRGB('c080ff');
            $objActSheet->getStyle("A{$color_row_11}:{$column}{$color_row_11}")->getFill()->getStartColor()->setRGB('fd82c0');
            $objActSheet->getStyle("A{$color_row_12_start}:D{$color_row_12_end}")->getFill()->getStartColor()->setRGB('ffff88');
            $objActSheet->getStyle("A{$color_row_13_start}:D{$color_row_13_end}")->getFill()->getStartColor()->setRGB('ffff88');
            $objActSheet->getStyle("A{$color_row_14_start}:D{$color_row_14_end}")->getFill()->getStartColor()->setRGB('ffff88');
            
            //设置对齐方式
            $objActSheet->getStyle("A1:{$column}{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平方向居中
            $objActSheet->getStyle("A1:{$column}{$row}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向居中
            
            $sheet++;
        }
        
        if($this->_post("subType") == "导出"){
            //输出xls
            $outputFileName = "北外少儿教育市场活动计划监测表"; 
            ob_end_clean() ;
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$outputFileName.'.xls');
            header('Cache-Control: No-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }else{
            //将$objPHPEcel对象转换成html格式的
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');  
            $objWriter->save('php://output');
        }
    }
    
    //咨询登记汇总表
    function record(){
        load("@.form");
        $post_start_date = $this->_post('start_date');
        $post_end_date = $this->_post('end_date');
        $post_site_id = $this->_post('site_id');

        $site_id = $this->site_id == 1?($post_site_id?$post_site_id:$this->site_id):$this->site_id;
        
        if($post_start_date && $post_end_date){
            $startDate = $post_start_date?$post_start_date:date("Y-m-01");
            $endDate = $post_end_date?$post_end_date:date("Y-m-d");
        }else{
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-d");
        }
        
        //如果开始时间大于结束时间返回false
        if($startDate>$endDate){
            return false;
        }
        $sites = D('Site')->getSite("valid=1");
        $this->assign('sites',$sites);
        $this->assign('site_id',$this->site_id);
        $this->assign('post_site_id',$site_id);
        $this->assign('post_start_date',$startDate);
        $this->assign('post_end_date',$endDate);
        $this->display();
              
        $startTime = strtotime($startDate);
        $endTime = strtotime($endDate."23:59:59");
        
        //如果开始时间结束时间为空返回false
        if(!$startTime || !$endTime){
            return false;
        }
   
        //获取数据
        $record = D('Student_record')->getRecord("type=1 and status in (1,2,3,4) and create_time>='{$startTime}' and create_time<='{$endTime}' and del is null","create_time desc");
        $student = D("Student")->getStudent("site_id={$site_id} and valid=1 and del is null");
        $flag = false;
        foreach($record as $k=>$v){
            foreach($student as $sk=>$sv){
                if($sv['id'] == $v['student_id']){
                    $flag = true;
                    $record[$k] = array_merge($sv,$v);
                }
            } 
            if(!$flag){
                unset($record[$k]);
            }
        }
        
        if(!$record){
            return false;
        }
        
        $title = array('序号','咨询日期','咨询类型','咨询进度','咨询量创造人','咨询师','意向程度','咨询课程','学员姓名','性别','年龄','就读幼儿园/学校','住址','咨询家长','联系方式','电话沟通记录','当面沟通记录','回访跟进记录');
        $num = 1;
        $data = null;
        foreach($record as $k=>$v){
            $data[$num]['num'] = $num;
            $data[$num]['create_date'] = date("Y-m-d H:i",$v['create_time']);
            $data[$num]['type'] = get_record_type($v['type']);
            $data[$num]['speed'] = $v['speed'];
            $data[$num]['add_name'] = $v['add_name'];
            $data[$num]['add_name2'] = $v['add_name'];
            $data[$num]['intention'] = $v['intention'];
            $data[$num]['course'] = $v['course'];
            $data[$num]['stu_name'] = $v['name']?$v['name']:$v['en_name'];
            $data[$num]['stu_gender'] = $v['gender']==1?'男':($v['gender']==2?'女':'');
            $data[$num]['stu_age'] = $v['age'];
            $data[$num]['stu_school'] = $v['school'];
            $data[$num]['stu_address'] = $v['address'];
            $data[$num]['stu_record_name'] = $v['record_name'];
            $data[$num]['stu_record_mobile'] = $v['record_mobile'];
            if($v['type']==1&&$v['status']==1){
                $data[$num]['stu_record_1'] = $v['content'];
            }else{
                $data[$num]['stu_record_1'] = '';
            }
            if($v['type']==1&&($v['status']==2 || $v['status']==3 || $v['status']==4)){
                $data[$num]['stu_record_2'] = $v['content'];
            }else{
                $data[$num]['stu_record_2'] = '';
            }
            if($v['type']==2){
                $data[$num]['stu_visit'] = $v['content'];
            }else{
                $data[$num]['stu_visit'] = '';
            }
            $num++;
        }
        include './App/Lib/Extend/Excel.php';  
        $excel = new Excel();
        $name = '咨询登记汇总表'.date('Ymd',$startTime)."-".date('Ymd',$endTime);
        if($this->_post("subType") == "导出"){
            $excel->xls($name, $title, $data);
        }else{
            $excel->html($name, $title, $data);
        }
    }
}

