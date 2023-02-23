<?php



namespace app\common\model;
use think\Db;


class Newschooserecord extends \think\Model
{
    protected $name = "news_choose_record";
    /**
     * 获取已选用线索列表
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param dateTime  	choose_start_time 选用时间-开始
     * @param dateTime  	choose_end_time 选用时间-结束
     * @param integer  		department_id 选用单位id
     * @param integer  		industry_id 行业id
     * @param integer  		province 省id
     * @param integer  		city 城市id
     * @param integer  		district 区县id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getNewsList($param);
     */
    public function getChoosedList($param =array()){
        $where = " WHERE 1 AND n.status = 10 AND n.news_id IS NOT NULL ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%')";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['choose_start_time'] !=""){
            $where.=" AND r.choose_time >='{$param['choose_start_time']}'";
        }
        if($param['choose_end_time'] !=""){
            $where.=" AND r.choose_time <='{$param['choose_end_time']}'";
        }
        /*
         * 发布单位检索信息开始
         * */
        if($param['department_id'] !=""){
            $where.=" AND (r.department_id ='{$param['department_id']}')";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (d.district ='{$param['district']}')";
        }
        /*
         * 发布单位检索信息开始
         * */
        /*
         * 选用单位检索信息开始
         * */
        if($param['choose_department_id'] !=""){
            $where.=" AND (rd.department_id ='{$param['choose_department_id']}')";
        }
        if($param['choose_province'] !=""){
            $where.=" AND (rd.province ='{$param['choose_province']}')";
        }
        if($param['choose_city'] !=""){
            $where.=" AND (rd.city ='{$param['choose_city']}')";
        }
        if($param['choose_district'] !=""){
            $where.=" AND (rd.district ='{$param['choose_district']}')";
        }
        /*
         * 选用单位检索信息结束
         * */
        if($param['industry_id'] !=""){
            $where.=" AND (n.industry_id ='{$param['industry_id']}')";
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
        $order_by_str = '';
        if($param['order_by_str']==""){
            $order_by_str = " n.news_id DESC ";
        }else{
            $order_by_str = $param['order_by_str'];
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM  {$database_prefix}news_choose_record AS r 
        LEFT JOIN {$database_prefix}news AS n ON r.news_id = n.news_id
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}department AS rd ON r.department_id = rd.department_id
    	{$where} GROUP BY r.news_id,r.department_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT n.*,r.*,d.department_name,pr.region_name AS province,
			cr.region_name AS city,dr.region_name AS district
    	FROM {$database_prefix}news_choose_record AS r  
    	LEFT JOIN {$database_prefix}news AS n ON r.news_id = n.news_id
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}department AS rd ON r.department_id = rd.department_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY r.news_id,r.department_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        $news = new News();
        $department = new Department();
        foreach ($news_list as $k=>$v){
            $news_list[$k]['type_name']     = $news->news_type[$v['type']];
            $news_list[$k]['status_name']   = $news->news_status[$v['status']];
            $news_list[$k]['source']   = $department->where("department_id='{$v['department_id']}'")->value('department_name');
        }
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
}