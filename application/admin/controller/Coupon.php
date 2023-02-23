<?php


namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Url;

class Coupon extends Admin
{
    public function index(){
        $generatecoupon = new \app\common\model\Generatecoupon;
        $dates = array_values($generatecoupon->order('id desc')->column(''));
        foreach ($dates as $k=>$v){
            $dates[$k]['distribution_time']=  date('Y-m-d', strtotime($v['distribution_time']));
        }
        $this->assign('dates', $dates);
        return $this->fetch();
    }
    public function getCouponList(){
        $keywords = input("request.keywords",'','htmlspecialchars,addslashes,strip_tags');
        $where = array();
        if($keywords!="") {
            $where['real_name|phone|address'] = array("LIKE","%$keywords%");
        }
        $id = input('id');
        $where['log_id'] = $id;
        $coupon = new \app\common\model\Coupon;
        $commodity = new \app\common\model\Commodity;
        $list = $coupon->where($where)->order("ID DESC")->paginate();
        $coupon_list = $list->toArray();
        foreach ($coupon_list['data'] as $k => $v)
        {
            $coupon_list['data'][$k]['coupon_status'] = $coupon->status[$v['coupon_status']];
            $coupon_list['data'][$k]['commodity'] = explode(',',$v['commodity_id']);
            foreach ($coupon_list['data'][$k]['commodity'] as $m=>$n)
            {
                $coupon_list['data'][$k]['commodity'][$m] = $commodity->where(['id'=>$n])->value('commodity_name');
            }
            $coupon_list['data'][$k]['commodity'] = implode(',',$coupon_list['data'][$k]['commodity']);
        }
        $result = array("code"=>0,"count" => $coupon_list['total'],"data"=>$coupon_list['data']);
        echo json_encode($result);
        exit;
    }

    public function addcoupon()
    {
        $commodity = new \app\common\model\Commodity;
        $commodity_list = $commodity ->column('');
        foreach ($commodity_list as $k=>$v){
            $commodity_list[$k]['name'] = $v['commodity_name'];
            $commodity_list[$k]['value'] = $v['id'];
        }
        $this->assign('commodity_list',json_encode($commodity_list) );
        return $this->fetch();
    }
    public function addcoupon_do()
    {
        $data = array();
        if (request()->isAjax()) {
            //自动完成验证与新增
            $generatecoupon = new \app\common\model\Generatecoupon;
            $coupon = new \app\common\model\Coupon;
            $data['coupon_name'] = input('coupon_name');
            $data['coupon_num'] = input('coupon_num');
            $data['commodity_id'] = input('select');
            $data['distribution_time'] = date("Y-m-d H:i:s");
            $data['receive_started_at'] = input('post.receive_started_at');
            $data['receive_ended_at'] = input('post.receive_ended_at');
            $data['use_started_at'] = input('post.use_started_at');
            $data['use_ended_at'] = input('post.use_ended_at');
            $rule = array();
            $rule['type'] = 1;//满减类型
            $rule['threshold'] = input('threshold');
            $rule['amount'] = input('amount');
            $data['rule'] = json_encode($rule);
            if ($generatecoupon->data($data)) {
                $generatecoupon->save();
                $no_of_codes = $data['coupon_num'];
                $exclude_codes_array = '';
                $code_length = 12;
                $promotion_code = $this->generate_promotion_code($no_of_codes,$exclude_codes_array,$code_length);
                foreach ($promotion_code as $k => $v) {
                    $code_data[] = array(
                        'coupon_code'         => $v,
                        'coupon_name'         => $data['coupon_name'],
                        'commodity_id'        =>  $data['commodity_id'],
                        'receive_started_at'  => $data['receive_started_at'],
                        'receive_ended_at'    => $data['receive_ended_at'],
                        'use_started_at'      => $data['use_started_at'],
                        'use_ended_at'        => $data['use_ended_at'],
                        'rule'                =>$data['rule'],
                        'log_id'              => $generatecoupon->id,
                        'coupon_status'       =>  0,

                    );
                }
                $coupon->saveAll($code_data);
                parent::operating(request()->path(), 0, '新增成功');
            } else {
                parent::operating(request()->path(), 1, '新增失败');
                return array('s' => $generatecoupon->getError());
            }
        }
    }

    public function getData()
    {
        return date("Y-m-d H:i:s");
    }
    public function getItemData()
    {
        $commodity = new \app\common\model\Commodity;
        $list = $commodity ->paginate('');
        $commodity_list = $list->toArray();
        foreach ($commodity_list['data'] as $k=>$v){
            $commodity_list['data'][$k]['name'] = $v['commodity_name'];
            $commodity_list['data'][$k]['value'] = $v['id'];
        }
        $result = array("code"=>0,"count" => $commodity_list['total'],"data"=>$commodity_list['data']);
        echo json_encode($result);
        exit;
    }

