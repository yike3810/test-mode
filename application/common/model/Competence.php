<?php
namespace app\common\model;
class Competence extends \think\Model {
	//自动完成
	protected $auto = ['Dtime'];
	//添加当前时间
	protected function setDtimeAttr() {
		return date('Y-m-d H:i:s');
	}
}
