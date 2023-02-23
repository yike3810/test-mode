<?php
namespace app\common\model;
use think\Db;
class Pointsgoodscategory extends \think\Model
{
	protected $name = 'points_goods_category';
	//自动验证
    public function getgoodscategoryList($param =array())
    {
        $where = " WHERE 1   ";
        $page_size = 1;
        if($param['category_name'] !=""){
            $where.=" AND (ps.category_name LIKE '%{$param['category_name']}%')";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['limit']!=""){
            $page_config['list_rows'] = $param['limit'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " ps.sort ASC ,ps.category_id ASC";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}points_goods_category AS ps 
    	{$where} GROUP BY ps.category_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT ps.*
    	FROM {$database_prefix}points_goods_category AS ps 
    	{$where} GROUP BY ps.category_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
}
