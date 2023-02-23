<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Url;

class Cluescount extends Admin
{
    public function index()
    {
        parent::userauth2(324);

        $tree_list = $this->gettreelist(1);
        $this->assign('tree_list', json_encode($tree_list));

        return $this->fetch();
    }

    public function getTreelist($parent_id)
    {
        $tree_list = array();

        $region = new \app\common\model\Region;
        $where['parent_id'] = $parent_id;
//        $where['region_id'] = array("neq",3876);

        $clues_list = $region->where($where)->order("sort ASC")->column('');
        foreach ($clues_list as $k => $v) {
            $flag = false;
            if ($v['region_type'] == 1) {
                $flag = true;
            }
            $clues_children = array(
                'id' => $v['region_id'],
                'title' => $v['region_name'],
                'spread' => $flag,
                'children' => $this->getTreelist($v['region_id']),
            );
            if (!$parent_id) {
                $tree_list[$parent_id]['children'][] = $clues_children;
            } else {
                $tree_list[] = $clues_children;
            }
        }
        return $tree_list;
    }

    public function getCluescount()
    {
        parent::userauth2(324);

        $department = new \app\common\model\Department;
        $news = new \app\common\model\News;
        $news_choose_record = new \app\common\model\Newschooserecord;
        $region = new \app\common\model\Region;
        $where = $where_d = array();
        $region_id = input('request.region_id');
        $limit = input('request.limit');
        $publish_time1 = input('request.publish_time1');
        $publish_time2 = input('request.publish_time2');

        //获取地区信息
        $where['region_id'] = $region_id;
        $region_info = $region->where($where)->find();
        //判断 $region_type 的值  1 province  2 city 3 district
        if ($region_info['region_type'] == "1") {
            $where_d['province'] = $region_id;
        } elseif ($region_info['region_type'] == "2") {
            $where_d['city'] = $region_id;
        } elseif ($region_info['region_type'] == "3") {
            $where_d['district'] = $region_id;
        }
        $list = $department->where($where_d)->paginate($limit);
        foreach ($list as $k => $v) {
            $list[$k]['province'] = $region->where(["region_id"=>$v['province']])->value("region_name");
            $list[$k]['city'] = $region->where(["region_id"=>$v['city']])->value("region_name");
            $list[$k]['district'] = $region->where(["region_id"=>$v['district']])->value("region_name");
            $list[$k]['region_name'] = $list[$k]['province']
                . $list[$k]['city'] . $list[$k]['district'];
            $cn_where = $cn_where1 = array();
            $cn_where['department_id'] = $v['department_id'];
            $cn_where['status'] = 10;
            if ($publish_time1 != "") {
                $cn_where['publish_time'] = array(">=", $publish_time1);
            }
            if ($publish_time2 != "") {
                $cn_where1['publish_time'] = array("<=", $publish_time2);
            }
            $list[$k]['clues_num'] = $news->where($cn_where)->where($cn_where1)->count();//发布线索数
            $list[$k]['choose_news_id'] = $news->where(["department_id"=>$v['department_id']])->column('news_id');
//            a($list[$k]['choose_news_id']);
            //被选用线索总数
            $param = array();
            $param['department_id'] = $v['department_id'];
            $list[$k]['choose_num'] = $news->getChooseNum($param);
            //被选用线索总次数
            $param = array();
            $param['department_id'] = $v['department_id'];
            $list[$k]['total_times'] = $news->getChooseTimes($param);
            //占比
            if ($list[$k]['clues_num'] != 0) {
                $list[$k]['ratio'] = round($list[$k]['choose_num'] / $list[$k]['clues_num'] * 100, 2) . "%";
            } else {
                $list[$k]['ratio'] = "-";
            }
            $list[$k]['publish_time'] = $news->where(["department_id"=>$v['department_id']])->value('publish_time');

        }
        $department_list = $list->toArray();
        $result = array("code" => 0, "count" => $department_list['total'], "data" => $department_list['data']);
        echo json_encode($result);
        exit;
    }

    public function city()
    {
        parent::userauth2(324);

        $tree_list2 = $this->gettreelist2(1);
        $this->assign('tree_list2', json_encode($tree_list2));

        return $this->fetch();
    }

