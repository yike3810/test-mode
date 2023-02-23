<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Url;
use think\Session;
use think\Cookie;
use think\console\Output;

class Admin extends Controller
{
    public function _initialize() {
        $this->checkAdminSession();
    }
    //判断用户是否登录的方法
    public function checkAdminSession() {
        $member_id 		=  cookie('member_id');
        $key   			=  cookie('key');
        if ($key==null) {
            Cookie::clear('jsl_');
            $this->redirect('Publics/errors');
        }else {
            $member_token = new \app\common\model\Membertoken;
            $token_info = $member_token->getMemberTokenInfoByToken($key);
            if(empty($token_info)){
                Cookie::clear('jsl_');
                $this->redirect('Publics/errors');
            }
//			将登录用户的信息存入 member_info
            $member = new \app\common\model\Member;
            $result=$member->where(["member_id"=>$member_id])->find();
            if (empty($result)) {

                Cookie::clear('jsl_');
                $this->redirect('Publics/errors');
            }
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if(strpos($userAgent,"iPhone") || strpos($userAgent,"iPad") || strpos($userAgent,"iPod")){
                $this->ios_status = 1;
            }else {
                $this->ios_status = 0;
            }
            $address = $_SERVER['HTTP_X_REQUESTED_WITH'];//服务请求
            //判断是否为方正客户端打开
            $client_status = strpos($address,'com.founder') !== false ?1:0;
            $this->member_info 	= $result;
            $this->member_info['client_status'] = $client_status;
        }
    }
    //操作记录
    public function operating($points,$goodsname,$goods_num) {
        //传入兑换积分、商品名称、商品数量
        $data = array();
        $data['member_id'] = $this->member_info['member_id'];
        $data['member_name'] = $this->member_info['member_name'];
        $data['points'] = -$points;
        $data['desc'] = "兑换商品 {$goodsname} {$goods_num}件";
        $data['add_time'] = date('Y-m-d H:i:s');
        $points_log_data['stage'] 		= 'exchange';
        $pointslog= new \app\common\model\Pointslog;
        $pointslog->save($data);
    }


    /**
     * @param int $no_of_codes //定义一个int类型的参数 用来确定生成多少个优惠码
     * @param array $exclude_codes_array //定义一个exclude_codes_array类型的数组
     * @param int $code_length //定义一个code_length的参数来确定优惠码的长度
     * @return array//返回数组
     */
    function generate_promotion_code($no_of_codes, $exclude_codes_array = '', $code_length = 4)
    {
        $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $promotion_codes = array();//这个数组用来接收生成的优惠码
        for ($j = 0; $j < $no_of_codes; $j++) {
            $code = "";
            for ($i = 0; $i < $code_length; $i++) {
                $code .= $characters[mt_rand(0, strlen($characters) - 1)];
            }//如果生成的4位随机数不再我们定义的$promotion_codes函数里面
            if (!in_array($code, $promotion_codes)) {
                if (is_array($exclude_codes_array))//
                {
                    if (!in_array($code, $exclude_codes_array))//排除已经使用的优惠码
                    {
                        $promotion_codes[$j] = $code;
            //将生成的新优惠码赋值给promotion_codes数组
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
        return $promotion_codes;
    }

    public function generate_code(){

    }
}
