<?php
namespace app\common\model;
class Examcount extends \think\Model {
    protected $name = "exam";
	public $exam_type_array = array(
			1=>"确定考题",
			2=>"按题型随机抽取",
			3=>"按题目随机",
			4=>"按分页随机",
	);
	public $exam_status_array = array(
			0=>"未发布",
			1=>"已发布",
	);
	public function updateExamInfo($exam_id){
		$question = new \app\common\model\Question;
		$sum_exam_points 	= $question->where("exam_id='{$exam_id}'")->sum("question_points");
		$sum_question_num 	= $question->where("exam_id='{$exam_id}'")->count();
		$this->save(array("sum_exam_points"=>$sum_exam_points,"sum_question_num"=>$sum_question_num),"exam_id='{$exam_id}'");
	}
}
