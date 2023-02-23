<?php
namespace app\common\model;
use Cassandra\Date;

class Verification extends \think\Model
{
    protected $name = 'verification_code_queue';
    protected $auto = [];
    protected $insert = [];
    protected $update = [];
    //添加当前时间
    protected function Dtime()
    {
    	return date('Y-m-d H:i:s');
    }
    protected function setDtimeAttr()
    {
    	return date('Y-m-d H:i:s');
    }
    //验证和发送操作
    public function autoValidate($phone) {
//	    状态：1发送成功，2 发送失败，3，号码错误
        if ($phone!=''&!preg_match("/^1[3456789]{1}\d{9}$/",$phone)) {
            return 3;
        }else{
//           记录数据库内的状况 1:发送验证码 0：不发送验证码
//            $status = 1; //默认发送
//            $newest = $this->where(['phone'=>$tel])->order("id desc")->find();
            $member = new \app\common\model\Member;
            $member_info = $member->where("phone = '{$phone}'")->find();
            if(empty($member_info)){
                return 6;
            }
            if(!empty($member_info)&&$member_info['type'] != 0){
                return 4;
            }
            $date =date('Y-m-d');
            if ($this->where("phone = '{$phone}' and send_time >= '{$date}'")->count()>=10){
                return 5;
            }

            $data = array();
            $data['phone']                = $phone;
            $data['verification_code']    = rand(100000, 999999);
            $data['send_time']            =  $this->Dtime();
            $data['expired_time']         = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))+300);
            $data['send_status']          = '0';
//            发送短信后更新 $data['send_status'] | 0未发送，1发送成功，2发送失败
            $TEMPLATE_ID = '77bf9d58cb1a44349bc6166128ea0812'; //模板ID
            $receiver = $phone; //短信接收人号码
            /**
             * 选填,使用无变量模板时请赋空值 $TEMPLATE_PARAS = '';
             * 单变量模板示例:模板内容为"您的验证码是${NUM_6}"时,$TEMPLATE_PARAS可填写为'["369751"]'
             * 双变量模板示例:模板内容为"您有${NUM_2}件快递请到${TXT_32}领取"时,$TEMPLATE_PARAS可填写为'["3","人民公园正门"]'
             * 查看更多模板变量规则:常见问题>业务规则>短信模板内容审核标准
             * @var string $TEMPLATE_PARAS
             */
            $app_name = config('sms.app_name');
            $TEMPLATE_PARAS = "['{$data['verification_code']}']"; //模板变量，根据自身使用的模板，其值长度和个数与模板对应
            $status_arr = sendSms($receiver,$TEMPLATE_ID,$TEMPLATE_PARAS);
//            模拟提交
//             $data['msg_code'] ='000000';
//             $data['msg_description'] ='Success';

            $data['msg_code']    = $status_arr->code;
            $data['msg_description']    = $status_arr->description;

            if ($data['msg_code'] ='000000'){
                $data['send_status']    = '1';
            }else{
                $data['send_status']    = '2';
            }
            if ($this->data($data)) {
                $this->save();
            }
            //a($status_arr);
            return $data['send_status'] ;
        }
    }

    //登陆：验证和发送操作
    public function autoValidatelogin($phone) {
//	    状态：1发送成功，2 发送失败，3，号码错误
        if ($phone!=''&!preg_match("/^1[3456789]{1}\d{9}$/",$phone)) {
            return 3;
        }else{
//           记录数据库内的状况 1:发送验证码 0：不发送验证码
//            $status = 1; //默认发送
//            $newest = $this->where(['phone'=>$tel])->order("id desc")->find();
            $member = new \app\common\model\Member;
            $member_info = $member->where("phone = '{$phone}'")->find();
            if(empty($member_info)){
                return 6;
            }
            $date =date('Y-m-d');
            if ($this->where("phone = '{$phone}' and send_time >= '{$date}'")->count()>=10){
                return 5;
            }

            $data = array();
            $data['phone']                = $phone;
            $data['verification_code']    = rand(100000, 999999);
            $data['send_time']            =  $this->Dtime();
            $data['expired_time']         = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))+300);
            $data['send_status']          = '0';
//            发送短信后更新 $data['send_status'] | 0未发送，1发送成功，2发送失败
            $TEMPLATE_ID = '77bf9d58cb1a44349bc6166128ea0812'; //模板ID
            $receiver = $phone; //短信接收人号码
            /**
             * 选填,使用无变量模板时请赋空值 $TEMPLATE_PARAS = '';
             * 单变量模板示例:模板内容为"您的验证码是${NUM_6}"时,$TEMPLATE_PARAS可填写为'["369751"]'
             * 双变量模板示例:模板内容为"您有${NUM_2}件快递请到${TXT_32}领取"时,$TEMPLATE_PARAS可填写为'["3","人民公园正门"]'
             * 查看更多模板变量规则:常见问题>业务规则>短信模板内容审核标准
             * @var string $TEMPLATE_PARAS
             */
            $app_name = config('sms.app_name');
            $TEMPLATE_PARAS = "['{$data['verification_code']}']"; //模板变量，根据自身使用的模板，其值长度和个数与模板对应
            $status_arr = sendSms($receiver,$TEMPLATE_ID,$TEMPLATE_PARAS);
//            模拟提交
//             $data['msg_code'] ='000000';
//             $data['msg_description'] ='Success';

            $data['msg_code']    = $status_arr->code;
            $data['msg_description']    = $status_arr->description;

            if ($data['msg_code'] ='000000'){
                $data['send_status']    = '1';
            }else{
                $data['send_status']    = '2';
            }
            if ($this->data($data)) {
                $this->save();
            }
            //a($status_arr);
            return $data['send_status'] ;
        }
    }
}
