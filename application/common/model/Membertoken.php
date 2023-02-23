<?php
namespace app\common\model;
use think\Db;
class Membertoken extends \think\Model {
	protected $name = 'member_token';
	
	public function getMemberTokenInfoByToken($key){
		$result = $this->where("token = '{$key}'")->find();
		return $result;
	}
	public function delMemberToken($key,$client_type){
		$result = $this->where("token = '{$key}' AND client_type='{$client_type}'")->delete();
		return $result;
	}
}
