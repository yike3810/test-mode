<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Url;

class Address extends Admin
{
    public function manageaddress()
    {
        $address = new \app\common\model\Address;
        $region = new \app\common\model\Region;
        $goods_num = input('goods_num');//此状态值用于判断订单页和管理地址页
        $goods_id = input('goods_id');//此状态值用于判断订单页和管理地址页
        $member_id = $this->member_info['member_id'];
        $where['member_id'] = $member_id;
        $where['is_default'] = 1;
        $address_info = $address->where($where)->find();//获取默认地址
        $address_info = empty($address_info) ? ['address_id' => 0] : $address_info;
        //判断是否为方正客户端打开
        $client_status = $this->member_info['client_status'];
        $this->assign('ios_status',$this->ios_status);
        $this->assign('client_status', $client_status);
        $this->assign('address_info', $address_info);
        $this->assign('goods_id', $goods_id);
        $this->assign('goods_num', $goods_num);
        return $this->fetch();
    }

    public function getDZlist()
    {
        $member_id = $this->member_info['member_id'];
        $address = new \app\common\model\Address;
        $list = $this->getAddressList($member_id);
        $where['member_id'] = $member_id;
        $where['is_default'] = 1;
        $address_info = $address->where($where)->find();//获取默认地址
        $address_info = empty($address_info) ? ['address_id' => 0] : $address_info;
        $data = array(
            'list' => array_values($list),
            'address_info' => $address_info,

        );
        echo json_encode($data);
        exit;
    }

    public function getAddressList($member_id = '')
    {
        //获取地址列表
        $address = new \app\common\model\Address;
        $region = new \app\common\model\Region;
        $basemodel = new \app\common\model\BaseModel;
        $address = $address->where(['member_id' => $member_id])->order("is_default DESC,address_id DESC ")->column('');
        if (!empty($address)) {
            foreach ($address as $k => $v) {
                $list[] = [
                    'id' => $v['address_id'],
                    'name' => $basemodel->sm4_decrypt($v['consignee']),
                    'tel' => $basemodel->sm4_decrypt($v['phone']),
                    'address' => $region->where(['region_id' => $v['province_id']])->value('region_name')
                        . $region->where(['region_id' => $v['city_id']])->value('region_name')
                        . $region->where(['region_id' => $v['district_id']])->value('region_name') . ' '
                        . $basemodel->sm4_decrypt($v['address']),
                    'isDefault' => $v['is_default'] == 1 ? 'true' : 'false',
                ];
            }
        } else {
            $list = [];
        }
        return $list;
    }

    public function add()
    {
        $region = new \app\common\model\Region;
        $regionlist_info = $region->where(['parent_id' => 0])->column('');
        foreach ($regionlist_info as $k => $v) {
            $regionlist[] = [
                'text' => $v['region_name'],
                'value' => $v['region_id'],
                'children' => $this->getClassnamestr($v['region_id']),
            ];
        }
//        $regionlist = $this->getClassnamestr();
        //判断是否为方正客户端打开
        $client_status = $this->member_info['client_status'];
        $this->assign('ios_status',$this->ios_status);
        $this->assign('client_status', $client_status);
        $this->assign('regionlist', json_encode($regionlist));
        return $this->fetch();
    }

    public function add_do()
    {
        $address = new \app\common\model\Address;
        $data['member_id'] = $this->member_info['member_id'];
        $data['consignee'] = input('consignee');
        $data['province_id'] = input('province_id');
        $data['city_id'] = input('city_id');
        $data['district_id'] = input('district_id');
        $data['address'] = input('address');
        $data['phone'] = input('phone');
        $data['is_default'] = input('is_default');
        //判断该地址是否存在
        $addrerss_info = $address->where(['member_id' => $data['member_id'], 'address' => $data['address'], 'phone' => $data['phone']])->find();
        if ($addrerss_info) {
            $array = array("status" => 0, "data" => "", "message" => "该地址已存在");
            echo json_encode($array);
            exit;
        }
        if ($data['is_default'] == 1 && $address->where(['member_id' => $data['member_id'], 'is_default' => 1])->find()) {
            $array = array("status" => 0, "data" => "", "message" => "默认地址只能有一个");
            echo json_encode($array);
            exit;
        } else {
            if ($address->save($data)) {
                $array = array("status" => 1, "data" => "", "message" => "保存成功");
                echo json_encode($array);
                exit;
            } else {
                $array = array("status" => 0, "data" => "", "message" => "保存失败");
                echo json_encode($array);
                exit;
            }
        }
    }

