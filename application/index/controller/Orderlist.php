<?php

namespace app\index\controller;

use app\common\model\Pointsgoods;
use think\Controller;
use think\Request;
use think\Url;

class Orderlist extends Admin
{
    public function index()
    {
        //判断是否为方正客户端打开
        $client_status = $this->member_info['client_status'];
        $this->assign('client_status', $client_status);
        $this->assign('ios_status',$this->ios_status);
        $order = new \app\common\model\Order;
        $order->AutoShipping();
        return $this->fetch();
    }

    //获取全部订单列表
    public function getrecordlist()
    {
        $order = new \app\common\model\Order;
        $pointsgoods = new \app\common\model\Pointsgoods;
        $ordergoods = new \app\common\model\Ordergoods;
        $activeName = input('post.activeName');//点击栏目名称：a全部,b待发货,c待收货,d已完成
        $page = input('post.page');
        $member_id = $this->member_info['member_id'];
        switch ($activeName) {
            case 'a':
                //全部
                $news_info2 = array_values($order->where(['member_id' => $member_id])->where(['order_status' => ['in',[0,1,2,10]]])->page($page, 5)->order('add_time desc')->column(''));
                break;
            case 'b':
                //待发货
                $news_info2 = array_values($order->where(['member_id' => $member_id])->where("shipping_status= 0 AND order_status = 1 or order_status = 0")->order('add_time desc')->page($page,5)->column(''));
                break;
            case 'c':
                //待收货
                $news_info2 = array_values($order->where(['member_id' => $member_id])->where(["shipping_status" => 1 ,"order_status" => 1])->order('add_time desc')->page($page,5)->column(''));
                break;
            case 'd':
                //已完成
                $news_info2 = array_values($order->where(['member_id' => $member_id])->where(["order_status" => 10])->order('add_time desc')->page($page,5)->column(''));
                break;
            default:
        }
        //兑换订单
        foreach ($news_info2 as $k => $v) {
            $order_list[$k]['order_id'] = $v['order_id'];
            $order_list[$k]['order_sn'] = $v['order_sn'];
            $order_list[$k]['order_status'] = $v['order_status'];
            $order_list[$k]['shipping_status'] = $v['shipping_status'];
            $order_list[$k]['order_goods'] = $ordergoods->where(['order_id' => $v['order_id']])->column('');
           //兑换商品
            foreach ($order_list[$k]['order_goods'] as $key => $value) {
                $order_list[$k]['order_goods'][$key]['goods_img'] = $pointsgoods->where(['goods_id' => $value['goods_id']])->value('goods_image');
                $order_list[$k]['order_goods'][$key]['goods_name'] = $value['goods_name'];
                $order_list[$k]['order_goods'][$key]['goods_points'] = $value['goods_points'];
                $order_list[$k]['order_goods'][$key]['goods_num'] = $value['goods_num'];
            }
            $order_list[$k]['total_goods'] = $ordergoods->where(['order_id' => $v['order_id']])->sum('goods_num');
            $order_list[$k]['total_points'] = $v['all_point'];
        }
        $order_list = empty($order_list)?[]:$order_list;
        return json_encode($order_list);
    }
    public function goodslist()
    {
        $member_id = $this->member_info['member_id'];
        $member  = new \app\common\model\Member;
        $points   =  $member->where(['member_id' => $member_id])->value('member_points');
        //判断是否为方正客户端打开
        $client_status = $this->member_info['client_status'];
        $this->assign('client_status', $client_status);
        $this->assign('ios_status',$this->ios_status);
        $this->assign('member_id', $member_id);
        $this->assign('points', $points);
        return $this->fetch();
    }

    public function getGoodslist(){

        $pointsgoods = new \app\common\model\Pointsgoods;
        $page = input('page');//分页序号
        $keywords = input('keywords');
        $where = array();
        if ($keywords != '') {
            $where['goods_name'] = ["LIKE", "%$keywords%"];
        }
        $list = $pointsgoods->where($where)->where(['goods_show' => 1, 'goods_state' => 2])->page($page, 6)->order('goods_id desc')->column('');
        foreach ($list as $k => $v) {
            $list[$k]['goods_image'] = getRootUrl() . 'uploads/goods/' . $v['goods_image'];
        }
        echo json_encode(array_values($list));
        exit;
    }
}
