<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Session;

class Ssodocking extends Controller
{
    //appSecret = "humvcse43vvauj4kdorzlowpimrigczk"
    //签名验证示例： //时间戳 $timestamp = time();
    //随机数 $nonce = mt_rand(1000,9999);
    //MD5 加密得到 sign $origin = $appSecret.$timestamp.$nonce;
    // $sign = md5($origin); Header 中设置：timestamp、sign、nonce，如下：
    public function checkAdminSession() {
//        $info = Request::instance()->header();
        $postStr = file_get_contents("php://input");
        $data    = json_decode($postStr);

        $timestamp  = $data->timestamp;//获取时间戳
        $sign       = $data->sign;//获取签名
        if($timestamp==""){
            $this->journal("请求的时间戳为空");//请求的时间戳不能为空
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的时间戳不能为空",
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
        ######其他判断条件开始##############
        /*
         * 比如  $timestamp有效期 5分钟、随机数、签名不能为空等等
         *  */
        $time = $this->getMillisecond();
        $min_time = $time-300*1000;
        $max_time = $time+120*1000;
        if($timestamp<=$min_time||$timestamp>=$max_time){
            $this->journal("请求的时间戳已过期");//请求的时间戳已过期
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的时间戳已过期",
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
        if(empty($sign)){
            $this->journal("请求的签名为空");//请求的签名不能为空
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的签名不为空",
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
        ######其他判断条件结束##############
        $msg_data   = $data->msg->data;
        $key_array  = array();

        //提取data中的key，放入数组当中
        foreach ($msg_data as $k=>$v){
            array_push($key_array,$k);
        }
        //对key按照字典排序
        sort($key_array);
        foreach ($key_array as $k=>$v){
            foreach ($msg_data as $key=>$value){
                if ($v==$key){
                    $sortstr1[$v] = $value;
                }
            }
        }
        $sortstr    = json_encode($sortstr1,JSON_UNESCAPED_UNICODE);
        $sortMapStr = $sortstr;
        //获取appkey和sercet
        $appkey = 'jifenshop';
        $secret = '021c8f81b0e34d9ab2513807ac7d1590';
        $_create_sign =  md5($appkey.$secret.$sortMapStr.$timestamp);
        if($_create_sign != $sign){
            $this->journal("请求的签名不正确");//请求的签名不正确
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的签名不正确",
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    public function getTicketSession() {
        $timestamp    = input("get.timestamp");//获取时间戳
        $sign         = input("get.sign");//获取签名
        $ticket       = input("get.ticket");//获取ticket
        $appkey       = input("get.appCode");//获取appCode
        $redirect_url = urldecode(input("get.redirect_url"));//获取redirect_url
        if($timestamp==""){
            $this->journal("请求的时间戳为空");//请求的时间戳不能为空
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的时间戳不能为空",
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
        ######其他判断条件开始##############
        /*
         * 比如  $timestamp有效期 5分钟、随机数、签名不能为空等等
         *  */
        $time = $this->getMillisecond();
        $min_time = $time-300*1000;
        $max_time = $time+120*1000;
        if($timestamp<=$min_time||$timestamp>=$max_time){
            $this->journal("请求的时间戳已过期");//请求的时间戳已过期
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的时间戳已过期",
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
        if(empty($sign)){
            $this->journal("请求的签名为空");//请求的签名不能为空
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的签名不为空",
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
        if(empty($ticket)){
            $this->journal("请求的ticket为空");//请求的签名不能为空
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的ticket不为空",
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
        if(empty($redirect_url)){
            $this->journal("请求的redirect_url为空");//请求的签名不能为空
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的redirect_url不为空",
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
        ######其他判断条件结束##############
        //获取appkey和sercet
        $appkey = $appkey;
        $secret = '021c8f81b0e34d9ab2513807ac7d1590';
        $_create_sign =  md5($appkey.$secret.'$'.$ticket.'$'.$timestamp.'&'.urlencode($redirect_url));
        if($_create_sign != $sign){
            $this->journal("请求的签名不正确");//请求的签名不正确
            echo json_encode(array(
                "status"=>500,
                "msg"=>"请求的签名不正确",
            ),JSON_UNESCAPED_UNICODE);
            exit;
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
        $api_log = new\app\common\model\Apilog;
        $data_log['api_url'] = $url;
        $data_log['request_parameter'] = $data_list;
        $data_log['response_data'] = $response_data;
        $data_log['api_time'] = date('Y-m-d H:i:s');
        $data_log['ip'] = get_client_ip();
        $api_log->save($data_log);
    }
    //同步用户信息接口
    public function syncUserInfo(){
        //先进行验证
        $this->checkAdminSession();
        //获取POST方法传过来的数据
        $postStr = file_get_contents("php://input");
        //对数据进行json化
        $data = json_decode($postStr);
        $uer  = new \app\common\model\User;
        $role = new \app\common\model\Role;
        //判断当前数据是否为用户，用户数据则选择录入
        if ($data->msg->business == 'user') {
            if ($data->msg->post == 'M'){
                $data_temp['Username']      = $data->msg->data->username;
                $data_temp['outer_user_id'] = $data->msg->data->id;
                $data_temp['name']          = $data->msg->data->name;
                $data_temp['Roleid']        = 17;
                $data_temp['Password']      = 'jsl123456';//初始密码
                //用户权限
                $data_temp['Competence']    = $role->where(['ID'=>$data_temp['Roleid']])->value('Competence');
                $where['outer_user_id']     = $data->msg->data->id;
                $error_msg = $uer->where($where)->find();
                if (empty($error_msg)) {
                    if ($uer->save($data_temp)){
                        echo json_encode(array(
                            "status"=>200,
                            "msg"=>"操作成功"
                        ),JSON_UNESCAPED_UNICODE);
                        exit();
                    }else{
                        echo json_encode(array(
                            "status"=>500,
                            "msg"=>"保存失败"
                        ),JSON_UNESCAPED_UNICODE);
                        exit();
                    }
                }else{
                    try {
                        $uer->save($data_temp,$where);
                        echo json_encode(array(
                            "status"=>200,
                            "msg"=>"该数据已更新"
                        ),JSON_UNESCAPED_UNICODE);
                        exit();
                    }catch (\Exception $exception){
                        echo json_encode(array(
                            "status"=>200,
                            "msg"=>"该数据未发生变化"
                        ),JSON_UNESCAPED_UNICODE);
                        exit();
                    }
                }
            }elseif ($data->msg->post == 'D'){
                $where['outer_user_id']     = $data->msg->data->id;
                $error_msg = $uer->where($where)->find();
                if (empty($error_msg)){
                    echo json_encode(array(
                        "status"=>200,
                        "msg"=>"要删除的数据不存在"
                    ),JSON_UNESCAPED_UNICODE);
                    exit();
                }else{
                    if ($uer->where($where)->delete()){
                        echo json_encode(array(
                            "status"=>200,
                            "msg"=>"删除数据成功"
                        ),JSON_UNESCAPED_UNICODE);
                        exit();
                    }else{
                        echo json_encode(array(
                            "status"=>500,
                            "msg"=>"删除数据失败"
                        ),JSON_UNESCAPED_UNICODE);
                        exit();
                    }
                }
            }
        }else {
            echo json_encode(array(
                "status"=>200,
                "msg"=>"非用户数据不录入"
            ),JSON_UNESCAPED_UNICODE);
            exit();
        }
    }
    //获取ticket
    public function getTicket(){
        //验签
        $this->getTicketSession();
        $ticket     = input('get.ticket');
        $timestamp  = $this->getMillisecond();
        $retrun_msg = $this->syncTicket($ticket,$timestamp);
        if ($retrun_msg->status == 200){
            $data = $retrun_msg->data;
            $where['outer_user_id'] = $data->userId;
            $where['Status'] = 0;
            $user = new \app\common\model\User;
            $user_info = $user->where($where)->find();
            if (empty($user_info)){
                //如果用户信息不存在
                $this->redirect('admin/Publics/index');//重定向到错误页面
            }else {
                $token = $data->token;
                $user_info['Logintime']  = time();
                $user_info['token']      = $token;
                $user_info['ticket']     = $ticket;
                $user_info['Logincount'] = $user_info['Logincount']+1;
                session('ThinkUser',$user_info);
                //销毁验证码session
                session('verify',null);
                $this->redirect('admin/Index/web');//重定向到首页
            }
        }else {
           echo json_encode(array(
               "status"=>500,
               "msg"=>$retrun_msg->msg,
           ),JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    public function getMillisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }
    public function syncTicket($ticket,$timestamp)
    {
        $appCode = 'jifenshop';
        $secret  = '021c8f81b0e34d9ab2513807ac7d1590';
        $data = array(
            'ticket'=>$ticket,
            'appCode'=>$appCode,
            'sign'=>md5($appCode.$secret.'$'.$ticket.'&'.$timestamp),
            'timestamp'=> $timestamp,
        );
        $url = "http://app.gstest.pdmiryun.com/portal/serverApi/v2/checkTicket";
        $curl_url = $url;
        $ch = curl_init();
        ini_set("display_errors", "On");//打开错误提示
        ini_set("error_reporting", E_ALL);//显示所有错误
        curl_setopt($ch, CURLOPT_URL, $curl_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));//重点
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);//最大执行时间
        $curl_result = curl_exec($ch);
        curl_close($ch);
        return json_decode($curl_result);
    }
    public function reshToken($token,$ticket,$userID){
        $token_data = $this->sso_reshToken($token,$ticket,$userID);
        if ($token_data->status == 200){
            $data = $token_data->data;
            Session::set('ThinkUser.token',$data);
        }else {
            echo json_encode(array(
                "status"=>500,
                "msg"=>$token_data->msg,
            ),JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    //刷新TOKEN
    public function sso_reshToken($token,$ticket,$userID){
        $token1 = json_encode($token);
        $timestamp = $this->getMillisecond();
        $param = array(
            'ticket'=>$ticket,
            'sign'=>md5('$'.$ticket.'$'.$token1.'$'.$userID.'&'.$timestamp),
            'timestamp' => $timestamp,
        );
        $url = "http://app.gstest.pdmiryun.com/portal/serverApi/v2/refreshToken";
        $curl_url = $url . "?" . http_build_query($param);
        $ch = curl_init();
        ini_set("display_errors", "On");//打开错误提示
        ini_set("error_reporting", E_ALL);//显示所有错误
        curl_setopt($ch, CURLOPT_URL, $curl_url);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);//在尝试连接时等待的秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);//最大执行时间
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
//        curl_setopt ($ch, CURLOPT_HEADER, 0); //头文件信息做数据流输出
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
        $curl_result = curl_exec($ch);
        curl_close($ch);
        return json_decode($curl_result);
    }
}