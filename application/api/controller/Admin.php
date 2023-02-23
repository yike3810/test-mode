<?php


namespace app\api\controller;


use think\Controller;
use think\Request;
use think\Session;

class Admin extends Controller
{
    public function _initialize() {
        $this->checkAdminSession();
    }
    //appSecret = "humvcse43vvauj4kdorzlowpimrigczk"
    //签名验证示例： //时间戳 $timestamp = time();
    //随机数 $nonce = mt_rand(1000,9999);
    //MD5 加密得到 sign $origin = $appSecret.$timestamp.$nonce;
    // $sign = md5($origin); Header 中设置：timestamp、sign、nonce，如下：
    public function checkAdminSession() {
//        $info = Request::instance()->header();
        $timestamp  = input("get.timestamp");//获取时间戳
        $nonce      = input("get.nonce");//获取随机数
        $sign       = input("get.sign");//获取签名
        $appkey     = input("get.appkey");//获取标识码
        if($timestamp==""){
            $this->journal("请求的时间戳为空");
            $this->redirect('index/publics/errors');//请求的时间戳不能为空
        }
        ######其他判断条件开始##############
        /*
         * 比如  $timestamp有效期 5分钟、随机数、签名不能为空等等
         *  */
        if($timestamp<=(time()-300)&&$timestamp>=time()){
            $this->journal("请求的时间戳已过期");
            $this->redirect('index/publics/errors');//请求的时间戳已过期
        }
        if(empty($nonce)){
            $this->journal("请求的随机数为空");
            $this->redirect('index/publics/errors');//请求的随机数不能为空
        }
        if(empty($sign)){
            $this->journal("请求的签名为空");
            $this->redirect('index/publics/errors');//请求的签名不能为空
        }

        if (empty($appkey)){
            $this->journal("请求的标识码为空");
            $this->redirect('index/publics/errors');//请求的标识码不能为空
        }

        ######其他判断条件结束##############
//        $appKey = '3gyWdRiPKkaMiiH6V3RUFybsdeDZ';
//        $appSecret = "humvcse43vvauj4kdorzlowpimrigczk";
        $api_account = new \app\common\model\Apiaccount;
//        $appSecret = $api_account->where("app_key = '{$appkey}'")->value('app_secret');
        $appSecret = config('jsl.app_secret');
        $_create_sign =  md5($appSecret.$timestamp.$nonce);
        if($_create_sign != $sign){
            $this->journal("请求的签名不正确");
            $this->redirect('index/publics/errors');//请求的签名不正确
        }
    }

    public function journal($response_data){
        $url = 'http://';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $url = 'https://';
        }

        if($_SERVER['SERVER_PORT'] != '80') {
            $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . ':' . $_SERVER['REQUEST_URI'];
        } else {
            $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['REQUEST_URI'];
        }
        $data_list = $_SERVER['QUERY_STRING'];;
        $api_log = new \app\common\model\Apilog;
        $data_log['api_url'] = $url;
        $data_log['request_parameter'] = $data_list;
        $data_log['response_data'] = $response_data;
        $data_log['api_time'] = date('Y-m-d H:i:s');
        $data_log['ip'] = get_client_ip();
        $api_log->save($data_log);
    }

    //操作记录
    public function operating($url,$status,$description) {
        $data = array();
        $data['Uid'] = Session::get('ThinkUser.ID');
        $data['Url'] = $url;
        $data['Description'] = $description;
        $data['Ip'] = get_client_ip();
        $data['Status'] = $status;
        $data['Dtime'] = date('Y-m-d H:i:s');
        $operating = new \app\common\model\Operating;
        $operating->save($data);
    }
}