    public function edit()
    {
        $region = new \app\common\model\Region;
        $address = new \app\common\model\Address;
        $address_id = input('address_id');
        $address_info = $address->where(['address_id' => $address_id])->find();
        $address_info['fieldValue'] = $region->where(['region_id' => $address_info['province_id']])->value('region_name') . '/'
            . $region->where(['region_id' => $address_info['city_id']])->value('region_name') . '/'
            . $region->where(['region_id' => $address_info['district_id']])->value('region_name');
        $address_info['is_default'] = $address_info['is_default'] == 1 ? 'true' : 'false';
        $regionlist_info = $region->where(['parent_id' => 0])->column('');
        foreach ($regionlist_info as $k => $v) {
            $regionlist[] = [
                'text' => $v['region_name'],
                'value' => $v['region_id'],
                'children' => $this->getClassnamestr($v['region_id']),
            ];
        }
        //判断是否为方正客户端打开
        $client_status = $this->member_info['client_status'];
        $this->assign('ios_status',$this->ios_status);
        $this->assign('client_status', $client_status);
        $this->assign('regionlist', json_encode($regionlist));
        $this->assign('address_info', $address_info);
        return $this->fetch();
    }

    public function edit_do()
    {
        $address = new \app\common\model\Address;
        $address_id = input('address_id');
        $data['consignee'] = input('consignee');
        $data['province_id'] = input('province_id');
        $data['city_id'] = input('city_id');
        $data['district_id'] = input('district_id');
        $data['address'] = input('address');
        $data['phone'] = input('phone');
        $data['is_default'] = input('is_default');
        //判断该地址是否存在
        $addrerss_info = $address->where(['member_id' => $data['member_id'], 'address' => $data['address'], 'phone' => $data['phone']])->find();
        if ($addrerss_info) {
            $array = array("status" => 0, "data" => "", "message" => "该地址已存在");
            echo json_encode($array);
            exit;
        }
        if ($data['is_default'] == 1 && $address->where(['member_id' => $data['member_id'], 'is_default' => 1])->find()) {
            $array = array("status" => 0, "data" => "", "message" => "默认地址只能有一个");
            echo json_encode($array);
            exit;
        } else {
            if ($address->save($data, ["address_id" => $address_id])) {
                $array = array("status" => 1, "data" => "", "message" => "修改成功");
                echo json_encode($array);
                exit;
            } else {
                $array = array("status" => 0, "data" => "", "message" => "修改失败");
                echo json_encode($array);
                exit;
            }
        }
    }

    public function getClassnamestr($parent_id)
    {
        $region = new \app\common\model\Region;
        //递归方法
        $relist = $region->where(['parent_id' => $parent_id])->column('');
        $list = array();
        foreach ($relist as $k => $v) {
//            $relist = $region->where(['parent_id' => $v['region_id']])->column('');
            if ($v['region_type'] <= 2) {
                $list[] = [
                    'text' => $v['region_name'],
                    'value' => $v['region_id'],
                    'children' => $this->getClassnamestr($v['region_id']),
                ];
            } else {
                $list[] = [
                    'text' => $v['region_name'],
                    'value' => $v['region_id'],
                ];
            }
        }
        //普通写法
//        $prolist = $region->where(['parent_id'=>0])->column('');
//        foreach ($prolist as $k=>$v){
//            $citylist = $region->where(['parent_id'=>$v['region_id']])->column('');
//            foreach ($citylist as $m=>$n){
//                $dislist = $region->where(['parent_id'=>$n['region_id']])->column('');
//                foreach ($dislist as $x=>$y){
//                    $arr[] = [
//                        'text'    => $y['region_name'],
//                        'value'   => $y['region_id'],
//                    ];                }
//                $array[] = [
//                    'text'    => $n['region_name'],
//                    'value'   => $n['region_id'],
//                    'children'=> $arr,
//                ];
//            }
//            $list[] = [
//                'text'    => $v['region_name'],
//                'value'   => $v['region_id'],
//                'children'=> $array,
//            ];
//
//        }
//        a($list);
        return $list;
    }

    public function defaultaddress()
    {
        $address = new \app\common\model\Address;
        $member_id = $this->member_info['member_id'];
        $address_id = input('address_id');
        $address_info = $address->where(['address_id' => $address_id])->find();
        if (!empty($address_info)) {
            if ($address_info['is_default'] == 0) {
                $address_old = $address->where(['member_id' => $member_id, 'is_default' => 1])->find();
                $address->save(['is_default' => 1], ['address_id' => $address_id]);
                if (!empty($address_old)) {
                    $address->save(['is_default' => 0], ['address_id' => $address_old['address_id']]);
                }
                $array = array("status" => 1, "data" => '', "message" => "默认地址设置成功");
                echo json_encode($array);
                exit;

            } else {
                $address->save(['is_default' => 0], ['address_id' => $address_id]);
                $array = array("status" => 1, "data" => '', "message" => "默认地址取消成功");
                echo json_encode($array);
                exit;
            }
        } else {
            $array = array("status" => 0, "data" => "", "message" => "数据异常");
            echo json_encode($array);
            exit;
        }
    }

    public function deladdress()
    {
        $address_id = input('address_id');
        $address = new \app\common\model\Address;
        $require = $address->where(['address_id' => $address_id])->delete();
        if ($require) {
            $msg = ['status' => 1, 'data' => '', "msg" => "删除成功"];
            echo json_encode($msg);
            exit();
        } else {
            $msg = ['status' => 0, 'data' => '', "msg" => "删除失败"];
            echo json_encode($msg);
            exit();
        }
    }
}
