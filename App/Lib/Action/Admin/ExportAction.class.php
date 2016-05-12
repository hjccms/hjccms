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
            $startDate = $post_start_date?$post_start_date:date("Y-m-01 00:00:00");
            $endDate = $post_end_date?$post_end_date:date("Y-m-d 23:59:59");
        }else{
            $startDate = date("Y-m-01 00:00:00");
            $endDate = date("Y-m-d 23:59:59");
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
            $objActSheet->getColumnDimension('D')->setWidth(20);//设置宽width
            
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
                    
                    //一级地区相同的合并start
                    if($parent_name == $v['parent_name']){
                        $end_num = $handle_row;
                    }else{
                        $parent_name = $v['parent_name'];
                        $num = $handle_row;
                        $end_num = null;
                    }
                    if($end_num){
                        $merage_arr[$num] = $end_num;
                        
                    }
                    //一级地区相同的合并end
                    
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
                
                //一级地区相同的合并start
                if($merage_arr){
                    foreach($merage_arr as $mk=>$mv){
                        $objActSheet->mergeCells("C{$mk}:C{$mv}");
                    }
                }
                //一级地区相同的合并end
                
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
                    
                    //一级地区相同的合并start
                    if($parent_name == $v['parent_name']){
                        $end_num = $handle_row;
                    }else{
                        $parent_name = $v['parent_name'];
                        $num = $handle_row;
                        $end_num = null;
                    }
                    if($end_num){
                        $merage_arr[$num] = $end_num;
                        
                    }
                    //一级地区相同的合并end
                    
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
                
                //一级地区相同的合并start
                if($merage_arr){
                    foreach($merage_arr as $mk=>$mv){
                        $objActSheet->mergeCells("C{$mk}:C{$mv}");
                    }
                }
                //一级地区相同的合并end
                
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
                    
                    //一级地区相同的合并start
                    if($parent_name == $v['parent_name']){
                        $end_num = $handle_row;
                    }else{
                        $parent_name = $v['parent_name'];
                        $num = $handle_row;
                        $end_num = null;
                    }
                    if($end_num){
                        $merage_arr[$num] = $end_num;
                        
                    }
                    //一级地区相同的合并end
                    
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
                
                //一级地区相同的合并start
                if($merage_arr){
                    foreach($merage_arr as $mk=>$mv){
                        $objActSheet->mergeCells("C{$mk}:C{$mv}");
                    }
                }
                //一级地区相同的合并end
                
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
            $outputFileName = "北外少儿教育市场活动计划监测表".date('Ymd',$startTime)."-".date('Ymd',$endTime);

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
            $startDate = $post_start_date?$post_start_date:date("Y-m-01 00:00:00");
            $endDate = $post_end_date?$post_end_date:date("Y-m-d 23:59:59");
        }else{
            $startDate = date("Y-m-01 00:00:00");
            $endDate = date("Y-m-d 23:59:59");
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
            $fun = get_record_status.$v['type'];
            $data[$num]['status'] = $fun($v['status']);
            $data[$num]['speed'] = $v['speed'];
            $data[$num]['add_name'] = $v['add_name'];
            $data[$num]['add_name2'] = $v['record_name'];
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
    
    //咨询登记统计表
    function recordCount(){
        load("@.form");
        
        $post_start_date = $this->_post('start_date');
        $post_end_date = $this->_post('end_date');
        $post_site_id = $this->_post('site_id');

        $site_id = $this->site_id == 1?($post_site_id?$post_site_id:$this->site_id):$this->site_id;
        
        if($post_start_date && $post_end_date){
            $startDate = $post_start_date?$post_start_date:date("Y-m-01 00:00:00");
            $endDate = $post_end_date?$post_end_date:date("Y-m-d 23:59:59");
        }else{
            $startDate = date("Y-m-01 00:00:00");
            $endDate = date("Y-m-d 23:59:59");
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
        $record = D('Student_record')->getRecord("type=1 and status in (1,2,3,4) and create_time>='{$startTime}' and create_time<='{$endTime}' and del is null","create_time desc");
        $student = D("Student")->getStudent("site_id={$site_id} and valid=1 and del is null");
        $questionnaire = D('Questionnaire')->getQuestionnaire("site_id={$site_id} and start_time>='{$startDate}' and end_time<='{$endDate}' and valid=1 and del is null");

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
        
        
        
        $channel = array(
            1=>'单页',
            2=>'有效调卷外呼',
            3=>'LED/招牌',
            4=>'推荐'
        );
        
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
            
            //设置宽width
            $objActSheet->getColumnDimension('A')->setWidth(10); 
            $objActSheet->getColumnDimension('B')->setWidth(17); 
            $objActSheet->getColumnDimension('C')->setWidth(17); 
            $objActSheet->getColumnDimension('D')->setWidth(8); 
            
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

            //设置单元格--表格头start
            $row = 1;
            $objActSheet->setCellValue('A'.$row, $startYear.'年'.$i.'月')->setCellValue('B'.$row, '')->setCellValue('C'.$row, '')->setCellValue('D'.$row, '')->setCellValue('E'.$row, '');           
            
            $row++;
            $objActSheet->setCellValue('A'.$row, '日咨询报名情况汇总表')->setCellValue('B'.$row, '')->setCellValue('C'.$row, '')->setCellValue('D'.$row, '')->setCellValue('E'.$row, '');
            
            $row++;
            $objActSheet->setCellValue('D'.$row, '合计');
            $objActSheet->mergeCells('A'.$row.':C'.$row);
             //设置单元格--表格头end
            
            //设置单元格--电话咨询start
            $row++;
            $tmp_row_1 = $row;
            $sign_row_dhzx_zj = $row;
            $objActSheet->setCellValue('C'.$row, '总计');
            
            $row++;
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $objActSheet->setCellValue("{$column}3", $j);
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $handle_row = $row;//开始操作行
                foreach($channel as $ck=>$cv){
                    $sign_row_dhzx[$ck] = $handle_row;
                    $day_number = null;
                    foreach($record as $rk=>$rv){
                        if($rv['type']==1 && $rv['status']==1 && $start<=$rv['create_time'] && $end>=$rv['create_time']){
                            if($ck==1 && $rv['channel']==1){
                                $day_number++;
                            }
                            if($ck==2 && $rv['channel']==6){
                                $day_number++;
                            }
                            if($ck==3 && $rv['channel']==3){
                                $day_number++;
                            }
                            if($ck==4 && ($rv['channel']==4 || $rv['channel'] == 5)){
                                $day_number++;
                            }
                        }
                    }
                    $objActSheet->setCellValue("C{$handle_row}", $cv);
                    $objActSheet->setCellValue("{$column}{$handle_row}", $day_number);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                $objActSheet->setCellValue("{$column}{$tmp_row_1}", "=SUM({$column}{$row}:{$column}{$handle_row})");//电话咨询－总计行
                
                if($j!=$end_day){
                    $column++;
                }
            }
            
            $tmp_row = $row;
            foreach($channel as $k=>$v){
                $objActSheet->setCellValue("D{$tmp_row}", "=SUM({$start_column}{$tmp_row}:{$column}{$tmp_row})");//电话咨询－合计列
                $tmp_row ++;
            }
            
            $objActSheet->setCellValue('D'.$tmp_row_1, "=SUM({$start_column}{$tmp_row_1}:{$column}{$tmp_row_1})");//电话咨询－总计－合计
            
            if($handle_row>$row){
                $objActSheet->setCellValue("A4", '电话咨询');
                $objActSheet->mergeCells("A4:B{$handle_row}");
            }
            
            $objActSheet->mergeCells('A1:'.$column.'1')->mergeCells('A2:'.$column.'2');//合并单元格 
            
            $row = $handle_row;
            //设置单元格--电话咨询end
            
            
            //设置单元格--首次上门start
            $row++;
            $tmp_row_1 = $row;
            $sign_row_scsm = $row;
            $objActSheet->setCellValue('B'.$row, '总计');
            $objActSheet->mergeCells("B{$row}:C{$row}");
            
            $row++;
            $tmp_row_2 = $row;
            $sign_row_dhsmzx_hj = $row;
            $objActSheet->setCellValue('C'.$row, '合计');
            
            $row++;
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $handle_row = $row;//开始操作行
                foreach($channel as $ck=>$cv){
                    $sign_row_dhsmzx[$ck] = $handle_row;
                    $day_number = null;
                    foreach($record as $rk=>$rv){
                        if($rv['type']==1 && $rv['status']==2 && $start<=$rv['create_time'] && $end>=$rv['create_time']){
                            if($ck==1 && $rv['channel']==1){
                                $day_number++;
                            }
                            if($ck==2 && $rv['channel']==6){
                                $day_number++;
                            }
                            if($ck==3 && $rv['channel']==3){
                                $day_number++;
                            }
                            if($ck==4 && ($rv['channel']==4 || $rv['channel'] == 5)){
                                $day_number++;
                            }
                        }
                    }
                    $objActSheet->setCellValue("C{$handle_row}", $cv);
                    $objActSheet->setCellValue("{$column}{$handle_row}", $day_number);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                $objActSheet->setCellValue("{$column}{$tmp_row_2}", "=SUM({$column}{$row}:{$column}{$handle_row})");//电话上门咨询－合计行
                
                if($j!=$end_day){
                    $column++;
                }
            }
            
            $tmp_row = $row;
            foreach($channel as $k=>$v){
                $objActSheet->setCellValue("D{$tmp_row}", "=SUM({$start_column}{$tmp_row}:{$column}{$tmp_row})");//电话上门咨询－合计列
                $tmp_row ++;
            }
            
            $objActSheet->setCellValue('D'.$tmp_row_2, "=SUM({$start_column}{$tmp_row_2}:{$column}{$tmp_row_2})");//电话上门咨询－合计行－合计列
            
            if($handle_row>$row){
                $objActSheet->setCellValue("B{$tmp_row_2}", '电话上门咨询');
                $objActSheet->mergeCells("B{$tmp_row_2}:B{$handle_row}");
            }
            
            $row = $handle_row;
            
            $row++;
            $tmp_row_3 = $row;
            $sign_row_zjsmzx_hj = $row;
            $objActSheet->setCellValue('C'.$row, '总计');
            
            $row++;
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $handle_row = $row;//开始操作行
                foreach($channel as $ck=>$cv){
                    $sign_row_zjsmzx[$ck] = $handle_row;
                    $day_number = null;
                    foreach($record as $rk=>$rv){
                        if($rv['type']==1 && $rv['status']==3 && $start<=$rv['create_time'] && $end>=$rv['create_time']){
                            if($ck==1 && $rv['channel']==1){
                                $day_number++;
                            }
                            if($ck==2 && $rv['channel']==6){
                                $day_number++;
                            }
                            if($ck==3 && $rv['channel']==3){
                                $day_number++;
                            }
                            if($ck==4 && ($rv['channel']==4 || $rv['channel'] == 5)){
                                $day_number++;
                            }
                        }
                    }
                    $objActSheet->setCellValue("C{$handle_row}", $cv);
                    $objActSheet->setCellValue("{$column}{$handle_row}", $day_number);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                
                $objActSheet->setCellValue("{$column}{$tmp_row_3}", "=SUM({$column}{$row}:{$column}{$handle_row})");//直接上门咨询－合计行
                
                if($j!=$end_day){
                    $column++;
                }
            }
            
            $tmp_row = $row;
            foreach($channel as $k=>$v){
                $objActSheet->setCellValue("D{$tmp_row}", "=SUM({$start_column}{$tmp_row}:{$column}{$tmp_row})");//直接上门咨询－合计列
                $tmp_row ++;
            }
            
            
            $objActSheet->setCellValue('D'.$tmp_row_3, "=SUM({$start_column}{$tmp_row_3}:{$column}{$tmp_row_3})");//直接上门咨询－合计行－合计列
            
            
            if($handle_row>$row){
                $objActSheet->setCellValue("B{$tmp_row_3}", '直接上门咨询');
                $objActSheet->mergeCells("B{$tmp_row_3}:B{$handle_row}");
            }
            
            $objActSheet->setCellValue("A{$tmp_row_1}", '首次上门');
            $objActSheet->mergeCells("A{$tmp_row_1}:A{$handle_row}");
            
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $objActSheet->setCellValue("{$column}{$tmp_row_1}", "=SUM({$column}{$tmp_row_2},{$column}{$tmp_row_3})");//首次上门－总计行
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue("D{$tmp_row_1}", "=SUM(D{$tmp_row_2},D{$tmp_row_3})");//首次上门－总计行-总计列
            
            $row = $handle_row;
            
            //设置单元格--首次上门end
            
            //设置单元格--再次上门start
            $row++;
            $tmp_row_1 = $row;
            $sign_row_zcsm = $row;
            $objActSheet->setCellValue('C'.$row, '总计');
            
            $row++;
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $handle_row = $row;//开始操作行
                foreach($channel as $ck=>$cv){
                    $day_number = null;
                    foreach($record as $rk=>$rv){
                        if($rv['type']==1 && $rv['status']==4 && $start<=$rv['create_time'] && $end>=$rv['create_time']){
                            if($ck==1 && $rv['channel']==1){
                                $day_number++;
                            }
                            if($ck==2 && $rv['channel']==6){
                                $day_number++;
                            }
                            if($ck==3 && $rv['channel']==3){
                                $day_number++;
                            }
                            if($ck==4 && ($rv['channel']==4 || $rv['channel'] == 5)){
                                $day_number++;
                            }
                        }
                    }
                    $objActSheet->setCellValue("C{$handle_row}", $cv);
                    $objActSheet->setCellValue("{$column}{$handle_row}", $day_number);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                $objActSheet->setCellValue("{$column}{$tmp_row_1}", "=SUM({$column}{$row}:{$column}{$handle_row})");//再次上门－合计行
                
                if($j!=$end_day){
                    $column++;
                }
            }
            
            $tmp_row = $row;
            foreach($channel as $k=>$v){
                $objActSheet->setCellValue("D{$tmp_row}", "=SUM({$start_column}{$tmp_row}:{$column}{$tmp_row})");//再次上门－合计列
                $tmp_row ++;
            }
            
            $objActSheet->setCellValue('D'.$tmp_row_1, "=SUM({$start_column}{$tmp_row_1}:{$column}{$tmp_row_1})");//再次上门－合计行－合计列
            
            if($handle_row>$row){
                $objActSheet->setCellValue("A{$tmp_row_1}", '再次上门');
                $objActSheet->mergeCells("A{$tmp_row_1}:B{$handle_row}");
            }
                        
            $row = $handle_row;
            //设置单元格--再次上门end
            
            
            //设置单元格--报名人数start
            $row++;
            $tmp_row_1 = $row;
            $sign_row_bmrs_zj = $row;
            $objActSheet->setCellValue('B'.$row, '总计');
            $objActSheet->mergeCells("B{$row}:C{$row}");
            
            $row++;
            $tmp_row_2 = $row;
            $sign_row_scsmbm_hj = $row;
            $objActSheet->setCellValue('C'.$row, '合计');
            
            $row++;
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $handle_row = $row;//开始操作行
                foreach($channel as $ck=>$cv){
                    $sign_row_scsmbm[$ck] = $handle_row;
                    $day_number = null;
                    foreach($record as $rk=>$rv){
                        if($rv['type']==1 && $rv['status']==5 && $start<=$rv['create_time'] && $end>=$rv['create_time']){
                            if($ck==1 && $rv['channel']==1){
                                $day_number++;
                            }
                            if($ck==2 && $rv['channel']==6){
                                $day_number++;
                            }
                            if($ck==3 && $rv['channel']==3){
                                $day_number++;
                            }
                            if($ck==4 && ($rv['channel']==4 || $rv['channel'] == 5)){
                                $day_number++;
                            }
                        }
                    }
                    $objActSheet->setCellValue("C{$handle_row}", $cv);
                    $objActSheet->setCellValue("{$column}{$handle_row}", $day_number);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                $objActSheet->setCellValue("{$column}{$tmp_row_2}", "=SUM({$column}{$row}:{$column}{$handle_row})");//首次上门报名－合计行
                
                if($j!=$end_day){
                    $column++;
                }
            }
            
            $tmp_row = $row;
            foreach($channel as $k=>$v){
                $objActSheet->setCellValue("D{$tmp_row}", "=SUM({$start_column}{$tmp_row}:{$column}{$tmp_row})");//首次上门报名－合计列
                $tmp_row ++;
            }
            
            $objActSheet->setCellValue('D'.$tmp_row_2, "=SUM({$start_column}{$tmp_row_2}:{$column}{$tmp_row_2})");//首次上门报名－合计行－合计列
            
            if($handle_row>$row){
                $objActSheet->setCellValue("B{$tmp_row_2}", '首次上门报名');
                $objActSheet->mergeCells("B{$tmp_row_2}:B{$handle_row}");
            }
            
            $row = $handle_row;
            
            $row++;
            $tmp_row_3 = $row;
            $sign_row_zcsmbm_hj = $row;
            $objActSheet->setCellValue('C'.$row, '合计');
            
            $row++;
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $handle_row = $row;//开始操作行
                foreach($channel as $ck=>$cv){
                    $sign_row_zcsmbm[$ck] = $handle_row;
                    $day_number = null;
                    foreach($record as $rk=>$rv){
                        if($rv['type']==1 && $rv['status']==6 && $start<=$rv['create_time'] && $end>=$rv['create_time']){
                            if($ck==1 && $rv['channel']==1){
                                $day_number++;
                            }
                            if($ck==2 && $rv['channel']==6){
                                $day_number++;
                            }
                            if($ck==3 && $rv['channel']==3){
                                $day_number++;
                            }
                            if($ck==4 && ($rv['channel']==4 || $rv['channel'] == 5)){
                                $day_number++;
                            }
                        }
                    }
                    $objActSheet->setCellValue("C{$handle_row}", $cv);
                    $objActSheet->setCellValue("{$column}{$handle_row}", $day_number);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                
                $objActSheet->setCellValue("{$column}{$tmp_row_3}", "=SUM({$column}{$row}:{$column}{$handle_row})");//再次上门报名－合计行
                
                if($j!=$end_day){
                    $column++;
                }
            }
            
            $tmp_row = $row;
            foreach($channel as $k=>$v){
                $objActSheet->setCellValue("D{$tmp_row}", "=SUM({$start_column}{$tmp_row}:{$column}{$tmp_row})");//再次上门报名－合计列
                $tmp_row ++;
            }
            
            
            $objActSheet->setCellValue('D'.$tmp_row_3, "=SUM({$start_column}{$tmp_row_3}:{$column}{$tmp_row_3})");//再次上门报名－合计行－合计列
            
            
            if($handle_row>$row){
                $objActSheet->setCellValue("B{$tmp_row_3}", '再次上门报名');
                $objActSheet->mergeCells("B{$tmp_row_3}:B{$handle_row}");
            }
            
            $objActSheet->setCellValue("A{$tmp_row_1}", '报名人数');
            $objActSheet->mergeCells("A{$tmp_row_1}:A{$handle_row}");
            
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $objActSheet->setCellValue("{$column}{$tmp_row_1}", "=SUM({$column}{$tmp_row_2},{$column}{$tmp_row_3})");//首次上门－总计行
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue("D{$tmp_row_1}", "=SUM(D{$tmp_row_2},D{$tmp_row_3})");//首次上门－总计行
            
            $row = $handle_row;
            //设置单元格--首次上门end
            
            //设置单元格--当日获取调卷量start
            $row++;
            $sign_row_drdjsl = $row;
            $objActSheet->setCellValue("A{$row}","当日获取调卷量");
            $objActSheet->mergeCells("A{$row}:C{$row}");
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $start = strtotime($startYear.'-'.$i.'-'.$j.' 00:00:00');//当天开始时间
                $end = strtotime($startYear.'-'.$i.'-'.$j.' 23:59:59');//当天结束时间
                $day_number = null;//每日实际调卷总计
                foreach($questionnaire as $qk=>$qv){
                        if($start<=strtotime($qv['start_time']) && $end>=strtotime($qv['end_time'])){
                            $day_number = $day_number+$qv['number'];
                        }
                    }
                $objActSheet->setCellValue("{$column}{$row}", $day_number);
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue("D{$row}", "=SUM({$start_column}{$row}:{$column}{$row})");//当日获取调卷量－合计行
            //设置单元格--当日获取调卷量end
            
            //设置单元格--调卷有效率start
            $row++;
            $sign_row_dj = $row;
            $objActSheet->setCellValue("A{$row}","调卷有效率");
            $objActSheet->mergeCells("A{$row}:C{$row}");
            //设置单元格--调卷有效率end
            
            
            //设置单元格--咨询总数start
            $row++;
            $tmp_row_1 = $row;
            $sign_row_zxzs_zj = $row;
            $objActSheet->setCellValue('C'.$row, '总计');
            
            $row++;
            $column = "D";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day+1;$j++){
                $handle_row = $row;//开始操作行
                foreach($channel as $ck=>$cv){
                    $sign_row_zxzs[$ck] = $handle_row;
                    $objActSheet->setCellValue("{$column}{$handle_row}", "=SUM({$column}".$sign_row_dhzx[$ck].",{$column}".$sign_row_zjsmzx[$ck].")");
                    $objActSheet->setCellValue("C{$handle_row}", $cv);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                $objActSheet->setCellValue("{$column}{$tmp_row_1}", "=SUM({$column}{$sign_row_dhzx_zj},{$column}{$sign_row_zjsmzx_hj})");//咨询总数－总计行
                
                if($j!=$end_day+1){
                    $column++;
                }
            }
                
            if($handle_row>$row){
                $objActSheet->setCellValue("A{$tmp_row_1}", '咨询总数');
                $objActSheet->mergeCells("A{$tmp_row_1}:B{$handle_row}");
            }                        
            $row = $handle_row;
            //设置单元格--咨询总数end
            
            
            //设置单元格--调卷有效率start
            $column = "D";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day+1;$j++){
                $objActSheet->setCellValue("{$column}{$sign_row_dj}", "={$column}{$sign_row_zxzs[2]}/{$column}{$sign_row_drdjsl}");//参加试听人数－行
                if($j!=$end_day+1){
                    $column++;
                }
            }
            //设置单元格--调卷有效率end
            
            //设置单元格--报名人数start
            $row++;
            $tmp_row_1 = $row;
            $sign_row_bmrs_zj_2 = $row;
            $objActSheet->setCellValue('C'.$row, '总计');
            
            $row++;
            $column = "D";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day+1;$j++){
                $handle_row = $row;//开始操作行
                foreach($channel as $ck=>$cv){
                    $sign_row_bmrs2[$ck] = $handle_row;
                    $objActSheet->setCellValue("{$column}{$handle_row}", "=SUM({$column}".$sign_row_scsmbm[$ck].",{$column}".$sign_row_zcsmbm[$ck].")");
                    $objActSheet->setCellValue("C{$handle_row}", $cv);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                $objActSheet->setCellValue("{$column}{$tmp_row_1}", "={$column}$sign_row_bmrs_zj");//报名人数－总计行
                
                if($j!=$end_day+1){
                    $column++;
                }
            }
                
            if($handle_row>$row){
                $objActSheet->setCellValue("A{$tmp_row_1}", '报名人数');
                $objActSheet->mergeCells("A{$tmp_row_1}:B{$handle_row}");
            }
            
            $row = $handle_row;
            //设置单元格--报名人数end

            
            //设置单元格--示范课start
            $row++;
            $tmp_row_1 = $row;
            $sign_row_cjst = $row;
            $objActSheet->setCellValue('C'.$row, '参加试听人数');
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $objActSheet->setCellValue("{$column}{$row}", "");//参加试听人数－行
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue("D{$row}", "=SUM({$start_column}{$row}:{$column}{$row})");//参加试听人数－总计
            
            $row++;
            $tmp_row_2 = $row;
            $sign_row_stbm = $row;
            $objActSheet->setCellValue('C'.$row, '试听报名人数');
            $column = "E";//开始操作列
            $start_column = $column;
            for($j=$start_day;$j<=$end_day;$j++){
                $objActSheet->setCellValue("{$column}{$row}", "");//试听报名人数－行
                if($j!=$end_day){
                    $column++;
                }
            }
            $objActSheet->setCellValue("D{$row}", "=SUM({$start_column}{$row}:{$column}{$row})");//试听报名人数－总计
            
            
            $objActSheet->setCellValue('A'.$tmp_row_1, '示范课');
            $objActSheet->mergeCells("A{$tmp_row_1}:B{$tmp_row_2}");
            //设置单元格--示范课end
            
            //设置单元格--转化率start
            $row++;
            $tmp_row_1 = $row;
            $sign_row_zhl = $row;
            $objActSheet->setCellValue('D'.$row, '电话/上门转化率')->setCellValue('E'.$row, '')->setCellValue('F'.$row, '');
            $objActSheet->setCellValue('G'.$row, '当面/报名转化率')->setCellValue('H'.$row, '')->setCellValue('I'.$row, '');
            $objActSheet->setCellValue('J'.$row, '总转化率')->setCellValue('K'.$row, '')->setCellValue('L'.$row, '');
            $objActSheet->setCellValue('M'.$row, '示范课转化率')->setCellValue('N'.$row, '');
            
            $row++;
            $tmp_row_2 = $row;
            $objActSheet->setCellValue('C'.$row, '总计');
            
            $row++;
            $column = "E";//开始操作列
            $start_column = $column;
            $tmp = false;
            for($j=$start_day;$j<=$end_day;$j++){
                if($column == 'N'){
                    $tmp = true;
                }
                $handle_row = $row;//开始操作行
                foreach($channel as $ck=>$cv){
                    $objActSheet->setCellValue("C{$handle_row}", $cv);
                    $handle_row++;
                }
                if($handle_row>$row){
                    $handle_row--;
                }
                
                if($j!=$end_day){
                    $column++;
                }
            }
            
            //转化率
            $objActSheet->setCellValue("D{$tmp_row_2}", "=D{$sign_row_dhsmzx_hj}/D{$sign_row_dhzx_zj}");//电话/上门转化率
            $objActSheet->setCellValue("G{$tmp_row_2}", "=D{$sign_row_bmrs_zj_2}/SUM(D{$sign_row_dhsmzx_hj},D{$sign_row_zjsmzx_hj})");//当面/报名转化率
            $objActSheet->setCellValue("J{$tmp_row_2}", "=D{$sign_row_bmrs_zj}/D{$sign_row_zxzs_zj}");//总转化率
            $objActSheet->setCellValue("M{$tmp_row_2}", "=D{$sign_row_stbm}/D{$sign_row_cjst}");//示范课转化率
            $tmp_row = $row;
            foreach($channel as $ck=>$cv){
                $objActSheet->mergeCells("D{$tmp_row}:F{$tmp_row}");
                $objActSheet->mergeCells("G{$tmp_row}:I{$tmp_row}");
                $objActSheet->mergeCells("J{$tmp_row}:L{$tmp_row}");
                $objActSheet->setCellValue("D{$tmp_row}", "=D".$sign_row_dhsmzx[$ck]."/D".$sign_row_dhzx[$ck]);//电话/上门转化率
                $objActSheet->setCellValue("G{$tmp_row}", "=D".$sign_row_bmrs2[$ck]."/SUM(D".$sign_row_dhsmzx[$ck].",D".$sign_row_zjsmzx[$ck].")");//当面/报名转化率
                $objActSheet->setCellValue("J{$tmp_row}", "=(SUM(D". $sign_row_scsmbm[$ck] .",D".$sign_row_zcsmbm[$ck] ."))/".$sign_row_zxzs[$ck]);//总转化率
                $tmp_row ++;
            }            
            
            if($handle_row>$row){
                $objActSheet->setCellValue("A{$tmp_row_1}", '转化率');
                $objActSheet->mergeCells("A{$tmp_row_1}:B{$handle_row}");
            }
            
            $objActSheet->mergeCells("D{$tmp_row_1}:F{$tmp_row_1}");
            $objActSheet->mergeCells("D{$tmp_row_2}:F{$tmp_row_2}");
            $objActSheet->mergeCells("G{$tmp_row_1}:I{$tmp_row_1}");
            $objActSheet->mergeCells("G{$tmp_row_2}:I{$tmp_row_2}");
            $objActSheet->mergeCells("J{$tmp_row_1}:L{$tmp_row_1}");
            $objActSheet->mergeCells("J{$tmp_row_2}:L{$tmp_row_2}");
            $objActSheet->mergeCells("M{$tmp_row_1}:N{$tmp_row_1}");
            $objActSheet->mergeCells("M{$tmp_row_2}:N{$handle_row}");
            
            $row = $handle_row;
            //设置单元格--转化率end
            
            
            $column = $tmp?$column:'N';//这个表比较特殊
            //从指定的单元格复制样式信息. 
            $objActSheet->duplicateStyle($objActSheet->getStyle('A1'), 'A1:'.$column.$row); 
            
            //设置填充的样式和背景色
            $objActSheet->getStyle("A1:{$column}{$row}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $flag = false;
            $column = "F";
            for($j=$start_day;$j<=$end_day;$j++){
                if($column == 'N'){
                    $flag = true;
                }
                if(date("w",strtotime($startYear.'-'.$i.'-'.$j)) == '6'){
                    $objActSheet->getStyle("{$column}3:{$column}{$sign_row_dj}")->getFill()->getStartColor()->setRGB('89bfff');
                }
                if(date("w",strtotime($startYear.'-'.$i.'-'.$j)) == '0'){
                    $objActSheet->getStyle("{$column}3:{$column}{$sign_row_dj}")->getFill()->getStartColor()->setRGB('c2ffff');
                }
                
                if($j!=$end_day){
                    $column++;
                }
            }
            $column = $flag?$column:'N';
            $objActSheet->getStyle("A{$sign_row_dhzx_zj}:C{$sign_row_dj}")->getFill()->getStartColor()->setRGB('ffff88');
            $objActSheet->getStyle("D{$sign_row_dhzx_zj}:D{$sign_row_dj}")->getFill()->getStartColor()->setRGB('b3b3b3');
            $objActSheet->getStyle("D{$sign_row_dhsmzx_hj}:{$column}{$sign_row_dhsmzx_hj}")->getFill()->getStartColor()->setRGB('b3b3b3');
            $objActSheet->getStyle("D{$sign_row_zjsmzx_hj}:{$column}{$sign_row_zjsmzx_hj}")->getFill()->getStartColor()->setRGB('b3b3b3');
            $objActSheet->getStyle("D{$sign_row_zcsm}:{$column}{$sign_row_zcsm}")->getFill()->getStartColor()->setRGB('b3b3b3');
            $objActSheet->getStyle("D{$sign_row_scsmbm_hj}:{$column}{$sign_row_scsmbm_hj}")->getFill()->getStartColor()->setRGB('b3b3b3');
            $objActSheet->getStyle("D{$sign_row_zcsmbm_hj}:{$column}{$sign_row_zcsmbm_hj}")->getFill()->getStartColor()->setRGB('b3b3b3');
            
            $objActSheet->getStyle("D{$sign_row_dhzx_zj}:{$column}{$sign_row_dhzx_zj}")->getFill()->getStartColor()->setRGB('535187');
            $objActSheet->getStyle("D{$sign_row_scsm}:{$column}{$sign_row_scsm}")->getFill()->getStartColor()->setRGB('535187');
            $objActSheet->getStyle("D{$sign_row_bmrs_zj}:{$column}{$sign_row_bmrs_zj}")->getFill()->getStartColor()->setRGB('535187');
            
            $objActSheet->getStyle("D{$sign_row_dj}:{$column}{$sign_row_dj}")->getFill()->getStartColor()->setRGB('ec0071');
            
            $objActSheet->getStyle("A{$sign_row_zxzs_zj}:{$column}".($sign_row_cjst-1))->getFill()->getStartColor()->setRGB('535187');
            $objActSheet->getStyle("A{$sign_row_cjst}:D{$sign_row_stbm}")->getFill()->getStartColor()->setRGB('535187');
            $objActSheet->getStyle("A{$sign_row_zhl}:C{$row}")->getFill()->getStartColor()->setRGB('535187');
            $objActSheet->getStyle("D{$sign_row_zhl}:N{$sign_row_zhl}")->getFill()->getStartColor()->setRGB('18c1fe');
            
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
}

