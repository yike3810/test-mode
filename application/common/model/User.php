<?php
namespace app\common\model;
class User extends \think\Model {
	protected $auto    = [];
	protected $insert  = ['Dtime'];
	protected $update  = [];
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
	//新增时加密密码
	protected function sha1pow($Password) {
		return sha1(md5($Password));
	}
	//修改时加密密码
	protected function sha2pow($Password) {
		if ($Password!='') {
			return sha1(md5($Password));
		}else {
			return false;
		}
	}
	protected function setDtimeAttr()
	{
	    return date('Y-m-d H:i:s');
	}
	
	protected function setPasswordAttr($value)
	{
		if ($value!='') {
			return sha1(md5($value));
		}else {
			return false;
		}
	}


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

}
