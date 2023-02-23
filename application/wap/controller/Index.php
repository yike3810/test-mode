<?php

namespace app\wap\controller;

use app\common\model\Status;
use think\cache\driver\Redis;
use think\Controller;
use think\Exception;
use think\Request;
use think\Url;

class Index extends Controller
{

    public function index()
    {
        return $this->fetch();
    }

    public function getgeneratecouponlist()
    {
        $member_id = 359;
        $page = input('page');
        if (!is_numeric($page)) {
            $this->error("参数不正确！");
        }
        $coupon = new \app\common\model\Coupon;
        $generatecoupon = new \app\common\model\Generatecoupon;
        $commodity = new \app\common\model\Commodity;
//        $where['member_id'] = $this->member_info['member_id'];
        $coupon_list = array_values($generatecoupon->order('receive_ended_at desc')->page($page, 10)->column(''));

        foreach ($coupon_list as $k => $v) {
//            rule格式转化
            $coupon_list[$k]['rule'] = json_decode($v['rule']);
//            时间格式转化
            $coupon_list[$k]['use_started_at'] = date("y.m.d", strtotime($v['use_started_at']));;
            $coupon_list[$k]['use_ended_at'] = date("y.m.d", strtotime($v['use_ended_at']));;
            //       是否抢光
            if ($coupon_list[$k]['coupon_num'] > 0) {
                $coupon_list[$k]['is_have'] = 1;
                //        是否领取
                $where = array();
                $where['member_id'] = $member_id;
                $where['log_id'] = $coupon_list[$k]['id'];
                $member_info = $coupon->where($where)->find();
                if (!empty($member_info)) {
                    $coupon_list[$k]['is_received'] = 1;
                } else {
                    $coupon_list[$k]['is_received'] = 0;
                }
            } else {
                $coupon_list[$k]['is_have'] = 0;
            }
        }
        echo json_encode($coupon_list);
        exit();
    }


    public function aaaa()
    {
        $coupon = new \app\common\model\Coupon;
        $generatecoupon = new \app\common\model\Generatecoupon;
        // 获取数据
        $gid = input("gid");

        // 链接redis
        $redis = new Redis();
        $lock_name = "lock_" . $gid; // 锁
        $count_name = "count_" . $gid; // 总数
        $coupon_ids_name = "coupon_ids_" . $gid; // 剩余券码

        // 判断redis是否正常
        if ($redis->has($count_name) && $redis->get($coupon_ids_name)) {
            // 链接数据库，查询数据并放到redis

            $redis->set($count_name, 12312);
            $redis->set($coupon_ids_name, ["000000", "000001",]);
        }

        // 获取锁
        while (!$redis->setlock($lock_name)) {
            sleep(0);
        }

        //提取数据
        $count = $redis->get($count_name);

        // 判断数据
        if ($count <= 0) {
            // 解锁并返回数据
            $redis->unlock($lock_name);
            return json_encode(["code" => "0009", "msg" => "消费券已经被抢完", "data" => []]);
        }
        $g_find = $generatecoupon->where(["id" => $gid])->find();
        $now_time = date("Y-m-d H:i:s");
        // 判断当前时间是否在开放领取时间内
        if ($now_time < $g_find["receive_started_at"] ||  $g_find["receive_ended_at"]<$now_time) {
            // 解锁并返回数据
            $redis->unlock($lock_name);
            return json_encode(["code" => "0002", "msg" => "该消费券还未开放领取", "data" => []]);
        }
        // 判断是否在领取时间内


        $count -= 1;
        $c_ids = $redis->get($coupon_ids_name);
        $coupon_code = array_shift($c_ids);
        try {
            // 存库
            $data = array();
            $data["coupon_status"] = 1;
            $data["member_id"] = $this->memberinfo["member_id"];
            $data["receive_time"] = date("Y-m-d H:i:s");
            if ($coupon->save($data, ["coupon_code" => $coupon_code, "member_id" => ""])) {
                // redis重新存入扣除之后的数据
                $redis->set($coupon_ids_name, $c_ids);
                $redis->set($count_name, $count);

                // 解锁并返回数据
                $redis->unlock($lock_name);
                return json_encode(["code" => "0000", "msg" => "Success", "data" => []]);
            } else {
                $redis->unlock($lock_name);
                return json_encode(["code" => "0001", "msg" => "保存失败", "data" => []]);
            }
        } catch (Exception $e) {
            $redis->unlock($lock_name);
            return json_encode(["code" => "1000", "msg" => "错误", "data" => []]);
        }
    }
}