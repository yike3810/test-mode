<?php
namespace app\api\controller;
use app\common\model\Jslapiaccount;
class Buildurl extends Admin
{
    public function buildurlwithsign(){
        $data['outer_member_id'] = input("get.uid");
        $data['member_points'] = input("get.credits");
        $data['phone'] = input("get.phone");
//        $data['outer_member_id'] = 2;
//        $data['member_points'] = 6000;
//        $data['phone'] = 18302247559;
//        $data['outer_member_id'] = 1;
//        $data['member_points'] = 6000;
//        $data['phone'] = 18394183669;
        $appID = input("get.appID");
        $redirect = input("get.redirect");
        $member = new \app\common\model\Member;
        //判断参数类型是否正确
        if (!is_numeric($data['member_points']) || !is_numeric($data['phone'])){
            parent::journal("参数类型错误");
            $this->redirect('index/publics/errors');
//           return $this->fetch('index@/public/errors');//参数类型错误跳转到错误页面
//            header("Location:".url('index@/public/errors'));
        }
        //将获取的数据入库
        $memberlist = $member->where(["outer_member_id" => $data['outer_member_id']])->find();
        if (!empty($memberlist)){
            if ($member->data($data)){
                $member->save($data,["outer_member_id"=>$data['outer_member_id']]);
            }else{
                parent::journal("更新失败");
                $this->redirect('index/publics/errors');//入库失败跳转到错误页面
            }
        }else{
            if ($member->data($data)){
                $member->save();
            }else{
                parent::journal("添加失败");
                $this->redirect('index/publics/errors');;//入库失败跳转到错误页面
            }
        }
        $memberlist = $member->where(["outer_member_id" => $data['outer_member_id']])->value('member_id');
        //获取key值并入库
        $data['key'] = $this->getToken($memberlist,$data['phone'],'wap');
        //将数据保存到cookie中
        cookie('member_id',$memberlist);
        cookie('key',$data['key']);
        cookie("phone",$data['phone']);

        if ($redirect != ''){
            parent::journal("访问成功,跳转到指定网页");
            header("Location: $redirect");//跳转到指定页面
        }else{
            parent::journal("跳转到首页");
//            header("Location: ".url("index/index/index"));//跳转到指定页面
            $this->redirect('index/index/index');
        }
    }
    public function getToken($member_id, $member_name, $client)
    {
        $mb_token_info = array();
        $token = md5($member_name . strval(time()) . strval(rand(0, 999999)));
        $mb_token_info['member_id'] = $member_id;
        $mb_token_info['member_name'] = $member_name;
        $mb_token_info['token'] = $token;
        $mb_token_info['login_time'] = time();
        $mb_token_info['client_type'] = $client;
        $member_token = new \app\common\model\Membertoken;
        $result = $member_token->save($mb_token_info);
        if ($result) {
            return $token;
        } else {
            return null;
        }
    }
}