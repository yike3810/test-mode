<?php
namespace app\common\model;
class Dmenu extends \think\Model
{
	//自动完成
	protected $_auto = array ( 
		array('Dtime','Dtime',1,'callback'),
		array('Uid','uid',3,'callback'),
	);
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
	//管理员ID
	protected function uid() {
		return $_SESSION['ThinkUser']['ID'];
	}
}