    public function getTreelist2($parent_id)
    {
        $tree_list2 = array();

        $region = new \app\common\model\Region;
        $where['parent_id'] = $parent_id;

        $clues_list = $region->where($where)->order("sort ASC")->column('');
        foreach ($clues_list as $k => $v) {
            $flag = false;
            if ($v['region_type'] == 1) {
                $flag = true;
            }
            if ($v['region_type'] > 2) {
               return ;
            }

            $clues_children = array(
                'id' => $v['region_id'],
                'title' => $v['region_name'],
                'spread' => $flag,
                'children' => $this->getTreelist2($v['region_id']),
            );


            if (!$parent_id) {
                $tree_list2[$parent_id]['children'][] = $clues_children;
            } else {
            $tree_list2[] = $clues_children;
            }
        }
        return $tree_list2;
    }
//
    public function getCluescountcity()
    {
//        parent::userauth2(324);
        $news = new \app\common\model\News;
        $news_choose_record = new \app\common\model\Newschooserecord;
        $region = new \app\common\model\Region;
        $where = $where_d = array();
        $region_id = input('request.region_id');
        $limit = input('request.limit');
        //获取地区信息
        $where['region_id'] = array("not in",array("313","314","315","316","317","318","319","320","321","322","323","324","325","326","3876"));
        if($region_id!=''){
            if($region_id == 3878){
                //省级
                $where['region_id'] = $region_id;
            }elseif($region_id == 3893){
                //兰州新区
                $where['region_id'] = $region_id;
            }else{
                //其他市州
                $where['parent_id'] = $region_id;
            }
        }
        $list = $region->where($where)->order('sort asc')->paginate($limit);
        $district_list = $list->toArray();
            foreach ($district_list['data'] as $k=>$v){
                $where_news = array();
                $where_news['status'] = 10;
                $c=0;
                if($v['region_id'] == 3878 || $v['region_id'] == 3898){
                    //兰州新区
                    //省级
                    $where_news['city'] = $v['region_id'];
                }else{
                    //其他市州
                    $where_news['district'] = $v['region_id'];
                }
                //共同的代码
                $news_info = $news->where($where_news)->column("");
                $counts = $news->where($where_news)->count();
                foreach ($news_info as $key=>$value){
                    $aaa = $news_choose_record->where(["news_id"=>$value['news_id']])->count();
                    $c += $aaa;
                }
                $district_list['data'][$k]['clues_num'] = $counts;
                $district_list['data'][$k]['choose_num'] = $c;
                if ($district_list['data'][$k]['clues_num'] != 0) {
                    $district_list['data'][$k]['ratio'] = round($district_list['data'][$k]['choose_num'] / $district_list['data'][$k]['clues_num'] * 100, 2) . "%";
                } else {
                    $district_list['data'][$k]['ratio'] = "-";
                }
                $district_list['data'][$k]['city'] = $region->where(["region_id"=>$v['parent_id']])->value("region_name");
                $district_list['data'][$k]['region_name'] = $district_list['data'][$k]['city'] . "--".$district_list['data'][$k]['region_name'];
            }

        $result = array("code" => 0, "count" => $district_list['total'], "data" => $district_list['data'],"city_id"=>$region_id);
        echo json_encode($result);
        exit;
    }

