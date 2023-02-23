<?php
//考试管理类
namespace app\exam\controller;
use think\Controller;
use think\Request;
use think\Url;
class Answer extends Admin {
	public function index() {
		parent::userauth2(234);
		$exam_type       = input('request.exam_type');
		$parent_id       = input('request.parent_id');
		$keywords        = input('request.keywords');
		$exam_id         = input('request.exam_id');
		$where = array();
		if($exam_type!=""){
			$where['exam_type'] = array("LIKE","%$exam_type%");
		}else{
			
		}
		if($keywords!=""){
			$where['keywords'] = $keywords;
		}
		$where['exam_id'] = $exam_id;
		$exam=new \app\common\model\Exam;
		$dmenu = new \app\common\model\Dmenu;
		$user = new \app\common\model\User;
		$department = new \app\common\model\Department;
		$member = new \app\common\model\Member;
		$answer = new \app\common\model\Answer;
		/*$list = $answer->where($where)->paginate(null,false,array('query'=>$where));
		$answer_list = $list->toArray();
		foreach($answer_list['data'] as $k=>$v){
			//获取人员对应的职责信息
			$member_info = $member->where("member_id='{$v['member_id']}'")->find();
			$answer_list['data'][$k]['real_name'] = $member_info['real_name'];
			$answer_list['data'][$k]['id_number'] = $member_info['id_number'];
			$answer_list['data'][$k]['phone'] 	= $member_info['phone'];
			$department_info = $department->where("id='{$member_info['department_id']}'")->find();
			$answer_list['data'][$k]['department_name'] = $department_info['department_name'];
			if($department_info['parent_name'] !=''){
				$answer_list['data'][$k]['parent_name'] = $department_info['parent_name']."--";
			}else{
				$answer_list['data'][$k]['parent_name'] = '';
			}
		}*/
        $answer_list = $answer->getAnswerList($where);
        foreach($answer_list['data'] as $k=>$v) {
            if ($answer_list['data'][$k]['id_number']) {
                $answer_list['data'][$k]['id_number'] = substr_replace($answer_list['data'][$k]['id_number'], '**********', 6, 10);
            }
        }
        $list =  $answer->paginate(config('paginate.list_rows'),$answer_list['total'],array('query' => $where));
		$this->assign('answer_list',$answer_list);
		$this->assign('list',$list);
		$this->assign('exam',$exam);
		
		return $this->fetch();
	}
	public function info() {
		parent::userauth2(234);
        $exam_type         	= input('request.exam_type');
        $exam_id        	= input('request.exam_id');
        $answer_id        	= input('request.answer_id');
        $keywords        	= input('request.keywords');
        $where = array();
        if($exam_id!=""){
        	$where['exam_id'] = $exam_id;
        }
        if($keywords!=""){
        	$where['exam_name|exam_function'] = array("LIKE","%$keywords%");
        }
        $exam	=	new \app\common\model\Exam;
        $answer	=	new \app\common\model\Answer;
        $dmenu 	= 	new \app\common\model\Dmenu;
        $user 	= 	new \app\common\model\User;
        $department = new \app\common\model\Department;
        $member = new \app\common\model\Member;
        $question = new \app\common\model\Question;
        $questiondetail = new \app\common\model\Questiondetail;
        $answer_detail = new \app\common\model\Answerdetail;
        //查出相应数据
        $exam = new \app\common\model\Exam;
        $answer_info = $answer->where("answer_id = '{$answer_id}' ")->find();
        $exam_info 	 = $exam->where("exam_id = '{$answer_info['exam_id']}' ")->find();
        $question_list = $question->where("exam_id = '{$answer_info['exam_id']}'")->order("sort ASC")->column('');
        foreach ($question_list as $k=>$v){
        	$question_list[$k]['detail_list'] = $questiondetail->where("question_id='{$v['question_id']}'")->order("question_detail_id ASC")->column('');
        	$question_list[$k]['answer_detail_info'] = $answer_detail->where("question_id='{$v['question_id']}' AND answer_id='{$answer_id}'")->find();
        	if($question_list[$k]['answer_detail_info']['detail_content'] !=""){
        		$question_list[$k]['answer_detail_info']['detail_content_str'] = 
        		$questiondetail->where("question_id = '{$v['question_id']}' AND question_detail_id IN({$question_list[$k]['answer_detail_info']['detail_content']}) ")
	        	->group('question_id')->value("group_concat(question_options_flag  separator '')");
        	}else{
        		$result['correct_answer_str'] = '';
        	}
        }
        $this->assign('question_list',$question_list);
        $this->assign('exam',$exam);
        $this->assign('question',$question);
        $this->assign('exam_info',$exam_info);
        $this->assign('answer_info',$answer_info);
        return $this->fetch();
	}
	//导入
	public function questionimport() {
		//parent::win_userauth(122);
		$dmenu = new \app\common\model\Dmenu;
		$volist=$dmenu->where('Sid <> 0')->order('Sortid asc')->select();
		$this->assign('volist',$volist);
		return $this->fetch();
	}
	//导入处理
	public function questionimport_do() {
		//验证用户权限
		//parent::win_userauth(122);
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
				$data['correct_answer']        	= trim($currentSheet->getCellByColumnAndRow(7, $row)->getValue());
				$data['answer_analysis']      	= trim($currentSheet->getCellByColumnAndRow(8, $row)->getValue());
				$data['question_points']      	= trim($currentSheet->getCellByColumnAndRow(9, $row)->getValue());
				
				if($data['question_type_name'] =="")continue;
				$contents[] = $data;
			}
			$question=new \app\common\model\Question;
			$question->where("exam_id='{$exam_id}'")->delete();
			foreach ($contents as $k=>$v){
				$question=new \app\common\model\Question;
				$questiondetail=new \app\common\model\Questiondetail;
				$cli = array();
				$cli['question_title'] 		= $v['question_title'];
				$cli['question_type'] 		= $this->getQuestionType($v['question_type_name']);
				$cli['correct_answer'] 		= $v['correct_answer'];
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
				for ($i=1;$i<=5;$i++){
					if($v["question_detail_{$i}"]!=""){
						$str = explode("、", $v["question_detail_{$i}"],2);
						$datail_data[] = array(
								'question_id'=>$question->question_id,
								'question_options_flag'=>$str[0],
								'question_options_content'=>$str[1],
						);
					}
				}
				$questiondetail->saveAll($datail_data);
			}
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
		parent::win_userauth(128);
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
		//parent::win_userauth(128);
		if (request()->isPost()) {
			$data	=	$member_data	= array();
			$data['exam_id']    		= input('post.exam_id');
			$data['question_title']    	= input('post.question_title');
			$data['question_type']      = input('post.question_type');
			$data['question_points']   	= input('post.question_points');
// 			$data['answer_analysis']   	= input('post.answer_analysis');
			$data['correct_answer']   	= input('post.correct_answer');
			$data['sort']  				= input('post.sort');
			$question_options  			= input('question_options/a');
			$data['add_user']   		= session('ThinkUser.ID');
			$data['add_time']   		= date("Y-m-d H:i:s");
			$question					=   new \app\common\model\Question;
			$question_detail			=   new \app\common\model\Questiondetail;
			$member 					=   new \app\common\model\Member;
			$question_info = $question->where("question_title = '{$data['question_title']}' AND exam_id='{$data['exam_id']}'")->find();
			if(!empty($question_info)){
				$this->error("该考试题目已存在!");
			}
			try{
				$question->save($data);
				$new_data = array();
				foreach ($question_options as $k=>$v){
					if($v !=""){
						$str = explode("、", $v,2);
						$new_data[] = array("question_id"=>$question->question_id,"question_options_flag"=>$str[0],"question_options_content"=>$str[1]);
					}
				}
				$question_detail->saveAll($new_data);
				parent::operating(request()->path(),0,'添加考试题目资料：'.$data['question_title']);
			}catch(\Exception $e){
				$this->error($e->getMessage());
				parent::operating(request()->path(),1,'添加失败：'.$data['question_title']);
			}
			$this->success('考试题目添加成功',url('Question/edit','question_id='.$data['question_id']));
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//修改考试资料
	public function edit() {
		parent::win_userauth(129);
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
		if (!$result) {
			parent::operating(request()->path(),1,'没有找到数据：'.$question_id);
			$this->error('不存在你要修改的数据，请关闭本窗口');
		}
		$detail_list = $questiondetail->where("question_id = '{$question_id}' ")->column('');
		if(count($detail_list)<5){
			$diff_detail_list = range(1,5-count($detail_list));
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
		parent::win_userauth(129);
		if (request()->isPost()) {
			$data=array();
			//考试资料信息
			$data['question_id']        = input('post.question_id');
			$data['question_title']    	= input('post.question_title');
			$data['question_type']      = input('post.question_type');
			$data['question_points']   	= input('post.question_points');
			$data['answer_analysis']   	= input('post.answer_analysis');
			$data['correct_answer']   	= input('post.correct_answer');
			$data['sort']  				= input('post.sort');
			$question_options  			= input('question_options/a');
			$question	=   new \app\common\model\Question;
			$question_detail	=   new \app\common\model\Questiondetail;
			$member 	=   new \app\common\model\Member;
			$question_info = $question->where("question_title = '{$data['question_title']}' AND question_id != '{$data['question_id']}'")->find();
			if(!empty($question_info)){
			    $this->error("该考试题目已存在!");
			}
			$question_data = $question->where("question_id = '{$data['question_id']}'")->find();
			try{
				$question->save($data,"question_id='{$data['question_id']}'");
				$question_detail->where("question_id='{$data['question_id']}'")->delete();
				$new_data = array();
				foreach ($question_options as $k=>$v){
					if($v !=""){
						$str = explode("、", $v,2);
						$new_data[] = array("question_id"=>$data['question_id'],"question_options_flag"=>$str[0],"question_options_content"=>$str[1]);
					}
				}
				$question_detail->saveAll($new_data);
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
		parent::win_userauth(129);
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
		//parent::userauth(130);
		//判断是否是ajax请求
		if (request()->isAjax()) {
			$question_id  =   input('post.question_id');
			if ($question_id=='' || !is_numeric($question_id)) {
				parent::operating(request()->path(),1,'参数错误');
				return array('s'=>'参数ID类型错误');
			}else {
				$question =   new \app\common\model\Question;
				$where  =   array('question_id'=>$question_id);
				if ($question->where($where)->value('question_id')) {
					$question->where($where)->delete();
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
		parent::userauth(130);
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
		//parent::userauth(131);
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
	    parent::userauth2(121);
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
	public function answerexport() {
		//验证用户权限
		parent::win_userauth(234);
		\think\Loader::import('PHPExcel.PHPExcel');
		\think\Loader::import('PHPExcel.PHPExcel.IOFactory');
		$PHPExcel  = new \PHPExcel();
		$PHPReader = new \PHPExcel_Reader_Excel2007();
		/*数据查询,与列表页保持一致，开始*/
		$exam_type         = input('request.exam_type');
		$parent_id        = input('request.parent_id');
		$keywords        = input('request.keywords');
		$where = array();
		if($exam_type!=""){
			$where['exam_type'] = array("LIKE","%$exam_type%");
		}else{
			
		}
		if($keywords!=""){
			$where['exam_name|exam_function'] = array("LIKE","%$keywords%");
		}
		$exam=new \app\common\model\Exam;
		$dmenu = new \app\common\model\Dmenu;
		$user = new \app\common\model\User;
		$department = new \app\common\model\Department;
		$member = new \app\common\model\Member;
		$answer = new \app\common\model\Answer;
		$list = $answer->where($where)->paginate(99999,false,array('query'=>$where));
		$answer_list = $list->toArray();
		foreach($answer_list['data'] as $k=>$v){
			//获取人员对应的职责信息
			$member_info = $member->where("member_id='{$v['member_id']}'")->find();
			$answer_list['data'][$k]['real_name'] = $member_info['real_name'];
			$answer_list['data'][$k]['id_number'] = $member_info['id_number'];
			$answer_list['data'][$k]['phone'] 	= $member_info['phone'];
			$department_info = $department->where("id='{$member_info['department_id']}'")->find();
			$answer_list['data'][$k]['department_name'] = $department_info['department_name'];
			if($department_info['parent_name'] !=''){
				$answer_list['data'][$k]['parent_name'] = $department_info['parent_name']."--";
			}else{
				$answer_list['data'][$k]['parent_name'] = '';
			}
		}
		/*数据查询,与列表页保持一致，结束*/
		$save_filename = "考试结果导出";
		$objPHPExcel = new \PHPExcel();
		// Set properties
		$objPHPExcel->getProperties()->setCreator('www');
		$objPHPExcel->getProperties()->setLastModifiedBy('www');
		$objPHPExcel->getProperties()->setTitle('www');
		$objPHPExcel->getProperties()->setSubject('www');
		$objPHPExcel->getProperties()->setDescription('www'.$save_filename);
		$objPHPExcel->getProperties()->setKeywords('www'.$save_filename);
		$objPHPExcel->getProperties()->setCategory('考试结果信息');
		// Add some data
		$objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setBold(true);      //第一行是否加粗
		$objPHPExcel->getActiveSheet(0)->getStyle('A2:I2')->getFont()->setBold(true);      //第二行是否加粗
		$objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setSize(18);         //第一行字体大小
		// 设置垂直居中
		$objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		// 设置水平居中
		$objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet(0)->getRowDimension('1')->setRowHeight(38);    //第一行行高
		$objPHPExcel->getActiveSheet(0)->mergeCells('A1:I1');
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "考试结果信息（{$volist['data'][0]['exam_name']}）");
		$ki = 2;
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$ki, '部门')
		->setCellValue('B'.$ki, '姓名')
		->setCellValue('C'.$ki, '身份证号码')
		->setCellValue('D'.$ki, '手机号码')
		->setCellValue('E'.$ki, '开始答卷时间')
		->setCellValue('F'.$ki, '提交答卷时间')
		->setCellValue('G'.$ki, '所用时间')
		->setCellValue('H'.$ki, '用户IP')
		->setCellValue('I'.$ki, '总分');
		$sheet = $objPHPExcel->getActiveSheet();
		$objPHPExcel->getActiveSheet(0)->getStyle("I")->getAlignment()->setWrapText(TRUE);
		foreach ($answer_list['data'] as $k=>$v){
			$i = $k+3;
			$sheet->setCellValueExplicit("A{$i}", $v['parent_name'].$v['department_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("B{$i}", $v['real_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("C{$i}", $v['id_number'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("D{$i}", $v['phone'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("E{$i}", $v['start_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("F{$i}", $v['submit_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("G{$i}", $v['answer_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("H{$i}", $v['ip'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("I{$i}", $v['exam_points'], \PHPExcel_Cell_DataType::TYPE_NUMERIC);
		}
		$styleThinBlackBorderOutline = array(
				'borders' => array(
						'allborders' => array( //设置全部边框
								'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
						),
				),
		);
		$objPHPExcel->getActiveSheet(0)->getStyle("A1:I{$i}")->applyFromArray($styleThinBlackBorderOutline);
		// Add title
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle(str_replace('www_', '', $save_filename));
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.iconv("UTF-8", "GBK", $save_filename).'.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save("php://output");
		exit;
	}
    //删除部门资料到回收站
    public function answer_del() {
//        parent::userauth(141);
        //判断是否是ajax请求
        if (request()->isAjax()) {
            $answer_id  =   input('post.answer_id');
            if ($answer_id=='' || !is_numeric($answer_id)) {
                parent::operating(request()->path(),1,'参数错误');
                return array('s'=>'参数ID类型错误');
            }else {
                $answer_id =   intval($answer_id);
                $answer = new \app\common\model\Answer;
                $where  =   array('answer_id'=>$answer_id);
                if ($answer->where($where)->select()) {
                    $answer->where($where)->delete();
                    parent::operating(request()->path(),0,'删除成功：'.$answer_id);
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
}
?>