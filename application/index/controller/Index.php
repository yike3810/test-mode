<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Url;

class Index extends Admin
{
    public function index()
    {
//        //判断是否为方正客户端打开
//        $client_status = $this->member_info['client_status'];
//        $this->assign('client_status', $client_status);
//
//        $pointsgoods = new \app\common\model\Pointsgoods;
//        $goodslist = $pointsgoods->where(['goods_state'=>2,'goods_show'=>1])->order('goods_commend desc')->limit(6)->column('');
//        foreach ($goodslist as $k => $v) {
//            $goods_list []= [
//                'goods_id'=>$v['goods_id'],
//                'goods_name'=>$v['goods_name'],
//                'goods_image'=> getRootUrl().'uploads/goods/' . $v['goods_image'],
//                'goods_points'=>$v['goods_points'],
//                'goods_storage'=>$v['goods_storage'],
//            ];
//        }
//        $this->assign('goods_list', json_encode($goods_list));
//        $this->assign('points', json_encode($this->member_info['member_points']));
        return $this->fetch();
    }
    public function search(){

        return $this->fetch();
    }
    public function pointsintroduction(){
        $parameterlong = new \app\common\model\Parameterlong;
        $integraldescription = $parameterlong->where(['param_name'=>'points_introduction'])->value('param_value');
        //判断是否为方正客户端打开
        $client_status = $this->member_info['client_status'];
        $this->assign('ios_status',$this->ios_status);
        $this->assign('client_status', $client_status);
        $this->assign('integraldescription', $integraldescription);
        return $this->fetch();
    }

}