    /**
     * @param int $no_of_codes//定义一个int类型的参数 用来确定生成多少个优惠码
     * @param array $exclude_codes_array//定义一个exclude_codes_array类型的数组
     * @param int $code_length //定义一个code_length的参数来确定优惠码的长度
     * @return array//返回数组
     **/

    function generate_promotion_code($no_of_codes, $exclude_codes_array = '', $code_length = 12)
    {
        $characters = "0123456789";
        $promotion_codes = array();//这个数组用来接收生成的优惠码
        for ($j = 0; $j < $no_of_codes; $j++) {
            $code = "";
            for ($i = 0; $i < $code_length; $i++) {
                $code .= $characters[mt_rand(0, strlen($characters) - 1)];
                //如果生成的随机数不在我们定义的$promotion_codes函数里面
                if (!in_array($code, $promotion_codes)) {
                    if (is_array($exclude_codes_array))//
                    {
                        if (!in_array($code, $exclude_codes_array))//排除已经使用的优惠码
                        {
                            $promotion_codes[$j] = $code;//将生成的新优惠码赋值给promotion_codes数组
                        } else {
                            $j--;
                        }
                    } else {
                        $promotion_codes[$j] = $code;//将优惠码赋值给数组
                    }
                } else {
                    $j--;
                }
            }
        }
        return $promotion_codes;
    }
    public function received()
    {
        $generatecoupon = new \app\common\model\Generatecoupon;
        $dates = array_values($generatecoupon->order('id desc')->column(''));
        foreach ($dates as $k=>$v){
            $dates[$k]['distribution_time']=  date('Y-m-d', strtotime($v['distribution_time']));
        }
        $this->assign('dates', $dates);
        return $this->fetch();
    }
    public function getreceivedList()
    {
        $keywords = input("request.keywords",'','htmlspecialchars,addslashes,strip_tags');
        $where = array();
        if($keywords!=""){
            $where['coupon_code'] = array("LIKE","%$keywords%");
        }
        $id = input('id');
        $where['log_id'] = $id;
        $where['coupon_status'] = 1;
        $coupon = new \app\common\model\Coupon;
        $commodity = new \app\common\model\Commodity;
        $list = $coupon->where($where)->order("ID DESC")->paginate();
        $coupon_list = $list->toArray();
        foreach ($coupon_list['data'] as $k => $v) {
            $coupon_list['data'][$k]['coupon_status'] = $coupon->status[$v['coupon_status']];
            $coupon_list['data'][$k]['commodity'] = explode(',',$v['commodity_id']);
            foreach ($coupon_list['data'][$k]['commodity'] as $m=>$n)
            {
                $coupon_list['data'][$k]['commodity'][$m] = $commodity->where(['id'=>$n])->value('commodity_name');
            }
            $coupon_list['data'][$k]['commodity'] = implode(',',$coupon_list['data'][$k]['commodity']);
        }
        $result = array("code"=>0,"count" => $coupon_list['total'],"data"=>$coupon_list['data']);
        echo json_encode($result);
        exit;
    }
    public function used()
    {
        $generatecoupon = new \app\common\model\Generatecoupon;
        $dates = array_values($generatecoupon->order('id desc')->column(''));
        foreach ($dates as $k=>$v){
            $dates[$k]['distribution_time']=  date('Y-m-d', strtotime($v['distribution_time']));
        }
        $this->assign('dates', $dates);
        return $this->fetch();
    }
    public function getusedList()
    {
        $keywords = input("request.keywords",'','htmlspecialchars,addslashes,strip_tags');
        $where = array();
        if($keywords!=""){
            $where['real_name|phone|address'] = array("LIKE","%$keywords%");
        }
        $id = input('id');
        $where['log_id'] = $id;
        $where['coupon_status'] = 10;
        $coupon = new \app\common\model\Coupon;
        $commodity = new \app\common\model\Commodity;
        $list = $coupon->where($where)->order("ID DESC")->paginate();
        $coupon_list = $list->toArray();
        foreach ($coupon_list['data'] as $k => $v) {
            $coupon_list['data'][$k]['coupon_status'] = $coupon->status[$v['coupon_status']];
            $coupon_list['data'][$k]['commodity'] = $commodity->where(['id'=>$v['commodity_id']])->value('commodity_name');
        }
        $result = array("code"=>0,"count" => $coupon_list['total'],"data"=>$coupon_list['data']);
        echo json_encode($result);
        exit;
    }
}
