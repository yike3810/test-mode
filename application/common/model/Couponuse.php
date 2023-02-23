<?php

namespace app\common\model;

use phpDocumentor\Reflection\Types\Object_;
use think\Db;
use think\Model;

class Couponuse extends \think\Model
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



}
