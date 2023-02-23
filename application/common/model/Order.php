<?php

namespace app\common\model;

use phpDocumentor\Reflection\Types\Object_;
use think\Db;
use think\Model;

class Order extends \think\Model
{

    /**
     * 存储数据时自动完成加密 - 手机号
     * @param $value
     * @return false|string
     */
    protected function setPhoneAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_encrypt($value);
    }

    /**
     * 查询数据时自动完成解密 - 手机号
     * @param $value
     * @return false|string
     */
    protected function getPhoneAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_decrypt($value);
    }
    /**
     * 存储数据时自动完成加密 - 兑换会员名
     * @param $value
     * @return false|string
     */
    protected function setMemberNameAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_encrypt($value);
    }

    /**
     * 查询数据时自动完成解密 - 兑换会员名
     * @param $value
     * @return false|string
     */
    protected function getMemberNameAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_decrypt($value);
    }

    /**
     * 存储数据时自动完成加密 - 收货人
     * @param $value
     * @return false|string
     */
    protected function setConsigneeAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_encrypt($value);
    }

    /**
     * 查询数据时自动完成解密 - 收货人
     * @param $value
     * @return false|string
     */
    protected function getConsigneeAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_decrypt($value);
    }

    /**
     * 存储数据时自动完成加密 - 收货人
     * @param $value
     * @return false|string
     */
    protected function setAddressAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_encrypt($value);
    }

    /**
     * 查询数据时自动完成解密 - 收货人
     * @param $value
     * @return false|string
     */
    protected function getAddressAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_decrypt($value);
    }
    //扣积分接口
    public function getDefuctPoints($params)
    {
        $appKey = config('jsl.app_key');
        $appSecret = config('jsl.app_secret');
        $timestamp = time();
        $param = [
            'timestamp' => $timestamp,
            'uid' => $params['uid'],
            'orderNum' => $params['orderNum'],
            'credits' => $params['credits'],
            'ip' => $params['ip'],
            'actualPrice' => $params['actualPrice'],
            'facePrice' => $params['facePrice'],
            'description' => $params['description'],
            'type' => $params['type'],
            'appKey' => $appKey,
        ];
        $sign_param = $param;
        $sign_param['appSecret'] = $appSecret;
        $sign = $this->sign($sign_param);
        $param['sign'] = $sign;
//        $url = "https://wan-jcgs.newgsclouds.com/v1/amuc/api/duiba/creditConsume/1";
        $consume_url = config('jsl.app_name');
        $url = "https://wan-".$consume_url.".newgsclouds.com/v1/amuc/api/duiba/creditConsume/1";
        $curl_url = $url . "?" . http_build_query($param);
        $ch = curl_init();
        ini_set("display_errors", "On");//打开错误提示
        ini_set("error_reporting", E_ALL);//显示所有错误
        curl_setopt($ch, CURLOPT_URL, $curl_url);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
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
        $api_log_deduct_points = new Apilogdeductpoints;
        $log_data = array();
        $log_data['api_url'] = $url;
        $log_data['request_parameter'] = $api_log_deduct_points . json_encode($params, JSON_UNESCAPED_UNICODE);
        $log_data['response_data'] = $curl_result;
        $log_data['api_time'] = date('Y-m-d H:i:s');
        $log_data['ip'] = get_client_ip();
        $api_log_deduct_points->save($log_data);
        $result = preg_replace('/\'/', '"', $curl_result);
        return json_decode($result);
    }

    /*
   *  md5签名，$array中务必包含 appSecret
   */
    function sign($array)
    {
        ksort($array);//数组升序排序
        $string = "";
        while (list($key, $val) = each($array)) {
            $string = $string . $val;
        }
        return md5($string);
    }

    //积分兑换结果通知接口
    public function getExchangeResultPoints($param = array())
    {
        $appKey = config('jsl.app_key');
        $appSecret = config('jsl.app_secret');
        $timestamp = time();
        $params = [
            'timestamp' => $timestamp,
            'uid' => $param['uid'],
            'orderNum' => $param['orderNum'],
            'success' => $param['success'],
            'appKey' => $appKey,
            'bizId' => $param['bizId'],
        ];
        $sign_param = $params;
        $sign_param['appSecret'] = $appSecret;
        $sign = $this->sign($sign_param);//生成签名
        $params['sign'] = $sign;
        $consume_url = config('jsl.app_name');
        $url = "https://wan-".$consume_url.".newgsclouds.com/v1/amuc/api/duiba/creditNotify/1";
//        $url = "https://wan-jcgs.newgsclouds.com/v1/amuc/api/duiba/creditNotify/1";
        $curl_url = $url . "?" . http_build_query($params);
        $ch = curl_init();
        ini_set("display_errors", "On");//打开错误提示
        ini_set("error_reporting", E_ALL);//显示所有错误
        curl_setopt($ch, CURLOPT_URL, $curl_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);//在尝试连接时等待的秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
//        curl_setopt ($ch, CURLOPT_HEADER, 0); //头文件信息做数据流输出
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
        $curl_result = curl_exec($ch);
        curl_close($ch);
        $api_log_exchange_result_notification = new Apilogexchangeresultnotification;
        $log_data = array();
        $log_data['api_url'] = $url;
        $log_data['request_parameter'] = $api_log_exchange_result_notification . json_encode($param, JSON_UNESCAPED_UNICODE);
        $log_data['response_data'] = $curl_result;
        $log_data['api_time'] = date('Y-m-d H:i:s');
        $log_data['ip'] = get_client_ip();
        $api_log_exchange_result_notification->save($log_data);
        $result = preg_replace('/\'/', '"', $curl_result);//接口返回结果’变”
        return json_decode($result);
    }

    public function submitExchange($param)
    {
        //积分兑换
        $pointsgoods = new Pointsgoods;
        $member = new Member;
        $ordergoods = new Ordergoods;
        $data = array();
        $data['province_id'] = $param['province_id'];
        $data['city_id'] = $param['city_id'];
        $data['district_id'] = $param['district_id'];
        $data['address'] = $param['address'];
        $data['phone'] = $param['phone'];
        $data['order_sn'] = $this->ordersntest();//随机生成订单号
        $data['all_point'] = $param['all_point'];
        $data['consignee'] = $param['consignee'];
        $data['member_id'] = $param['member_id'];
        $data['member_name'] = $param['member_name'];
        $data['order_status'] = 0;//订单状态
        $data['pay_status'] = 1;//支付状态
        $data['shipping_status'] = 0;//发货状态
        $member_points = $member->where(['member_id' => $data['member_id']])->value('member_points');
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['pay_time'] = date('Y-m-d H:i:s');
        $goods_id = $param['goods_id'];
        $goods_num = $param['goods_num'];
        $pointsgoods_info = $pointsgoods->where(['goods_id' => $goods_id])->find();
        $goods_name = $pointsgoods->where(['goods_id' => $goods_id])->value('goods_name');
        if ($data['province_id'] == '' || $data['city_id'] == '' || $data['district_id'] == '' || $data['address'] == '') {
            return array("status" => 0, "data" => "", "message" => "请选择收货地址！");
        }
        if ($data['phone'] == '' || $data['consignee'] == '') {
            return array("status" => 0, "data" => "", "message" => "请填写收货人信息！");
        }
        if ($pointsgoods_info['goods_islimit'] == 1) {
            if ($goods_num > $pointsgoods_info['goods_limitnum']) {
                //判断该订单商品兑换数量是否超过最大限制数量
                return array("status" => 0, "data" => "", "message" => "超出该商品最大限制兑换数量！");
            }
            $order_list = $this->where(['member_id' => $data['member_id']])->column('');
            $goodsorder_num = 0;
            foreach ($order_list as $k => $v) {
                $goodsorder_num += $ordergoods->where(['order_id' => $v['order_id'], 'goods_id' => $goods_id])->value('goods_num');
            }
            if (($goodsorder_num + $goods_num) > $pointsgoods_info['goods_limitnum']) {
                //判断该订单商品数量与已下单该商品数量是否超过商品限制总数
                return array("status" => 0, "data" => "", "message" => "超出该商品最大限制兑换数量！");
            }
        }
        if ($member_points < $data['all_point']) {
            //判断当前登录积分与商品兑换积分大小
            return array("status" => 0, "data" => "", "message" => "积分不足！");
        }
        if ($this->save($data)) {
            $cfg = config('jsl.newgscloud_type');
            if ($cfg == 'fz'){
                $where['credits'] = $data['all_point'];
                $where['uid'] = $param['outer_member_id'];
                $where['description'] = "兑换" . $goods_name . '商品' . $goods_num . '件';
                $where['orderNum'] = $this->order_sn;
                $where['ip'] = get_client_ip();
                $where['facePrice'] = 0;
                $where['actualPrice'] = 0;
                $where['type'] = "实物";
                //获取扣积分接口(方正)
                $pointsresult = $this->getDefuctPoints($where);
                if (empty($pointsresult)) {
                    $this->save(['deduct_points_api_status' => 0, 'order_status' => 8], ['order_id' => $this->order_id]);
                    return array("status" => 0, "data" => "", "message" => "扣积分接口连接失败");
                }
                if ($pointsresult->status != 'ok') {
                    $this->save(['deduct_points_api_status' => 1, 'order_status' => 8], ['order_id' => $this->order_id]);
                    return array("status" => 0, "data" => "", "message" => "扣积分接口连接失败");
                }
                $this->save(['deduct_points_api_status' => 10, 'biz_id' => $pointsresult->bizId], ['order_id' => $this->order_id]);
            }elseif ($cfg == 'rb'){
                $where['actualPrice'] = $data['all_point'];
                $where['uid'] = $param['outer_member_id'];
                $where['description'] = "兑换" . $goods_name . '商品' . $goods_num . '件';
                $where['orderNum'] = $this->order_sn;
                //获取扣积分接口(人报)
                $pointsresult = $this->getintegralpoints($where);
                if (empty($pointsresult)) {
                    $this->save(['deduct_points_api_status' => 0, 'order_status' => 8], ['order_id' => $this->order_id]);
                    return array("status" => 0, "data" => "", "message" => "扣积分接口连接失败");
                }
                if ($pointsresult->status != '200') {
                    $this->save(['deduct_points_api_status' => 1, 'order_status' => 8], ['order_id' => $this->order_id]);
                    return array("status" => 0, "data" => "", "message" => "扣积分接口连接失败");
                }
                $this->save(['deduct_points_api_status' => 10, 'biz_id' => $pointsresult->bizId], ['order_id' => $this->order_id]);
                $member->save(['member_points' => $pointsresult->credits],['outer_member_id' => $param['outer_member_id']]);
            }

            //兑换商品订单保存
            $array = array();
            $array['order_id'] = $this->order_id;
            $array['goods_id'] = $goods_id;
            $array['goods_name'] = $goods_name;
            $array['goods_points'] = input('all_point');
            $array['goods_num'] = $goods_num;
            if ($ordergoods->save($array)
                && $pointsgoods->save(['goods_storage' => $pointsgoods_info['goods_storage'] - $goods_num], ['goods_id' => $goods_id])) {
                $memberAfpoints = $member_points - $data['all_point'];
                if ($memberAfpoints != $member_points) {
                    $member->save(['member_points' => $member_points - $data['all_point']], ['member_id' => $data['member_id']]);
                }
                return array("status" => 1, "data" => "", "message" => "兑换成功");
            } else {
                return array("status" => 0, "data" => "", "message" => "兑换失败");
            }
        } else {
            return array("status" => 0, "data" => "", "message" => "兑换失败");
        }
    }

    public function exchange($member_code, $barcode)
    {
        $member = new \app\common\model\Member;
        $points_goods = new \app\common\model\Pointsgoods;
        $points_log = new \app\common\model\Pointslog;
        $order = new \app\common\model\Order;
        $order_goods = new \app\common\model\Ordergoods;
        $member_array['id_number|phone'] = $member_code;
        $member_info = $member->where($member_array)->find();
        if (empty($member_info)) {
            return array("status" => 0, message => "志愿者信息不存在！");
        }
        $goods_array['barcode'] = $barcode;
        $points_goods_info = $points_goods->where($goods_array)->find();
        if (empty($points_goods_info)) {
            return array("status" => 0, message => "商品信息不存在！");
        }
        if ($points_goods_info['goods_points'] > $member_info['member_points']) {
            return array("status" => 0, message => "志愿者积分不足！");
        }
        //积分扣减
        db('member')->where('member_id', $member_info['member_id'])->
        setDec('member_points', $points_goods_info['goods_points']);
        //商品库存扣减
        db('points_goods')->where('goods_id', $points_goods_info['goods_id'])->
        setDec('goods_storage', 1);
        //订单入库
        $order_data = $order_goods_data = array();
        $order_data['order_sn'] = $this->createOrdersn();
        $order_data['member_id'] = $member_info['member_id'];
        $order_data['member_name'] = $member_info['member_name'];
        $order_data['add_time'] = date("Y-m-d H:i:s");
        $order_data['all_point'] = $points_goods_info['goods_points'] * 1;
        $order_data['phone'] = $member_info['phone'];
        $order_data['store_id'] = session('Store.store_id');
        $order->save($order_data);
        //订单商品入库
        $order_goods_data['order_id'] = $order->order_id;
        $order_goods_data['goods_id'] = $points_goods_info['goods_id'];
        $order_goods_data['goods_name'] = $points_goods_info['goods_name'];
        $order_goods_data['goods_points'] = $points_goods_info['goods_points'] * 1;
        $order_goods_data['goods_num'] = 1;
        $order_goods->save($order_goods_data);
        //积分变动日志
        $points_log_data = array();
        $points_log_data['member_id'] = $member_info['member_id'];
        $points_log_data['member_name'] = $member_info['member_name'];
        $points_log_data['points'] = -$points_goods_info['goods_points'];
        $points_log_data['add_time'] = date("Y-m-d H:i:s");
        $points_log_data['desc'] = "兑换商品 {$points_goods_info['goods_name']} 一件";
        $points_log_data['stage'] = 'exchange';
        $points_log->save($points_log_data);
        return array("status" => 1, message => "");

    }

    public function ordersntest()
    {
        //订单编号校验
        $order = new \app\common\model\Order;
        $orderNum = date('Ymd') . rand(100000, 999999);
        $testOrder = $order->where(['order_sn' => $orderNum])->find();
        if ($testOrder) {
            return $this->ordersntest();
        } else {
            return $orderNum;
        }
    }

    public function createOrdersn()
    {
        $order = new \app\common\model\Order;
        $sn = '';
        while ($sn == "") {
            $sn = date("Ymd") . str_shuffle(rand(100000, 999999));
            $order_info = $order->where(["order_sn" =>$sn])->value("order_sn");
            if (!empty($order_info)) {
                $sn = '';
            }
        }
        return $sn;
    }

    public function orderGoods()
    {
        return $this->hasOne('Ordergoods', 'order_id');
    }

    public function member()
    {
        return $this->hasOne('Member', 'member_id', 'member_id');
    }

    public function store()
    {
        return $this->hasOne('Store', 'store_id', 'store_id');
    }

    public function getOrderGoodsList($param = array())
    {

        $where = "  ";
        $page_size = 1;

        if ($param['add_time_start'] != "" && $param['add_time_end'] != "") {
            $where .= " AND o.add_time >='{$param['add_time_start']}' AND  o.add_time <='{$param['add_time_end']}'";
        } else {
            if ($param['add_time_start'] != "") {
                $where .= " AND o.add_time >='{$param['add_time_start']}' ";
            }
            if ($param['add_time_end'] != "") {
                $where .= " AND o.add_time <='{$param['add_time_end']}' ";
            }
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page = input('param.page');
        if ($param['list_rows'] != "") {
            $page_config['list_rows'] = $param['list_rows'];
        }
        if ($param['store_id'] != "") {
            $where .= " AND o.store_id <='{$param['store_id']}' ";
        }
        if ($page >= 2) {
            $start_page = ($page - 1) * $page_config['list_rows'];
        } else {
            $start_page = 0;
        }
        $sql = "SELECT count(*) AS t FROM {$database_prefix}order AS o ,
					 {$database_prefix}order_goods AS og,
					 {$database_prefix}points_goods AS g
		WHERE o.order_id = og.order_id AND og.goods_id = g.goods_id {$where}  GROUP BY og.goods_id";
        $total = Db::query($sql);
        $sql = "SELECT s.store_name,og.goods_name,og.goods_points,SUM(og.goods_num) AS s_goods_num,SUM(og.goods_points*og.goods_num) AS s_goods_points  
				FROM {$database_prefix}order AS o ,
					 {$database_prefix}order_goods AS og,
					 {$database_prefix}points_goods AS g,
					 {$database_prefix}store AS s
		WHERE o.order_id = og.order_id AND og.goods_id = g.goods_id
		AND o.store_id = s.store_id  {$where}  GROUP BY og.goods_id";
        $sql .= " LIMIT $start_page,{$page_config['list_rows']}";
        $order_goods_list = Db::query($sql);
        return array("total" => $total[0]['t'], "data" => $order_goods_list);
    }

    public $order_status = array(
        0 => "待审核",
        1 => "已审核",
        8 => "异常",
        9 => "已取消",
        10 => "已完成",
    );
    public $pay_status = array(
        0 => "未付款",
        1 => "已付款",
    );
    public $shipping_status = array(
        0 => "未发货",
        1 => "已发货",
    );

    public function AutoShipping()
    {
        //当发货事件大于等于15天时将状态改为已收货
        $now_time = time();
        //order_status = 10表示已完成
        $data = array();
        $list = $this->where("order_status=1 AND shipping_status=1")->column('');
        foreach ($list as $k => $v) {
            //strtotime() 将时间转换为时间戳
            $add_time = strtotime($list[$k]['add_time']);//订单生成时间
            $shipping_time = strtotime($list[$k]['shipping_time']);//发货时间
            //如果订单生成时间和发货时间都大于等于15天，订单自动完成
            if ((intval(($now_time-$add_time)/86400)>=15)&&(intval(($now_time-$shipping_time)/86400)>=15)){
                $data_temp['order_status'] = 10;
                $data_temp['order_id'] = $v['order_id'];
                array_push($data, $data_temp);
            }
        }
        if ($this->saveAll($data)) {
            return json_encode(array(
                'code' => 0,
                'msg' => '保存成功'
            ));
        } else {
            return json_encode(array(
                'code' => 1,
                'msg' => '保存失败'
            ));
        }

    }

    //实物兑换结果通知接口方法调用
    public function getexchangepointsresult()
    {
        $list = $this->where('deduct_points_api_status = 10 and order_status = 10')->column('');
        $cfg = config('jsl.newgscloud_type');
        if(!empty($list)){
            foreach ($list as $k => $v) {
                $ordergoods = new Ordergoods;
                $pointsgoods = new Pointsgoods;
                $member = new Member;
                $goods_id = $ordergoods->where(['order_id' => $v['order_id']])->value('goods_id');
                $type = $pointsgoods->where(['goods_id' => $goods_id])->value('type');
                if($type == 1){
                    if ($cfg == 'fz'){
                        $params = array(
                            'uid' => $member -> where(['member_id'=>$v['member_id']])->value('outer_member_id'),
                            'credits' => $v['all_point'],
                            'success' => 'true',
                            'orderNum' => $v['order_sn'],
                            'bizId' => $v['biz_id'],
                        );
                        //获取积分兑换成功通知接口(方正)
                        $exchangepointsresult = $this->getExchangeResultPoints($params);
                    }elseif ($cfg == 'rb'){
                        $params = array(
                            'status' => 'ok',
                            'bizId' => $v['biz_id'],
                        );
                        //获取积分兑换成功通知接口(人报)
                        $exchangepointsresult = $this->getResultPoints($params);
                    }
                    if (empty($exchangepointsresult)) {
                        $this->save(['deduct_points_api_status' => 0, 'order_status' => 8,], ['order_id' => $v['order_id']]);
                        return array("status" => 0, "data" => "", "message" => "兑换结果通知接口连接失败");
                    }
                    if ($exchangepointsresult->status != 'ok') {
                        $this->save(['exchange_result_notification_status' => 1, 'order_status' => 8], ['order_id' => $v['order_id']]);
                        return array("status" => 0, "data" => "", "message" => "兑换结果通知接口连接失败");
                    }
                    $this->save(['exchange_result_notification_status' => 10], ['order_id' => $v['order_id']]);
                }
            }
        }
    }

    //获取token接口（get）
    public function gettoken($timestamp)
    {
        $appId = config('jsl.app_key');
        $appSecret = config('jsl.app_secret');
        $param = array(
            'sign'=>md5($appId.$appSecret."$".$timestamp),
            'appId' => $appId,
            'timestamp' => $timestamp,
        );
        $url = "https://xgs.newgscloud.com/thirdServiceApi/api/third/getToken";
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

    //人报扣积分接口（post）
    public function getintegralpoints($params)
    {
        $appId = config('jsl.app_key');
        $appSecret = config('jsl.app_secret');
        $timestamp = round(microtime(true)*1000);//获取毫秒时间戳
        $token = $this->gettoken($timestamp)->token;
        $sign = md5($token.$appId.$appSecret."$".$timestamp);
        $param = array(
            'siteId'=>config('jsl.site_id'),//站点ID固定值
            'actualPrice'=>$params['actualPrice'],//消耗的积分
            'uid'=>$params['uid'],//用户ID
            'orderNum'=>$params['orderNum'],//商城订单号
            "description"=>$params['description'],//订单描述
            'source'=>'shop'//积分变更来源
        );
        $headers = array(
            "Content-Type:application/json",
            "appId:".$appId,
            "timestamp:".$timestamp,
            "token:".$token,
            "sign:".$sign
        );
        $url = "https://xgs.newgscloud.com/thirdServiceApi/integralshop/api/consumepoins";
//        $post_data = http_build_query($param);
        $post_data = json_encode($param);
        $ch = curl_init();
        ini_set("display_errors", "On");//打开错误提示
        ini_set("error_reporting", E_ALL);//显示所有错误
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);//最大执行时间
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);//在尝试连接时等待的秒数
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
//        curl_setopt ($ch, CURLOPT_HEADER, 0); //头文件信息做数据流输出
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
        $curl_result = curl_exec($ch);
        curl_close($ch);
        $api_log_deduct_points = new Apilogdeductpoints;
        $log_data = array();
        $log_data['api_url'] = $url;
        $log_data['request_parameter'] = $api_log_deduct_points . json_encode($params, JSON_UNESCAPED_UNICODE);
        $log_data['response_data'] = $curl_result;
        $log_data['api_time'] = date('Y-m-d H:i:s');
        $log_data['ip'] = get_client_ip();
        $api_log_deduct_points->save($log_data);
        $result = preg_replace('/\'/', '"', $curl_result);
        return json_decode($curl_result);
    }

    //积分兑换结果通知接口(post)
    public function getResultPoints($param)
    {
        $appId = config('jsl.app_key');
        $appSecret = config('jsl.app_secret');
        $timestamp = round(microtime(true)*1000);
        $token = $this->gettoken($timestamp)->token;
        $sign = md5($token.$appId.$appSecret."$".$timestamp);
        $params = [
            'siteId'=>config('jsl.site_id'),
            'status'=>$param['status'],
            'bizId'=>$param['bizId']
        ];
        $headers = array(
            "Content-Type:application/json",
            "appId:$appId",
            "timestamp:$timestamp",
            "token:$token",
            "sign:$sign"
        );
        $url = "https://xgs.newgscloud.com/thirdServiceApi/integralshop/api/callback";
//        $post_data = http_build_query($params);
        $post_data = json_encode($params);
        $ch = curl_init();
        ini_set("display_errors", "On");//打开错误提示
        ini_set("error_reporting", E_ALL);//显示所有错误
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);//在尝试连接时等待的秒数
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
//        curl_setopt ($ch, CURLOPT_HEADER, 0); //头文件信息做数据流输出
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
        $curl_result = curl_exec($ch);
        curl_close($ch);
        $api_log_exchange_result_notification = new Apilogexchangeresultnotification;
        $log_data = array();
        $log_data['api_url'] = $url;
        $log_data['request_parameter'] = $api_log_exchange_result_notification . json_encode($param, JSON_UNESCAPED_UNICODE);
        $log_data['response_data'] = $curl_result;
        $log_data['api_time'] = date('Y-m-d H:i:s');
        $log_data['ip'] = get_client_ip();
        $api_log_exchange_result_notification->save($log_data);
        $result = preg_replace('/\'/', '"', $curl_result);//接口返回结果’变”
        return json_decode($result);
    }
}
