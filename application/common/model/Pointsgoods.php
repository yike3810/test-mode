<?php
namespace app\common\model;
use think\Db;

class Pointsgoods extends \think\Model
{
	protected $name = 'points_goods';
	//自动验证
	protected $_validate = array(

	);
	//自动完成
	protected $_auto = array ( 

	);
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
	//添加当前管理员
	protected function userid() {
		return $_SESSION['ThinkUser']['ID'];
	}
    public $goods_type = array(
        1=>'实物',
        2=>'优惠券',
        3=>'卡密',
        4=>'充值',
    );
    public $goods_islimit = array(
        0=>'否',
        1=>'是',
    );
    public $goods_commend = array(
        0=>'未推荐',
        1=>'已推荐',
    );
    public $goods_state = array(
        0=>'未提交',
        1=>'已提交',
        2=>'审核通过',
        3=>'驳回',
    );

    public function getGoodsList($param =array())
    {
        $where = " WHERE 1 ";
        $page_size = 1;
        if($param['goods_id'] !=""){
            $where.=" AND m.goods_id ='{$param['goods_id']}'";
        }
//        if($param['keywords'] !=""){
//            $where.=" AND (m.goods_name LIKE '%{$param['keywords']}%'";
//        }
        if($param['goods_name'] !=""){
            $where.=" AND m.goods_name ='{$param['goods_name']}'";
        }
        if($param['goods_show'] !=""){
            $where.=" AND m.goods_show ='{$param['goods_show']}'";
        }
        if($param['goods_image'] !=""){
            $where.=" AND m.goods_image ='{$param['goods_image']}'";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
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
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " m.add_time DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT * FROM {$database_prefix}points_goods AS m   GROUP BY m.goods_id ) AS temp ";

        $total = Db::query($sql);
        $sql = "SELECT m.*,d.goods_name,ms.goods_id FROM {$database_prefix}points_goods AS m 
        LEFT JOIN {$database_prefix}points_goods AS ms ON m.goods_id = ms.goods_id
        LEFT JOIN {$database_prefix}points_goods AS d  ON m.goods_name = d.goods_name
        {$where}  GROUP BY m.goods_name ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $points_goods_list = Db::query($sql);
        foreach ($points_goods_list as $k=>$v){
            $points_goods_list[$k]['type']   = $this->goods_type[$v['type']];
            $points_goods_list[$k]['goods_islimit']   = $this->goods_islimit[$v['goods_islimit']];
            $points_goods_list[$k]['goods_state']   = $this->goods_state[$v['goods_state']];
        }
        return array("total"=>$total[0]['t'],"data"=>$points_goods_list);
    }


}
