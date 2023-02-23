<?php
namespace app\common\model;
class Exam extends \think\Model {
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
        $exam_info = $this->where("exam_id='{$exam_id}'")->find();
        $sum_question_num 	    = $question->where("exam_id='{$exam_id}'")->count();
        if($exam_info['exam_type'] == 1){
            $sum_exam_points 	= $question->where("exam_id='{$exam_id}'")->sum("question_points");
            $this->save(array("sum_exam_points"=>$sum_exam_points,"sum_question_num"=>$sum_question_num),"exam_id='{$exam_id}'");
        }else if($exam_info['exam_type'] == 2){
            $question_info 	    = $question->where("exam_id='{$exam_id}'")->find();
            $sum_exam_points    = $question_info['question_points'] * $exam_info['random_number'];
            $this->save(array("sum_exam_points"=>$sum_exam_points,"sum_question_num"=>$sum_question_num),"exam_id='{$exam_id}'");
        }
    }
}
