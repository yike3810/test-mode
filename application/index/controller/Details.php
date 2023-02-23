<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Url;

class Details extends Admin
{
    public function order_detail()
    {
        $pointsgoods = new \app\common\model\Pointsgoods;
        $order       = new \app\common\model\Order;
        $ordergoods  = new \app\common\model\Ordergoods;
        $region      = new \app\common\model\Region;
        $shipping    = new \app\common\model\Shipping;
        $order_id    = input('order_id');//兑换订单id
        //当前会员id
        $member_id = $this->member_info['member_id'];//获取当前登录id
        //显示物流模块
        $order_info = $order ->where(["order_id"=>$order_id])->find();//查找order_id
        $shipping_info = $shipping ->where(['shipping_id'=>$order_info['shipping_id']])->find();
        $order_info['shipping_name'] = $shipping_info['shipping_name'];//获取快递公司名称
        //获取收货人信息
        //获取收货人的完整地址
        $order_info['addressAll']   = $region->where(['region_id'=>$order_info['province_id']])->value('region_name').'省 '
                                      .$region->where(['region_id'=>$order_info['city_id']])->value('region_name').' '
                                      .$region->where(['region_id'=>$order_info['district_id']])->value('region_name').' '
                                      .$order_info['address'];
        //获取商品详细信息
        $ordergoods_info = $ordergoods->where("order_id={$order_id}")->find();
        $order_info['goods_id'] = $ordergoods_info['goods_id'];
        $order_info['goods_num'] = $ordergoods_info['goods_num'];
        $order_info['goods_name'] = $ordergoods_info['goods_name'];
        $order_info['goods_points'] = $ordergoods_info['goods_points'];
        $order_info['goods_image'] = $pointsgoods ->where(['goods_id'=>$ordergoods_info['goods_id']])->value('goods_image');
        //热门兑换商品
        $list        = $pointsgoods->where("goods_show=1")->limit(4)->order("goods_id asc")->column('');
        foreach ($list as $k=>$v){
            $list[$k]['goods_image'] = getRootUrl().'uploads/goods/' . $v['goods_image'];
        }
        //判断是否为方正客户端打开
        $client_status = $this->member_info['client_status'];
        $this->assign('client_status', $client_status);
        $this->assign('ios_status',$this->ios_status);
        $this->assign('list',json_encode(array_values($list)));
        $this->assign('order_info',$order_info);
        return $this->fetch();
    }


    public function index()
    {
        $pointsgoods      = new \app\common\model\Pointsgoods;
        $order            = new \app\common\model\Order;
        $ordergoods       = new \app\common\model\Ordergoods;
        $pointsgoodsimage = new \app\common\model\Pointsgoodsimage;
        $goods_id = input('goods_id');//商品id
        $goods_message = $pointsgoods->where(["goods_id"=>$goods_id])->find();
        $goods_banner  = $pointsgoodsimage->where(["goods_id"=>$goods_id])->column('');
        $list          = $pointsgoods->where("goods_show=1")->limit(4)->order("goods_id asc")->column('');
        foreach ($list as $k=>$v){
            $list[$k]['goods_image'] = getRootUrl().'uploads/goods/' . $v['goods_image'];
        }
        $count = 0;
        $where['member_id'] = $this->member_info['member_id'];
        $order_list = $order->where($where)->column('');
        $where_d['goods_id'] = $goods_id;
        foreach ($order_list as $k=>$v){
            $where_d['order_id'] = $v['order_id'];
            $count += $ordergoods->where($where_d)->value('goods_num');
        }
        //判断是否为方正客户端打开
        $client_status = $this->member_info['client_status'];
        $this->assign('client_status', $client_status);
        $this->assign('ios_status',$this->ios_status);
        $this->assign('goods_banner',json_encode(array_values($goods_banner)));
        $this->assign('goods_message',$goods_message);
        $this->assign('goods_count',$count);
        $this->assign('list',json_encode(array_values($list)));
        return $this->fetch();
    }
    public function placeorder_detail()
    {
        $region = new \app\common\model\Region;
        $address = new \app\common\model\Address;
        $pointsgoods = new \app\common\model\Pointsgoods;
        $goods_id = input('goods_id');//商品id
        $address_id = input('address_id');//地址id
        $goods_num = input('goods_num');//商品选购数量
        $member_id = $this->member_info['member_id'];//当前会员id
        //获取收货地址
        if($address_id){
            //判断是否有地址id,如无则收货地址为默认地址，否则为当前选取地址
            $address_list= $address->where(['address_id'=>$address_id])->find();
            $address_list['addressAll'] = $region->where(['region_id'=>$address_list['province_id']])->value('region_name').'省 '
                                        .$region->where(['region_id'=>$address_list['city_id']])->value('region_name').' '
                                        .$region->where(['region_id'=>$address_list['district_id']])->value('region_name').' '
                                        .$address_list['address'];
        }else{
            $address_list= $address->where(['member_id'=>$member_id,'is_default'=>1])->find();
            $address_list['addressAll'] = $region->where(['region_id'=>$address_list['province_id']])->value('region_name').'省 '
                                        .$region->where(['region_id'=>$address_list['city_id']])->value('region_name').' '
                                        .$region->where(['region_id'=>$address_list['district_id']])->value('region_name').' '
                                        .$address_list['address'];
        }
        //获取当前下单商品
        $pointsgoods_info = $pointsgoods->where(['goods_id'=>$goods_id])->find();
        $pointsgoods_info['goods_num'] = $goods_num;
        $pointsgoods_info['all_point'] = $goods_num * $pointsgoods_info['goods_points'];
        //判断是否为方正客户端打开
        $client_status = $this->member_info['client_status'];
        $this->assign('client_status', $client_status);
        $this->assign('ios_status',$this->ios_status);
        $this->assign('goods_id',$goods_id);
        $this->assign('goods_num',$goods_num);
        $this->assign('address_list',$address_list);
        $this->assign('pointsgoods_info',$pointsgoods_info);
        return $this->fetch();
    }
    public function submitExchange(){
        $pointsgoods = new \app\common\model\Pointsgoods;
        $order = new \app\common\model\Order;
        $param['province_id'] = input('province_id');//省
        $param['city_id'] = input('city_id');//市
        $param['district_id'] = input('district_id');//县
        $param['address'] = input('address');//详细地址
        $param['phone'] = input('phone');
        $param['all_point'] = input('all_point');
        $param['consignee'] = input('consignee');
        $param['goods_id'] = input('goods_id');
        $param['goods_num']= input('goods_num');
        $param['member_id'] = $this->member_info['member_id'];
        $param['outer_member_id'] = $this->member_info['outer_member_id'];
        $param['member_name'] = $this->member_info['member_name'];
        $result = $order ->submitExchange($param);
        if(!empty($result)){
            if($result['status'] == 1){
                $goods_name = $pointsgoods -> where(['goods_id'=>$param['goods_id']])->value('goods_name');
                parent::operating($param['all_point'],$goods_name, $param['goods_num']);
            }
            echo json_encode($result);exit;
        }
    }
}
