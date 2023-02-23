<?php
namespace app\common\model;
class Memberservice extends \think\Model {
    protected $name = 'member_service';
	//自动验证
	protected $_validate = array(
		
	);
	//自动完成
	protected $_auto = array ( 
		array('add_time','Dtime',1,'callback'),
	);
	protected $auto    = [];

	protected $update  = [];
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
}
