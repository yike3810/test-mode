<?php
namespace app\common\model;
class Question extends \think\Model
{
	public $question_type_array=array(
		1=>"单选题",
		2=>"多选题",
		3=>"判断题",
		4=>"填空题",
		5=>"简答题"
	);
}
