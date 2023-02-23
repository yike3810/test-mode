<?php
//考试管理类
namespace app\exam\controller;
use think\Controller;
use think\Request;
use think\Url;
class Question extends Admin {
	public function index() {
		parent::userauth2(233);
        $exam_type         	= input('request.exam_type');
        $exam_id        	= input('request.exam_id');
        $keywords        	= input('request.keywords');
        $where = array();
        if($exam_id!=""){
        	$where['exam_id'] = $exam_id;
        }
        if($keywords!=""){
        	$where['exam_name|exam_function'] = array("LIKE","%$keywords%");
        }
        $exam=new \app\common\model\Exam;
        $dmenu = new \app\common\model\Dmenu;
        $user = new \app\common\model\User;
        $department = new \app\common\model\Department;
        $member = new \app\common\model\Member;
        $question = new \app\common\model\Question;
        $questiondetail = new \app\common\model\Questiondetail;
        $question_list = $question->where($where)->order("sort ASC")->column('');
        foreach ($question_list as $k=>$v){
        	$question_list[$k]['detail_list'] = $questiondetail->where("question_id='{$v['question_id']}'")->order("question_detail_id ASC")->column('');
		}
        //查出相应数据
        $exam = new \app\common\model\Exam;
        $result = $exam->where("exam_id = '{$exam_id}' ")->find();
        $this->assign('question_list',$question_list);
        $this->assign('exam',$exam);
        $this->assign('question',$question);
        $this->assign('result',$result);
        return $this->fetch();
	}
	//导入
	public function questionimport() {
		parent::win_userauth(233);
		$dmenu = new \app\common\model\Dmenu;
		$volist=$dmenu->where('Sid <> 0')->order('Sortid asc')->select();
		$this->assign('volist',$volist);
		return $this->fetch();
	}
	//导入处理
	public function questionimport_do() {
		//验证用户权限
		parent::win_userauth(233);
		if (request()->isPost()) {
			$cont=array();
			\think\Loader::import('PHPExcel.PHPExcel');
			\think\Loader::import('PHPExcel.PHPExcel.IOFactory');
			$PHPExcel  = new \PHPExcel();
			$PHPReader = new \PHPExcel_Reader_Excel2007();
			
			$name      = $_FILES['question_info']['tmp_name'];
			if(!$PHPReader->canRead($name)){
				$PHPReader = new \PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($name)){
					return array('status'=>0,'message'=>'导入失败','data'=>'');
				}
			}
			$PHPExcel     = $PHPReader->load($name);
			$currentSheet = $PHPExcel->getSheet(0);
			$highestRow   = $currentSheet->getHighestRow();
			$contents     = array();
			$dmenu =   new \app\common\model\Dmenu;
			$exam_id      = input('exam_id');
			for($row=2;$row<=$highestRow;$row++){
				$data = array();
				$data['question_type_name'] 	= trim($currentSheet->getCellByColumnAndRow(0, $row)->getValue());
				$data['question_title']       	= trim($currentSheet->getCellByColumnAndRow(1, $row)->getValue());
				$data['question_detail_1']      = trim($currentSheet->getCellByColumnAndRow(2, $row)->getValue());
				$data['question_detail_2']      = trim($currentSheet->getCellByColumnAndRow(3, $row)->getValue());
				$data['question_detail_3']      = trim($currentSheet->getCellByColumnAndRow(4, $row)->getValue());
				$data['question_detail_4']      = trim($currentSheet->getCellByColumnAndRow(5, $row)->getValue());
				$data['question_detail_5']      = trim($currentSheet->getCellByColumnAndRow(6, $row)->getValue());
				$data['correct_answer_str']     = trim($currentSheet->getCellByColumnAndRow(7, $row)->getValue());
				$data['answer_analysis']      	= trim($currentSheet->getCellByColumnAndRow(8, $row)->getValue());
				$data['question_points']      	= trim($currentSheet->getCellByColumnAndRow(9, $row)->getValue());
				
				if($data['question_type_name'] =="")continue;
				$contents[] = $data;
			}
			$question=new \app\common\model\Question;
			$question->where("exam_id='{$exam_id}'")->delete();
			foreach ($contents as $k=>$v){
				$question=new \app\common\model\Question;
				$cli = array();
				$cli['question_title'] 		= $v['question_title'];
				$cli['question_type'] 		= $this->getQuestionType($v['question_type_name']);
				//$cli['correct_answer'] 		= $v['correct_answer'];
				$cli['answer_analysis'] 	= $v['answer_analysis'];
				$cli['question_points'] 	= $v['question_points'];
				$cli['exam_id'] 			= $exam_id;
				$cli['add_user'] 			= session('ThinkUser.ID');
				$cli['add_time'] 			= date("Y-m-d H:i:s");
				$cli['sort'] 				= $k+1;
				if ($question->save($cli)) {
					parent::operating(request()->path(),0,'导入考题：'.$cli['question_title']);
				}else {
					parent::operating(request()->path(),1,'导入失败：'.$question->getError());
					$this->error($client->getError());
				}
				$datail_data = array();
				$correct_answer = '';
				for ($i=1;$i<=5;$i++){
					if($v["question_detail_{$i}"]!=""){
						$str = explode("、", $v["question_detail_{$i}"],2);
						if($str[0]!="" && $str[1]!=""){
							$datail_data = array(
									'question_id'=>$question->question_id,
									'question_options_flag'=>$str[0],
									'question_options_content'=>$str[1],
							);
							$questiondetail=new \app\common\model\Questiondetail;
							$questiondetail->save($datail_data);
							if($cli['question_type'] == 1){
								if($str[0] == $v['correct_answer_str']){
									$correct_answer = $questiondetail->question_detail_id;
								}
							}elseif ($cli['question_type'] == 2){
								if(strpos($v['correct_answer_str'],$str[0]) !== false){
									$correct_answer .= $questiondetail->question_detail_id.",";
								}
							}elseif ($cli['question_type'] == 3){
								if($str[0] == $v['correct_answer_str']){
									$correct_answer = $questiondetail->question_detail_id;
								}
							}elseif ($cli['question_type'] == 4){

							}elseif ($cli['question_type'] == 5){
								
							}
						}
					}
				}
				$correct_answer = trim($correct_answer,",");
				$question->save(array("correct_answer"=>$correct_answer),"question_id='{$question->question_id}'");
			}
			$exam 	=   new \app\common\model\Exam;
			$exam->updateExamInfo($exam_id);
			$this->success('导入成功',url('Question/questionimport'),3);
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//导入
	public function questiontenimport() {
		parent::win_userauth(233);
		$dmenu = new \app\common\model\Dmenu;
		$volist=$dmenu->where('Sid <> 0')->order('Sortid asc')->select();
		$this->assign('volist',$volist);
		return $this->fetch();
	}
	//导入处理
	public function questiontenimport_do() {
		//验证用户权限
		parent::win_userauth(233);
		if (request()->isPost()) {
			$cont=array();
			\think\Loader::import('PHPExcel.PHPExcel');
			\think\Loader::import('PHPExcel.PHPExcel.IOFactory');
			$PHPExcel  = new \PHPExcel();
			$PHPReader = new \PHPExcel_Reader_Excel2007();
			
			$name      = $_FILES['question_info']['tmp_name'];
			if(!$PHPReader->canRead($name)){
				$PHPReader = new \PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($name)){
					return array('status'=>0,'message'=>'导入失败','data'=>'');
				}
			}
			$PHPExcel     = $PHPReader->load($name);
			$currentSheet = $PHPExcel->getSheet(0);
			$highestRow   = $currentSheet->getHighestRow();
			$contents     = array();
			$dmenu =   new \app\common\model\Dmenu;
			$exam_id      = input('exam_id');
			for($row=2;$row<=$highestRow;$row++){
				$data = array();
				$data['question_type_name'] 	= trim($currentSheet->getCellByColumnAndRow(0, $row)->getValue());
				$data['question_title']       	= trim($currentSheet->getCellByColumnAndRow(1, $row)->getValue());
				$data['question_detail_1']      = trim($currentSheet->getCellByColumnAndRow(2, $row)->getValue());
				$data['question_detail_2']      = trim($currentSheet->getCellByColumnAndRow(3, $row)->getValue());
				$data['question_detail_3']      = trim($currentSheet->getCellByColumnAndRow(4, $row)->getValue());
				$data['question_detail_4']      = trim($currentSheet->getCellByColumnAndRow(5, $row)->getValue());
				$data['question_detail_5']      = trim($currentSheet->getCellByColumnAndRow(6, $row)->getValue());
				$data['question_detail_6']      = trim($currentSheet->getCellByColumnAndRow(7, $row)->getValue());
				$data['question_detail_7']      = trim($currentSheet->getCellByColumnAndRow(8, $row)->getValue());
				$data['question_detail_8']      = trim($currentSheet->getCellByColumnAndRow(9, $row)->getValue());
				$data['question_detail_9']      = trim($currentSheet->getCellByColumnAndRow(10, $row)->getValue());
				$data['question_detail_10']     = trim($currentSheet->getCellByColumnAndRow(11, $row)->getValue());
				$data['correct_answer_str']     = trim($currentSheet->getCellByColumnAndRow(12, $row)->getValue());
				$data['answer_analysis']      	= trim($currentSheet->getCellByColumnAndRow(13, $row)->getValue());
				$data['question_points']      	= trim($currentSheet->getCellByColumnAndRow(14, $row)->getValue());
				
				if($data['question_type_name'] =="")continue;
				$contents[] = $data;
			}
			$question=new \app\common\model\Question;
			$question->where("exam_id='{$exam_id}'")->delete();
			foreach ($contents as $k=>$v){
				$question=new \app\common\model\Question;
				$cli = array();
				$cli['question_title'] 		= $v['question_title'];
				$cli['question_type'] 		= $this->getQuestionType($v['question_type_name']);
				//$cli['correct_answer'] 		= $v['correct_answer'];
				$cli['answer_analysis'] 	= $v['answer_analysis'];
				$cli['question_points'] 	= $v['question_points'];
				$cli['exam_id'] 			= $exam_id;
				$cli['add_user'] 			= session('ThinkUser.ID');
				$cli['add_time'] 			= date("Y-m-d H:i:s");
				$cli['sort'] 				= $k+1;
				if ($question->save($cli)) {
					parent::operating(request()->path(),0,'导入考题：'.$cli['question_title']);
				}else {
					parent::operating(request()->path(),1,'导入失败：'.$question->getError());
					$this->error($client->getError());
				}
				$datail_data = array();
				$correct_answer = '';
				for ($i=1;$i<=10;$i++){
					if($v["question_detail_{$i}"]!=""){
						$str = explode("、", $v["question_detail_{$i}"],2);
						if($str[0]!="" && $str[1]!=""){
							$datail_data = array(
									'question_id'=>$question->question_id,
									'question_options_flag'=>$str[0],
									'question_options_content'=>$str[1],
							);
							$questiondetail=new \app\common\model\Questiondetail;
							$questiondetail->save($datail_data);
							if($cli['question_type'] == 1){
								if($str[0] == $v['correct_answer_str']){
									$correct_answer = $questiondetail->question_detail_id;
								}
							}elseif ($cli['question_type'] == 2){
								if(strpos($v['correct_answer_str'],$str[0]) !== false){
									$correct_answer .= $questiondetail->question_detail_id.",";
								}
							}elseif ($cli['question_type'] == 3){
								if($str[0] == $v['correct_answer_str']){
									$correct_answer = $questiondetail->question_detail_id;
								}
							}elseif ($cli['question_type'] == 4){

							}elseif ($cli['question_type'] == 5){
								
							}
						}
					}
				}
				$correct_answer = trim($correct_answer,",");
				$question->save(array("correct_answer"=>$correct_answer),"question_id='{$question->question_id}'");
			}
			$exam 	=   new \app\common\model\Exam;
			$exam->updateExamInfo($exam_id);
			$this->success('导入成功',url('Question/questionimport'),3);
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	function getQuestionType($str){
		if(preg_match('/单选题/', $str)){
			return 1;
		}elseif (preg_match('/多选题/', $str)){
			return 2;
		}elseif (preg_match('/判断题/', $str)){
			return 3;
		}elseif (preg_match('/填空题/', $str)){
			return 4;
		}elseif (preg_match('/简答题/', $str)){
			return 5;
		}
	}
	//新增考试
	public function add() {
		parent::win_userauth(233);
		$dmenu = new \app\common\model\Dmenu;
		$user  = new \app\common\model\User;
		$exam  = new \app\common\model\Exam;
		$department  = new \app\common\model\Department;
        $parent_id   = input('request.parent_id');
		$dmenu_list=$dmenu->where('Sid <>0')->order('Sortid asc')->select();
		$this->assign('dmenu_list',$dmenu_list);
		$user_list=$user->where('Status = 0')->order('ID ASC')->select();
		$this->assign('user_list',$user_list);
		$department_list=$department->where('parent_id = 0')->order('id ASC')->select();
		$this->assign('department_list',$department_list);
		return $this->fetch();
	}
	//添加处理
	public function add_do() {
		//验证用户权限
		parent::win_userauth(233);
		if (request()->isPost()) {
			$data	=	$member_data	= array();
			$data['exam_id']    		= input('post.exam_id');
			$data['question_title']    	= input('post.question_title');
			$data['question_type']      = input('post.question_type');
			$data['question_points']   	= input('post.question_points');
// 			$data['answer_analysis']   	= input('post.answer_analysis');
			$data['sort']  				= input('post.sort');
			$question_options  			= input('question_options/a');
			$data['add_user']   		= session('ThinkUser.ID');
			$data['add_time']   		= date("Y-m-d H:i:s");
			$question					=   new \app\common\model\Question;
			$member 					=   new \app\common\model\Member;
			$exam 					=   new \app\common\model\Exam;
			$question_info = $question->where("question_title = '{$data['question_title']}' AND exam_id='{$data['exam_id']}'")->find();
			if(!empty($question_info)){
				$this->error("该考试题目已存在!");
			}
			try{
				$question->save($data);
				$exam->updateExamInfo($data['exam_id']);
				$new_data = array();
				$correct_answer = '';
				$data['correct_answer_str'] = input('post.correct_answer');
				foreach ($question_options as $k=>$v){
					if($v !=""){
						$str = explode("、", $v,2);
						$new_data = array("question_id"=>$question->question_id,"question_options_flag"=>$str[0],"question_options_content"=>$str[1]);
						$questiondetail   =   new \app\common\model\Questiondetail;
						$questiondetail->save($new_data);
						if($data['question_type'] == 1){
							if($str[0] == $data['correct_answer_str']){
								$correct_answer = $questiondetail->question_detail_id;
							}
						}elseif ($data['question_type'] == 2){
							if(strpos($data['correct_answer_str'],$str[0]) !== false){
							$correct_answer .= $questiondetail->question_detail_id.",";
						}
						}elseif ($data['question_type'] == 3){
							if($str[0] == $data['correct_answer_str']){
								$correct_answer = $questiondetail->question_detail_id;
							}
						}elseif ($data['question_type'] == 4){
							
						}elseif ($data['question_type'] == 5){
							
						}
					}
				}
				$correct_answer = trim($correct_answer,",");
				$question->save(array("correct_answer"=>$correct_answer),"question_id='{$question->question_id}'");
				$exam->updateExamInfo($data['exam_id']);
				parent::operating(request()->path(),0,'添加考试题目资料：'.$data['question_title']);
			}catch(\Exception $e){
				$this->error($e->getMessage());
				parent::operating(request()->path(),1,'添加失败：'.$data['question_title']);
			}
			$this->success('考试题目添加成功',url('Question/edit','question_id='.$question->question_id));
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//修改考试资料
	public function edit() {
		parent::win_userauth(233);
		$question_id = input('param.question_id');
		if ($question_id=='' || !is_numeric($question_id)) {
			parent::operating(request()->path(),1,'参数错误');
			$this->error('参数ID类型错误，请关闭本窗口');
		}
		$uid = session('ThinkUser.ID');
		//下拉菜单数据
		$dmenu = new \app\common\model\Dmenu;
		$department  = new \app\common\model\Department;
		$member  = new \app\common\model\Member;
		$dmenu_list=$dmenu->where('Sid <> 0')->order('Sortid asc')->select();
		//查出相应数据
		$question = new \app\common\model\Question;
		$questiondetail = new \app\common\model\Questiondetail;
		$result = $question->where("question_id = '{$question_id}' ")->find();
		if($result['correct_answer'] !=""){
			$result['correct_answer_str'] = $questiondetail->where("question_id = '{$question_id}' AND question_detail_id IN({$result['correct_answer']}) ")
			->group('question_id')->value("group_concat(question_options_flag  separator '')");
		}else{
			$result['correct_answer_str'] = '';
		}
		if (!$result) {
			parent::operating(request()->path(),1,'没有找到数据：'.$question_id);
			$this->error('不存在你要修改的数据，请关闭本窗口');
		}
		$detail_list = $questiondetail->where("question_id = '{$question_id}' ")->column('');
		if(count($detail_list)<10){
			$diff_detail_list = range(1,10-count($detail_list));
		}
		$this->assign('dmenu_list',$dmenu_list);
		$this->assign('result',$result);
		$this->assign('detail_list',$detail_list);
		$this->assign('diff_detail_list',$diff_detail_list);
		return $this->fetch();
	}
	//修改考试资料处理
	public function edit_do() {
		//验证用户权限
		parent::win_userauth(233);
		if (request()->isPost()) {
			$data=array();
			//考试资料信息
			$data['question_id']        = input('post.question_id');
			$data['question_title']    	= input('post.question_title');
			$data['question_type']      = input('post.question_type');
			$data['question_points']   	= input('post.question_points');
			$data['answer_analysis']   	= input('post.answer_analysis');
			$data['sort']  				= input('post.sort');
			$question_options  			= input('question_options/a');
			$question	=   new \app\common\model\Question;
			$question_detail	=   new \app\common\model\Questiondetail;
			$member 	=   new \app\common\model\Member;
			$exam 	=   new \app\common\model\Exam;
			$question_data = $question->where("question_id = '{$data['question_id']}'")->find();
			$question_info = $question->where("question_title = '{$data['question_title']}' AND question_id != '{$data['question_id']}' AND exam_id = '{$question_data['exam_id']}'")->find();
			if(!empty($question_info)){
			    $this->error("该考试题目已存在!");
			}
			try{
				$question->save($data,"question_id='{$data['question_id']}'");
				$question_detail->where("question_id='{$data['question_id']}'")->delete();
				$new_data = array();
				$data['correct_answer_str'] = input('post.correct_answer');
				foreach ($question_options as $k=>$v){
					if($v !=""){
						$str = explode("、", $v,2);
						$question_detail	=   new \app\common\model\Questiondetail;
						$new_data = array("question_id"=>$data['question_id'],"question_options_flag"=>$str[0],"question_options_content"=>$str[1]);
						$question_detail->save($new_data);
						if($data['question_type'] == 1){
							if($str[0] == $data['correct_answer_str']){
								$correct_answer = $question_detail->question_detail_id;
							}
						}elseif ($data['question_type'] == 2){
							if(strpos($data['correct_answer_str'],$str[0]) !== false){
								$correct_answer .= $question_detail->question_detail_id.",";
							}
						}elseif ($data['question_type'] == 3){
							if($str[0] == $data['correct_answer_str']){
								$correct_answer = $question_detail->question_detail_id;
							}
						}elseif ($data['question_type'] == 4){
							
						}elseif ($data['question_type'] == 5){
							
						}
					}
				}
				$correct_answer = trim($correct_answer,",");
				$question->save(array("correct_answer"=>$correct_answer),"question_id='{$data['question_id']}'");
				$question_info = $question->where("question_id = '{$data['question_id']}'")->find();
				$exam->updateExamInfo($question_info['exam_id']);
				parent::operating(request()->path(),0,'更新考试题目资料：'.$data['question_title']);
			}catch(\Exception $e){
				$this->error($e->getMessage());
				parent::operating(request()->path(),1,'更新失败：'.$data['question_title']);
			}
			$this->success('考试题目更新成功',url('Question/edit','question_id='.$data['question_id']));
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//修改考试资料处理
	public function save_field_do() {
		//验证用户权限
		parent::win_userauth(233);
		if (request()->isPost()) {
			$data=array();
			//考试资料信息
			$data['question_id']        = input('post.question_id');
			$field    					= input('post.field');
			$value      				= input('post.value');
			$data[$field]				= $value;
			$question	=   new \app\common\model\Question;
			$question_detail	=   new \app\common\model\Questiondetail;
			$member 	=   new \app\common\model\Member;
			if($field == "question_title"){
				$question_info = $question->where("question_title = '{$value}' AND question_id != '{$data['question_id']}'")->find();
				if(!empty($question_info)){
					return array('s'=>"该考试题目已存在!");
				}
			}
			try{
				$question->save($data,"question_id='{$data['question_id']}'");
				parent::operating(request()->path(),0,'更新考试题目字段信息：'.$field);
			}catch(\Exception $e){
				$this->error($e->getMessage());
				parent::operating(request()->path(),1,'更新失败：'.$field);
			}
			return array('s'=>"ok");
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//删除考试资料到回收站
	public function del() {
		parent::userauth(233);
		//判断是否是ajax请求
		if (request()->isAjax()) {
			$question_id  =   input('post.question_id');
			if ($question_id=='' || !is_numeric($question_id)) {
				parent::operating(request()->path(),1,'参数错误');
				return array('s'=>'参数ID类型错误');
			}else {
				$question =   new \app\common\model\Question;
				$where  =   array('question_id'=>$question_id);
				$question_info = $question->where($where)->find();
				if ($question->where($where)->value('question_id')) {
					$question->where($where)->delete();
					$exam 	=   new \app\common\model\Exam;
					$exam->updateExamInfo($question_info['exam_id']);
                    parent::operating(request()->path(),0,'删除成功：'.$question_id);
					return array('s'=>'ok');
				}else {
					parent::operating(request()->path(),1,'删除失败：'.$this->getError());
					return array('s'=>$this->getError());
				}
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//批量删除考试资料到回收站
	public function exam_indel() {
		//验证用户权限
		parent::userauth(233);
		if (request()->isAjax()) {
			if (!$delid=explode(',',input('post.delid'))) {
				return array('s'=>'请选中后再删除');
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
            $exam  =   new \app\common\model\Exam();
			$map['id'] = array('in',$id);
			if ($exam->where($map)->delete()) {
				parent::operating(request()->path(),0,'删除成功：'.$id);
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'删除失败：'.$id);
				return array('s'=>'删除失败');
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//批量删除
	public function exam_c_indel() {
		//验证用户权限
		parent::userauth(233);
		if (request()->isAjax()) {
			if (!$delid=explode(',',input('post.delid'))) {
				return array('s'=>'请选中后再删除');
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$exam  =   new \app\common\model\Exam;
			if ($exam->where('id','IN',$id)->delete()) {
				parent::operating(request()->path(),0,'删除成功');
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'删除失败');
				return array('s'=>'删除失败');
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//下载文件
	public function down() {
		parent::userauth2(233);
	    $string = input('get.filename');
	    if (file_exists($string)) {
	        $string=iconv("utf-8","gb2312",$string);
	        header("Content-Type: application/force-download");
	        header("Content-Disposition: attachment; filename=".basename($string));
	        readfile($string);
	    }else {
	        parent::operating(request()->path(),1,'文件不存在：'.$string);
	        $this->error('文件不存在');
	    }
	}
	public function upload(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = request()->file('member_avatar');
	    //echo "<pre>";print_r($file);exit;
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS . 'member');
	    if($info){
	    	// 成功上传后 获取上传信息
	    	// 输出 jpg
	    	//echo $info->getExtension();
	    	// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
	    	//echo $info->getSaveName();
	    	// 输出 42a79759f284b767dfcb2a0197904287.jpg
	    	//echo $info->getFilename();
	    }else{
	    	// 上传失败获取错误信息
	    	//echo $file->getError();
	    	$this->error($file->getError());
	    }
	    return $info;
	}
	public function getDepartmentList(){
		$id = input('request.id');
		$department = new \app\common\model\Department;
		$where['parent_id'] = $id;
		$department_list = $department->where($where)->select();
		$status = 1; $html='';
		if(!empty($department_list)){
			foreach($department_list as $key=>$value){
				$html .= "<option value='{$value['id']}'>{$value['department_name']}</option>";
			}
			$message = '';
		}else{
			$status = -1;
			$message = '没有找到地址';
		}
		$region_list = array("status" => $status, "data" => $html, "message" => $message);
		echo json_encode($region_list);exit;
	}
	public function getTreelist($parent_id){
		$exam_tree_list = array();
		if(!$parent_id){
			$exam_tree_list[] = array('title'=>'新时代文明实践中心','spread'=>true);
		}
		$exam=new \app\common\model\Exam;
		$where['parent_id'] = $parent_id;
		$exam_list = $exam->where($where)->column('');
		foreach($exam_list as $k=>$v){
			$exam_children = array(
					'id'=>$v['id'],
					'title'=>$v['exam_name'],
					'spread'=>true,
					'children'=>$this->getTreelist($v['id']),
			);
			if(!$parent_id){
				$exam_tree_list[$parent_id]['children'][] = $exam_children;
			}else{
				$exam_tree_list[] = $exam_children;
			}
		}
		return $exam_tree_list;
	}
	public function getMemberList(){
		$member = new \app\common\model\Member;
		$where = array();
		$exam_id = input('request.exam_id');
		$limit = input('request.limit');
		$where['exam_id'] = $exam_id;
		$where['type'] = 1;
		$where['list_rows'] = $limit;
		$member_list = $member->getMemberList($where);
		$result = array("code"=>0,"count"=>$member_list['total'],"data"=>$member_list['data']);
		echo json_encode($result);exit;
	}
}
?>