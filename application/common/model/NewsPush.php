<?php
namespace app\common\model;
use think\Db;
class NewsPush extends \think\Model
{    
	/**
	* 获取已推送线索
	* @param string        keywords 关键词查询（模糊匹配标题）
	* @param dateTime  	   publish_start_time 发布时间-开始
	* @param dateTime  	   publish_end_time 发布时间-结束
	* @param integer  	   department_id 单位id
	* @param integer  	   news_id 线索id
	* @param integer  	   province 省id
	* @param integer  	   city 城市id
	* @param integer  	   district 县id
	* @param integer       list_rows 每页条数
	* @return array        格式：array("total"=>xx,"data"=>$push_list)
	* 调用方式举例:$news = news();$news->getChooseList($param);
	*/
	public function getPushNewsList($param =array()){
		$where = " WHERE 1 AND n.status = 10 AND n.news_id IS NOT NULL ";
		$page_size = 1;
		if($param['keywords'] !=""){
			$where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%')";
		}
		if($param['publish_start_time'] !=""){
			$where.=" AND p.push_time >='{$param['publish_start_time']}'";
		}
		if($param['publish_end_time'] !=""){
			$where.=" AND p.push_time <='{$param['publish_end_time']}'";
		}
		if($param['department_id'] !=""){
			$where.=" AND (p.department_id ='{$param['department_id']}')";
		}
		if($param['news_id'] !=""){
			$where.=" AND (n.news_id ='{$param['news_id']}')";
		}
		if($param['province'] !=""){
			$where.=" AND (n.province ='{$param['province']}')";
		}
		if($param['city'] !=""){
			$where.=" AND (n.city ='{$param['city']}')";
		}
		if($param['district'] !=""){
			$where.=" AND (n.district ='{$param['district']}')";
		}
		$page_config = config('paginate');
		$database_prefix = config('database.prefix');
		$page =  $param['page'];
		if($param['list_rows']!=""){
			$page_config['list_rows'] = $param['list_rows'];
		}
		if($page>=2){
			$start_page = ($page-1)*$page_config['list_rows'];
		}else{
			$start_page = 0;
		}
		if($order_by_str==""){
			$order_by_str = " p.push_id DESC ";
		}
		$sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}news_push AS p
		LEFT JOIN {$database_prefix}news AS n ON p.news_id = n.news_id
		LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
		{$where}) AS temp ";
		$total = Db::query($sql);
		$sql = "SELECT n.*,p.*,d.department_name,rd.department_name AS source_department_name,pr.region_name AS province,
		cr.region_name AS city,dr.region_name AS district,
		CONCAT(IFNULL(pr.region_name,''),IFNULL(cr.region_name,''),IFNULL(dr.region_name,'')) AS region_name_s
		FROM {$database_prefix}news_push AS p
		LEFT JOIN {$database_prefix}news AS n ON p.news_id = n.news_id
		LEFT JOIN {$database_prefix}department AS d ON p.department_id = d.department_id
		LEFT JOIN {$database_prefix}department AS rd ON n.department_id = rd.department_id
		LEFT JOIN {$database_prefix}region AS pr ON d.province 	= pr.region_id
		LEFT JOIN {$database_prefix}region AS cr ON d.city 		= cr.region_id
		LEFT JOIN {$database_prefix}region AS dr ON d.district 	= dr.region_id
		{$where} ORDER BY {$order_by_str} ";
		$sql.=" LIMIT $start_page,{$page_config['list_rows']}";
		$push_list = Db::query($sql);
        $news = new News();
        foreach ($push_list as $k=>$v){
            $push_list[$k]['type_name']     = $news->news_type[$v['type']];
            $push_list[$k]['status_name']   = $news->news_status[$v['status']];
            //查询改线索本单位是否已选用
            $department_id = session("Department.department_id");
            $newschoose = new Newschooserecord();
            $newschoose_info = $newschoose->where(['news_id' => $v['news_id'], 'department_id' => $department_id])->find();
            $push_list[$k]['choose_status'] = $newschoose_info?1 : 0;       //1为已选用
        }
		return array("total"=>$total[0]['t'],"data"=>$push_list);
	}
}