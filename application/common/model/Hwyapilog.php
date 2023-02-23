<?php
namespace app\common\model;
class Hwyapilog extends \think\Model {
	//关联查询
	protected $name = 'hwy_api_log';
	protected $insert = ['api_time'];  
	protected function setApiTimeAttr()
	{
		return date("Y-m-d H:i:s");
	}
}
?>