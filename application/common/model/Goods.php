<?php
namespace app\common\model;
class Goods extends \think\Model
{
    public function getGoodsList($param =array())
    {
        $where = " WHERE 1 ";
        $page_size = 1;
        if($param['goods_id'] !=""){
            $where.=" AND ms.goods_id ='{$param['goods_id']}'";
        }
        if($param['keywords'] !=""){
            $where.=" AND (m.goods_name LIKE '{$param['keywords']}%' OR m.goods_price LIKE '{$param['keywords']}%'
OR m.goods_points LIKE '{$param['keywords']}%'
OR m.goods_name LIKE '{$param['keywords']}%'";
        }
        if($param['goods_name'] !=""){
            $where.=" AND m.goods_name ='{$param['goods_name']}'";
        }
        if($param['goods_show'] !=""){
            $where.=" AND m.goods_show ='{$param['goods_show']}'";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page = input('param.page');
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        if($order_by_str==""){
            $order_by_str = " m.goods_id ASC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT * FROM {$database_prefix}points_goods AS m   GROUP BY m.goods_id ) AS temp ";
        $total = Db::query($sql);

        $sql = "SELECT m.*,d.goods_name,ms.goods_id FROM {$database_prefix}points_goods AS m
LEFT JOIN {$database_prefix}points_goods AS ms ON m.goods_id = ms.goods_id
LEFT JOIN {$database_prefix}points_goods AS d  ON m.goods_name = d.goods_name
{$where}  GROUP BY m.goods_name ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $points_goods_list = Db::query($sql);
        return array("total"=>$total[0]['t'],"data"=>$points_goods_list);
    }

}
