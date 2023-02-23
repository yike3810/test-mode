<?php
//考试管理类
namespace app\exam\controller;
use think\Controller;
use think\Request;
use think\Url;
class Examcount extends Admin {
	public function index() {
		parent::userauth2(172);
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
        $list = $exam->where($where)->paginate(null,false,array('query'=>$where));
        $exam_list = $list->toArray();
        foreach($exam_list['data'] as $k=>$v){
            //获取人员对应的职责信息
        	$exam_list['data'][$k]['user_name'] = $user->where(['ID'=>$v['add_user']])->value('Username');
        	$exam_list['data'][$k]['highest'] = $answer->where(['exam_id'=>$v['exam_id']])->max('exam_points');
        	$exam_list['data'][$k]['average'] = $answer->where(['exam_id'=>$v['exam_id']])->avg('exam_points');
        	$exam_list['data'][$k]['average'] = round($exam_list['data'][$k]['average'],0);
        	
        	$exam_list['data'][$k]['minimum'] = $answer->where(['exam_id'=>$v['exam_id']])->min('exam_points');
        	$exam_list['data'][$k]['examcount'] = $answer->where(['exam_id'=>$v['exam_id']])->count();
        	$exam_list['data'][$k]['exam_status_name']=$exam->exam_status_array[$exam_list['data'][$k]['exam_status']];
        	$exam_list['data'][$k]['exam_type_name']=$exam->exam_type_array[$exam_list['data'][$k]['exam_type']];
        }
        $this->assign('exam_list',$exam_list);
        $this->assign('list',$list);
        $this->assign('exam',$exam);

        return $this->fetch();
	}
	public function tree() {
		parent::userauth2(172);
		$exam_id     = input('request.exam_id');
		$sid        = input('request.sid');
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
        $this->assign('exam_id',$exam_id);
		return $this->fetch();
	}
	///////////新增树形统计
    public function getTreelist($parent_id){
        $exam_tree_list = array();
        if(!$parent_id){
            $exam_tree_list[] = array('title'=>'部门分类','spread'=>true);
        }
        $department = new \app\common\model\Department;
        $where['parent_id'] = $parent_id;
        $exam_list =$department ->where($where)->column('');
        foreach($exam_list as $k=>$v){
            $exam_children = array(
                'id'=>$v['id'],
                'title'=>$v['department_name'],
                'spread'=>false,
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
    public function getExamsList(){
        $answer=new \app\common\model\Answer;
        $department = new \app\common\model\Department;
        $dmenu = new \app\common\model\Dmenu;
        $member= new \app\common\model\Member;
        $where = array();
        $limit = input('request.limit');
        $parent_id = input('request.parent_id');
        $exam_id    = input('request.exam_id');
        $where['exam_id'] = $exam_id;
        $where['list_rows'] = $limit;
        $where['parent_id'] = $parent_id;
        $answer_list=$answer->getDepartmentExamList($where);
        foreach($answer_list['data'] as $k=>$v){
            $answer_list['data'][$k]['sex']=$member->member_sex_array[$answer_list['data'][$k]['sex']];
            $answer_list['data'][$k]['answer_time']= s_to_hs($answer_list['data'][$k]['answer_time']);
            if ($answer_list['data'][$k]['id_number']){
                $answer_list['data'][$k]['id_number'] = substr_replace($answer_list['data'][$k]['id_number'],'**********',6,10);
            }

        }
        $result = array("code"=>0,"count"=> $answer_list['total'],"data"=> $answer_list['data']);
        echo json_encode($result);
        exit;
    }


    ////////////
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
		parent::win_userauth(128);
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
		parent::win_userauth(129);
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
		parent::win_userauth(129);
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
			
			$exam	=   new \app\common\model\Exam;
			$member 	=   new \app\common\model\Member;
			$exam_info = $exam->where("exam_name = '{$data['exam_name']}' AND exam_id != '{$data['exam_id']}'")->find();
			if(!empty($exam_info)){
			    $this->error("该考试已存在!");
			}
			$exam_data = $exam->where("exam_id = '{$data['exam_id']}'")->find();
			try{
				$exam->save($data,"exam_id='{$data['exam_id']}'");
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
		parent::userauth(130);
		//判断是否是ajax请求
		if (request()->isAjax()) {
			$exam_id  =   input('post.exam_id');
			if ($exam_id=='' || !is_numeric($exam_id)) {
				parent::operating(request()->path(),1,'参数错误');
				return array('s'=>'参数ID类型错误');
			}else {
				$exam =   new \app\common\model\Exam();
				$where  =   array('exam_id'=>$exam_id);
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
	public function getDepartmentTreelist($parent_id){
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
	public function examcounexport() {
	    //验证用户权限
		parent::win_userauth(172);
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
	    $list = $exam->where($where)->paginate(null,false,array('query'=>$where));
	    
	    $exam_list = $list->toArray();
	    foreach($exam_list['data'] as $k=>$v){
	        //获取人员对应的职责信息
	        $exam_list['data'][$k]['user_name'] = $user->where(['ID'=>$v['add_user']])->value('Username');
	        $exam_list['data'][$k]['highest'] = $answer->where(['exam_id'=>$v['exam_id']])->max('exam_points');
	        $exam_list['data'][$k]['average'] = $answer->where(['exam_id'=>$v['exam_id']])->avg('exam_points');
	        $exam_list['data'][$k]['minimum'] = $answer->where(['exam_id'=>$v['exam_id']])->min('exam_points');
	        $exam_list['data'][$k]['examcount'] = $answer->where(['exam_id'=>$v['exam_id']])->count();
	        $exam_list['data'][$k]['exam_status_name']=$exam->exam_status_array[$exam_list['data'][$k]['exam_status']];
	        $exam_list['data'][$k]['exam_type_name']=$exam->exam_type_array[$exam_list['data'][$k]['exam_type']];
	    }
	    /*数据查询,与列表页保持一致，结束*/
	    $save_filename = "全部考试信息导出";
	    $objPHPExcel = new \PHPExcel();
	    // Set properties
	    $objPHPExcel->getProperties()->setCreator('www');
	    $objPHPExcel->getProperties()->setLastModifiedBy('www');
	    $objPHPExcel->getProperties()->setTitle('www');
	    $objPHPExcel->getProperties()->setSubject('www');
	    $objPHPExcel->getProperties()->setDescription('www'.$save_filename);
	    $objPHPExcel->getProperties()->setKeywords('www'.$save_filename);
	    $objPHPExcel->getProperties()->setCategory('全部考试信息');
	    // Add some data
	    $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setBold(true);      //第一行是否加粗
	    $objPHPExcel->getActiveSheet(0)->getStyle('A2:J2')->getFont()->setBold(true);      //第二行是否加粗
	    $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setSize(18);         //第一行字体大小
	    // 设置垂直居中
	    $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
	    // 设置水平居中
	    $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet(0)->getRowDimension('1')->setRowHeight(38);    //第一行行高
	    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:J1');
	    
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "全部考试信息");
	    $ki = 2;
	    $objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A'.$ki, '考试名称')
	    ->setCellValue('B'.$ki, '考试类型')
	    ->setCellValue('C'.$ki, '考试状态')
	    ->setCellValue('D'.$ki, '考试开始时间')
	    ->setCellValue('E'.$ki, '考试结束时间')
	    ->setCellValue('F'.$ki, '创建人')
	    ->setCellValue('G'.$ki, '最高分')
	    ->setCellValue('H'.$ki, '最低分')
	    ->setCellValue('I'.$ki, '平均分')
	    ->setCellValue('J'.$ki, '参与人数');
	    $sheet = $objPHPExcel->getActiveSheet();
	    $objPHPExcel->getActiveSheet(0)->getStyle("I")->getAlignment()->setWrapText(TRUE);
	    foreach ($exam_list['data'] as $k=>$v){
	        $i = $k+3;
	        $sheet->setCellValueExplicit("A{$i}", $v['exam_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("B{$i}", $v['exam_type_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("C{$i}", $v['exam_status_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("D{$i}", $v['start_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("E{$i}", $v['end_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("F{$i}", $v['user_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("G{$i}", $v['highest'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("H{$i}", $v['minimum'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("I{$i}", $v['average'], \PHPExcel_Cell_DataType::TYPE_NUMERIC)
	        ->setCellValueExplicit("J{$i}", $v['examcount'], \PHPExcel_Cell_DataType::TYPE_NUMERIC);
	    }
	    $styleThinBlackBorderOutline = array(
	        'borders' => array(
	            'allborders' => array( //设置全部边框
	                'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
	            ),
	        ),
	    );
	    $objPHPExcel->getActiveSheet(0)->getStyle("A1:J{$i}")->applyFromArray($styleThinBlackBorderOutline);
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
	public function examAllExport() {
	    //验证用户权限
		parent::win_userauth(172);
	    \think\Loader::import('PHPExcel.PHPExcel');
	    \think\Loader::import('PHPExcel.PHPExcel.IOFactory');
	    $PHPExcel  = new \PHPExcel();
	    $PHPReader = new \PHPExcel_Reader_Excel2007();
	    /*数据查询,与列表页保持一致，开始*/
	    $exam_type         	= input('request.exam_type');
	    $exam_id        	= input('request.exam_id');
	    $keywords        	= input('request.keywords');
	    $where = array();
	    $where['exam_id'] = $exam_id;
	    $exam=new \app\common\model\Exam;
	    $dmenu = new \app\common\model\Dmenu;
	    $user = new \app\common\model\User;
	    $department = new \app\common\model\Department;
	    $member = new \app\common\model\Member;
	    $answer = new \app\common\model\Answer;
	    $list = $answer->where($where)->paginate(60000);
	    $answer_list = $list->toArray();
	    $dmenu_list = $dmenu->column('MenuName','ID');
	    foreach($answer_list['data'] as $k=>$v){
	        //获取人员对应的职责信息
	    	$member_info = $member->where("member_id='{$v['member_id']}'")->find();
	    	$department_info = $department->where("id='{$member_info['department_id']}'")->find();
	    	$parent_info = $department->where("id='{$department_info['parent_id']}'")->find();
	    	if(empty($parent_info)){
	    		$answer_list['data'][$k]['parent_name'] = '';
	    	}else{
	    		$answer_list['data'][$k]['parent_name'] = $parent_info['department_name']."-";
	    	}
	    	$answer_list['data'][$k]['department_name'] = $department_info['department_name'];
	    	$answer_list['data'][$k]['real_name'] 		= $member_info['real_name'];
	    	$answer_list['data'][$k]['id_number'] 		= $member_info['id_number'];
	    	$answer_list['data'][$k]['sex_name'] 		= $dmenu_list[$member_info['sex']];
	    	$answer_list['data'][$k]['phone'] 			= $member_info['phone'];
	    	$answer_list['data'][$k]['political_status']= $member_info['political_status'];
	    }
	    $_exam_name = $exam->where($where)->value("exam_name");
	    /*数据查询,与列表页保持一致，结束*/
	    $save_filename = "(".$_exam_name.")考试成绩信息";
	    $objPHPExcel = new \PHPExcel();
	    // Set properties
	    $objPHPExcel->getProperties()->setCreator('www');
	    $objPHPExcel->getProperties()->setLastModifiedBy('www');
	    $objPHPExcel->getProperties()->setTitle('www');
	    $objPHPExcel->getProperties()->setSubject('www');
	    $objPHPExcel->getProperties()->setDescription('www'.$save_filename);
	    $objPHPExcel->getProperties()->setKeywords('www'.$save_filename);
	    $objPHPExcel->getProperties()->setCategory('全部考试信息');
	    // Add some data
	    $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setBold(true);      //第一行是否加粗
	    $objPHPExcel->getActiveSheet(0)->getStyle('A2:J2')->getFont()->setBold(true);      //第二行是否加粗
	    $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setSize(18);         //第一行字体大小
	    // 设置垂直居中
	    $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
	    // 设置水平居中
	    $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet(0)->getRowDimension('1')->setRowHeight(38);    //第一行行高
	    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:J1');

	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "(".$_exam_name.")考试成绩");
	    $ki = 2;
	    $objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A'.$ki, '部门名称')
	    ->setCellValue('B'.$ki, '姓名')
	    ->setCellValue('C'.$ki, '性别')
	    ->setCellValue('D'.$ki, '手机号')
	    ->setCellValue('E'.$ki, '身份证号码')
	    ->setCellValue('F'.$ki, '政治面貌')
	    ->setCellValue('G'.$ki, '答题开始时间')
	    ->setCellValue('H'.$ki, '提交答卷时间')
	    ->setCellValue('I'.$ki, '所用时间')
	    ->setCellValue('J'.$ki, '分数');
	    $sheet = $objPHPExcel->getActiveSheet();
	    $sheet->getColumnDimension('A')->setWidth(20);
	    $sheet->getColumnDimension('D')->setWidth(20);
	    $sheet->getColumnDimension('E')->setWidth(20);
	    $sheet->getColumnDimension('I')->setWidth(20);
	    //$objPHPExcel->getActiveSheet(0)->getStyle("I")->getAlignment()->setWrapText(TRUE);
	    foreach ($answer_list['data'] as $k=>$v){
	        $i = $k+3;
	        $sheet->setCellValueExplicit("A{$i}", $v['parent_name'].$v['department_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("B{$i}", $v['real_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("C{$i}", $v['sex'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("D{$i}", $v['phone'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("E{$i}", $v['id_number'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("F{$i}", $v['political_status'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("G{$i}", $v['start_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("H{$i}", $v['submit_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("I{$i}", s_to_hs($v['answer_time']), \PHPExcel_Cell_DataType::TYPE_STRING)
	        ->setCellValueExplicit("J{$i}", $v['exam_points'], \PHPExcel_Cell_DataType::TYPE_NUMERIC);
	    }
	    $styleThinBlackBorderOutline = array(
	        'borders' => array(
	            'allborders' => array( //设置全部边框
	                'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
	            ),
	        ),
	    );
	    $objPHPExcel->getActiveSheet(0)->getStyle("A1:J{$i}")->applyFromArray($styleThinBlackBorderOutline);
	    // Add title
	    $objPHPExcel->setActiveSheetIndex(0);
	    $objPHPExcel->getActiveSheet()->setTitle(str_replace('www_', '', "考试成绩"));
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
    public function examDepartmentExport() {
        //验证用户权限
        parent::win_userauth(172);
        \think\Loader::import('PHPExcel.PHPExcel');
        \think\Loader::import('PHPExcel.PHPExcel.IOFactory');
        $PHPExcel  = new \PHPExcel();
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        /*数据查询,与列表页保持一致，开始*/
        $answer=new \app\common\model\Answer;
        $department = new \app\common\model\Department;
        $dmenu = new \app\common\model\Dmenu;
        $member= new \app\common\model\Member;
        $exam= new \app\common\model\Exam;
        $where = array();
        $limit = 65535;
        $parent_id = input('request.parent_id');
        $exam_id     = input('request.exam_id');
        $where['exam_id'] = $exam_id;
        $where['list_rows'] = $limit;
        $where['parent_id'] = $parent_id;
        $dmenu_list=$dmenu->where('Sid <> 0')->order('Sortid asc')->select();
        $answer_list =$answer->getDepartmentExamList($where);
        $this->assign('dmenu_list',$dmenu_list);
        $_exam_name = $exam->where("exam_id='{$exam_id}'")->value("exam_name");
        /*数据查询,与列表页保持一致，结束*/

        $save_filename = "("    .   $_exam_name    .   ")考试成绩信息";
        $objPHPExcel = new \PHPExcel();
        // Set properties
        $objPHPExcel->getProperties()->setCreator('www');
        $objPHPExcel->getProperties()->setLastModifiedBy('www');
        $objPHPExcel->getProperties()->setTitle('www');
        $objPHPExcel->getProperties()->setSubject('www');
        $objPHPExcel->getProperties()->setDescription('www'.$save_filename);
        $objPHPExcel->getProperties()->setKeywords('www'.$save_filename);
        $objPHPExcel->getProperties()->setCategory('全部考试信息');
        // Add some data
        $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setBold(true);      //第一行是否加粗
        $objPHPExcel->getActiveSheet(0)->getStyle('A2:J2')->getFont()->setBold(true);      //第二行是否加粗
        $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setSize(18);         //第一行字体大小
        // 设置垂直居中
        $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        // 设置水平居中
        $objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet(0)->getRowDimension('1')->setRowHeight(38);    //第一行行高
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:J1');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "("  .$_exam_name.  ")考试成绩");

        $ki = 2;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$ki, '部门名称')
            ->setCellValue('B'.$ki, '姓名')
            ->setCellValue('C'.$ki, '性别')
            ->setCellValue('D'.$ki, '手机号')
            ->setCellValue('E'.$ki, '身份证号码')
            ->setCellValue('F'.$ki, '政治面貌')
            ->setCellValue('G'.$ki, '答题开始时间')
            ->setCellValue('H'.$ki, '提交答卷时间')
            ->setCellValue('I'.$ki, '所用时间')
            ->setCellValue('J'.$ki, '分数');
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        //$objPHPExcel->getActiveSheet(0)->getStyle("I")->getAlignment()->setWrapText(TRUE);
        foreach ($answer_list['data'] as $k=>$v){
            $i = $k+3;
            $sheet->setCellValueExplicit("A{$i}", $v['parent_name'].$v['department_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("B{$i}", $v['real_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("C{$i}", $v['sex'], \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("D{$i}", $v['phone'], \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("E{$i}", $v['id_number'], \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("F{$i}", $v['political_status'], \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("G{$i}", $v['start_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("H{$i}", $v['submit_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("I{$i}", s_to_hs($v['answer_time']), \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("J{$i}", $v['exam_points'], \PHPExcel_Cell_DataType::TYPE_NUMERIC);
        }
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'allborders' => array( //设置全部边框
                    'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                ),
            ),
        );
        $objPHPExcel->getActiveSheet(0)->getStyle("A1:J{$i}")->applyFromArray($styleThinBlackBorderOutline);
        // Add title
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle(str_replace('www_', '', "考试成绩"));
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
}
?>