        public function city_export()
        {
            \think\Loader::import('PHPExcel.PHPExcel');
            \think\Loader::import('PHPExcel.PHPExcel.IOFactory');
            $PHPExcel = new \PHPExcel();
            $PHPReader = new \PHPExcel_Reader_Excel2007();
            /*数据查询,与列表页保持一致，开始*/
            $news = new \app\common\model\News;
            $news_choose_record = new \app\common\model\Newschooserecord;
            $region = new \app\common\model\Region;
            $where = $where_d = array();
            $region_id = input('request.region_id');
            $limit = input('request.limit');
            //获取地区信息
            $where['region_id'] = array("not in",array("313","314","315","316","317","318","319","320","321","322","323","324","325","326","3876"));
            if($region_id!=''){
                if($region_id == 3878){
                    //省级
                    $where['region_id'] = $region_id;
                }elseif($region_id == 3893){
                    //兰州新区
                    $where['region_id'] = $region_id;
                }else{
                    //其他市州
                    $where['parent_id'] = $region_id;
                }
            }
            $list = $region->where($where)->order('sort asc')->paginate(99999);
            $district_list = $list->toArray();
            foreach ($district_list['data'] as $k=>$v){
                $where_news = array();
                $where_news['status'] = 10;
                $c=0;
                if($v['region_id'] == 3878 || $v['region_id'] == 3898){
                    //兰州新区
                    //省级
                    $where_news['city'] = $v['region_id'];
                }else{
                    //其他市州
                    $where_news['district'] = $v['region_id'];
                }
                //共同的代码
                $news_info = $news->where($where_news)->column("");
                $counts = $news->where($where_news)->count();
                foreach ($news_info as $key=>$value){
                    $aaa = $news_choose_record->where(["news_id"=>$value['news_id']])->count();
                    $c += $aaa;
                }
                $district_list['data'][$k]['clues_num'] = $counts;
                $district_list['data'][$k]['choose_num'] = $c;
                if ($district_list['data'][$k]['clues_num'] != 0) {
                    $district_list['data'][$k]['ratio'] = round($district_list['data'][$k]['choose_num'] / $district_list['data'][$k]['clues_num'] * 100, 2) . "%";
                } else {
                    $district_list['data'][$k]['ratio'] = "-";
                }
                $district_list['data'][$k]['city'] = $region->where(["region_id"=>$v['parent_id']])->value("region_name");
                $district_list['data'][$k]['region_name'] = $district_list['data'][$k]['city'] . "--".$district_list['data'][$k]['region_name'];
            }
            /*数据查询,与列表页保持一致，结束*/
            $save_filename = "市州统计信息导出";
            $objPHPExcel = new \PHPExcel();
            // Set properties
            $objPHPExcel->getProperties()->setCreator('www');
            $objPHPExcel->getProperties()->setLastModifiedBy('www');
            $objPHPExcel->getProperties()->setTitle('www');
            $objPHPExcel->getProperties()->setSubject('www');
            $objPHPExcel->getProperties()->setDescription('www' . $save_filename);
            $objPHPExcel->getProperties()->setKeywords('www' . $save_filename);
            $objPHPExcel->getProperties()->setCategory('活动信息');
            // Add some data
            $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setBold(true);      //第一行是否加粗
            $objPHPExcel->getActiveSheet(0)->getStyle('A2:D2')->getFont()->setBold(true);      //第二行是否加粗
            $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setSize(18);         //第一行字体大小
            // 设置垂直居中
            $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            // 设置水平居中
            $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet(0)->getRowDimension('1')->setRowHeight(38);    //第一行行高
            $objPHPExcel->getActiveSheet(0)->mergeCells('A1:D1');

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "市州线索统计信息");
            $ki = 2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $ki, '县区名称')
                ->setCellValue('B' . $ki, '发布线索数')
                ->setCellValue('C' . $ki, '被选用总数')
                ->setCellValue('D' . $ki, '被选用比例');
            $sheet = $objPHPExcel->getActiveSheet();
            $sheet->getColumnDimension('A')->setWidth(35);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet(0)->getStyle("Q")->getAlignment()->setWrapText(TRUE);

            foreach ($district_list['data'] as $k => $v) {
                $i = $k + 3;
                $sheet->setCellValueExplicit("A{$i}", $v['region_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit("B{$i}", $v['clues_num'], \PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit("C{$i}", $v['choose_num'], \PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit("D{$i}", $v['ratio'], \PHPExcel_Cell_DataType::TYPE_STRING);
            }

            $styleThinBlackBorderOutline = array(
                'borders' => array(
                    'allborders' => array( //设置全部边框
                        'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                    ),
                ),
            );
            $objPHPExcel->getActiveSheet(0)->getStyle("A1:D{$i}")->applyFromArray($styleThinBlackBorderOutline);
            // Add title
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setTitle(str_replace('www_', '', $save_filename));
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);

            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . iconv("UTF-8", "GBK", $save_filename) . '.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save("php://output");
            exit;
        }

}

