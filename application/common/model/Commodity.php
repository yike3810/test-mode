<?php
namespace app\common\model;
use phpDocumentor\Reflection\Types\Object_;
use think\Db;
use think\Model;

class Commodity extends \think\Model
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

}
