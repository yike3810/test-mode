<?php
namespace app\common\model;
class Store extends \think\Model {
	
	public $store_state_array =	array(
		0 =>"关闭",
		1 =>"开启",
	);
	public $store_recommend_array =	array(
		0 =>"未推荐",
		1 =>"已推荐",
	);
	public function member()
	{
		return $this->hasOne('Member','member_id','member_id');
	}
}
