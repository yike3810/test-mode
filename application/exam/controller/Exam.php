<?php
//考试管理类
namespace app\exam\controller;
use think\Controller;
use think\Request;
use think\Url;
use think\Db;
class Exam extends Admin {
	public function index() {
		parent::userauth2(235);
        $exam_type         = input('request.exam_type');
        $parent_id         = input('request.parent_id');
        $keywords          = input('request.keywords');
        $where = array();
        if($exam_type!=""){
            $where['exam_type'] = array("LIKE","%$exam_type%");
        }else{

        }
        if($keywords!=""){
        	$where['exam_name'] = array("LIKE","%$keywords%");
        }
        //b($keywords);
        $exam=new \app\common\model\Exam;
        $dmenu = new \app\common\model\Dmenu;
        $user = new \app\common\model\User;
        $department = new \app\common\model\Department;
        $member = new \app\common\model\Member;
        $list = $exam->where($where)->paginate(null,false,array('query'=>$where));
        $exam_list = $list->toArray();
        foreach($exam_list['data'] as $k=>$v){
            //获取人员对应的职责信息
        	$exam_list['data'][$k]['user_name'] = $user->where(['ID'=>$v['add_user']])->value('Username');
        }
        $this->assign('exam_list',$exam_list);
        $this->assign('list',$list);
        $this->assign('exam',$exam);

        return $this->fetch();
	}
	public function tree() {
		parent::userauth2(127);
		$exam_type     = input('request.exam_type');
		$parent_id        = input('request.parent_id');
		$where = array();
		if($exam_type!=""){
			$where['exam_type'] = array("LIKE","%$exam_type%");
		}else{
			
		}
		if($parent_id!=""){
			$where['parent_id'] = $parent_id;
		}else{
			$where['parent_id'] =0;
		}
 		//(b($exam_tree_list));
// 		a(json_encode($exam_tree_list));
 		$exam_tree_list = $this->getTreelist(0);
 		//a($exam_tree_list);
		$this->assign('exam_tree_list',json_encode($exam_tree_list));
		return $this->fetch();
	}
	//新增考试
	public function add() {
		parent::win_userauth(230);
		$dmenu = new \app\common\model\Dmenu;
		$user  = new \app\common\model\User;
		$exam  = new \app\common\model\Exam;
		$department  = new \app\common\model\Department;
        $parent_id   = input('request.parent_id');
		$dmenu_list=$dmenu->where('Sid <>0')->order('Sortid asc')->select();
		$this->assign('dmenu_list',$dmenu_list);
		$user_list=$user->where('Status = 0')->order('ID ASC')->select();
		$this->assign('user_list',$user_list);
		$department_list=$department->where('parent_id = 0')->order('department_id ASC')->select();
		$this->assign('department_list',$department_list);
		return $this->fetch();
	}
	//添加处理
	public function add_do() {
		//验证用户权限
		parent::win_userauth(230);
		if (request()->isPost()) {
			$data	=	$member_data	= array();
			$data['exam_name']    		= input('post.exam_name');
			$data['exam_type']       	= input('post.exam_type');
			$data['random_number']   	= input('post.random_number');
			$data['start_time']   		= input('post.start_time');
			$data['end_time']   		= input('post.end_time');
			$data['answer_time_limit']  = input('post.answer_time_limit');
			$data['notes']  			= input('post.notes');
			$data['add_user']   		= session('ThinkUser.ID');
			$data['add_time']   		= date("Y-m-d H:i:s");
            $data['re_exam_times']   		= input('post.resit_count');
			$exam	=   new \app\common\model\Exam;
			$member 	=   new \app\common\model\Member;
			$exam_info = $exam->where("exam_name = '{$data['exam_name']}'")->find();
			if(!empty($exam_info)){
			    $this->error("该考试已存在!");
			}
			if ($exam->save($data)) {
				parent::operating(request()->path(),0,'新增考试：'.$data['exam_name']);
				$this->success('添加成功',url('Exam/add'),3);
			}else {
				parent::operating(request()->path(),1,'新增失败：'.$exam->getError());
				$this->error($exam->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//修改考试资料
	public function edit() {
		parent::win_userauth(231);
		$exam_id = input('param.exam_id');
		if ($exam_id=='' || !is_numeric($exam_id)) {
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
		$exam = new \app\common\model\Exam;
		$result = $exam->where("exam_id = '{$exam_id}' ")->find();
		if (!$result) {
			parent::operating(request()->path(),1,'没有找到数据：'.$exam_id);
			$this->error('不存在你要修改的数据，请关闭本窗口');
		}
		$this->assign('dmenu_list',$dmenu_list);
		$this->assign('result',$result);
		return $this->fetch();
	}
	//修改考试资料处理
	public function edit_do() {
		//验证用户权限
		parent::win_userauth(231);
		if (request()->isPost()) {
			$data=array();
			//考试资料信息
			$data['exam_id']            = input('post.exam_id');
			$data['exam_name']    		= input('post.exam_name');
			$data['exam_type']       	= input('post.exam_type');
			$data['exam_status']       	= input('post.exam_status');
			$data['random_number']   	= input('post.random_number');
			$data['start_time']   		= input('post.start_time');
			$data['end_time']   		= input('post.end_time');
			$data['answer_time_limit']  = input('post.answer_time_limit');
			$data['notes']  			= input('post.notes');
            $data['re_exam_times']   		= input('post.resit_count');
			$exam	    =  new \app\common\model\Exam;
			$member 	=  new \app\common\model\Member;
            $question   =  new \app\common\model\Question;
			$exam_info = $exam->where("exam_name = '{$data['exam_name']}' AND exam_id != '{$data['exam_id']}'")->find();
			if(!empty($exam_info)){
			    $this->error("该考试已存在!");
			}
			//如果是随机考试，每道题分值需要一致
            $question_points_groyp_count = Db::table('jsl_question')->where("exam_id = '{$data['exam_id']}'")->group('question_points')->count();
            if($data['exam_type'] == 2 && $question_points_groyp_count>1){
                $this->error("考试题目分数不一致，随机抽题后的试卷总分无法确定，请确保每道题分数相同!",null, $data = '', 8);
            }
            //如果是随机开始，抽取题数量不能大于总题数
            $question_count = $question->where("exam_id = '{$data['exam_id']}'")->count();
            if($data['exam_type'] == 2 && $data['random_number'] > $question_count ){
                $this->error("随机抽取类型的考试：抽取题数量不能大于总题数!",null, $data = '', 5);
            }
            //如果是随机开始，抽取题数量大于0
            if($data['exam_type'] == 2 && $data['random_number'] == 0){
                $this->error("随机抽取类型的考试：抽取题数量需大于0!",null, $data = '', 5);
            }
			$exam_data = $exam->where("exam_id = '{$data['exam_id']}'")->find();
			try{
				$exam->save($data,"exam_id='{$data['exam_id']}'");
                $exam->updateExamInfo($data['exam_id']);
				parent::operating(request()->path(),0,'更新考试资料：'.$data['exam_name']);
			}catch(\Exception $e){
				$this->error($e->getMessage());
				parent::operating(request()->path(),1,'更新失败：'.$data['exam_name']);
			}
			$this->success('考试资料更新成功',url('Exam/edit','exam_id='.$data['exam_id']));
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//删除考试资料到回收站
	public function exam_del() {
		parent::userauth(232);
		//判断是否是ajax请求
		if (request()->isAjax()) {
			$exam_id  =   input('post.exam_id');
			if ($exam_id=='' || !is_numeric($exam_id)) {
				parent::operating(request()->path(),1,'参数错误');
				return array('s'=>'参数ID类型错误');
			}else {
				$exam 	=   new \app\common\model\Exam();
				$answer =   new \app\common\model\Answer();
				$where  =   array('exam_id'=>$exam_id);
				if(!empty($answer->where($where)->find())){
					parent::operating(request()->path(),1,'该考试已有答题记录不能删除！');
					return array('s'=>'该考试已有答题记录不能删除！');
				}
				if ($exam->where($where)->value('exam_id')) {
                    $exam->where($where)->delete();
                    parent::operating(request()->path(),0,'删除成功：'.$exam_id);
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
		parent::userauth(232);
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
}
?>