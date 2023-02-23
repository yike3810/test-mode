<?php
namespace app\exam\controller;
use think\Controller;
use think\Request;
use think\Url;

class Member extends Admin
{
	public function index() {
		parent::userauth2(132);
		$department_id = input('request.department_id');
        $department_type = input('request.department_type');
        $parent_id = input('request.parent_id');
		$real_name = input('request.real_name');
		$member_id = input('request.id');
		$keywords = input('request.keywords');

		$member = new \app\common\model\Member;
		
		//查询部门库
		$department = new \app\common\model\Department;
        $dmenu = new \app\common\model\Dmenu;

		$department_list =$department->where('department_type = 118')->order('department_id ASC')->select();
        $dmenu_list =$dmenu->where('Sid = 109')->order('Sortid ASC')->select();

		$where = array();
		if($keywords!=""){
		    $where['keywords'] = $keywords;
		}
		if($real_name!=""){
		    $where['real_name']= $real_name;
		}
        if($parent_id!=""){
            //数据库查询
            $parent_info = $department->where("parent_id='{$parent_id}' ")->find();
            if(empty($parent_info)){
                $where['department_id']= $parent_id ;
            }else{
                $where['parent_id']= $parent_id ;
            }
        }

		if($department_id!=""){
		    $where['department_id']= $department_id ;
		}
		if($department_type!=""){
		    $where['department_type']= $department_type ;
		}

//		$lists  = $member->where($where)->order('member_id ASC')->paginate();
//		//$lists   = $member->paginate(config('paginate.list_rows'),$volist['total']);
//		$volist = $lists->toArray();
        $volist = $member->getMemberList($where);
        foreach($volist['data'] as $k=>$v){
            if ($volist['data'][$k]['id_number']){
                $volist['data'][$k]['id_number'] = substr_replace($volist['data'][$k]['id_number'],'**********',6,10);
            }
		    if($volist['data'][$k]['sex']=='73')
		    {
		        $volist['data'][$k]['sex_name']    ="男";
		    }else {
		        $volist['data'][$k]['sex_name']    ="女";
		    }
		    $volist['data'][$k]['department_name']    =$department->where("department_id='{$volist['data'][$k]['department_id']}'")->value('department_name');
		}
        $lists   = $member->paginate(config('paginate.list_rows'),$volist['total'],array('query'=>$where));
        if($department_type!=""){
            $parent_id_list = $department->where("department_type = '{$department_type}' AND parent_id=0 ")->order('department_id ASC')->select();
        }
        if($parent_id!=""){
            $department_id_list = $department->where("parent_id = '{$parent_id}'")->order('department_id ASC')->select();
        }
		$this->assign('volist',$volist);
		$this->assign('lists',$lists);
        $this->assign('dmenu_list',$dmenu_list);
        $this->assign('department_list',$department_list);
		$this->assign('keywords',$keywords);
		$this->assign('parent_id_list',$parent_id_list);
        $this->assign('department_id_list',$department_id_list);
        return $this->fetch();
	}
    public function getDptlist(){
        $id = input('request.id');
        $department = new \app\common\model\Department;
        $where['department_type'] = $id;
        $where['parent_id'] = 0;
        $department_list = $department->where($where)->order("sort ASC")->select();
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
        $department_list = array("status" => $status, "data" => $html, "message" => $message);
        //return json($region_list);
        echo json_encode($department_list, JSON_UNESCAPED_UNICODE);exit;
    }
    public function getDptlist2(){
        $id = input('request.id');
        $department = new \app\common\model\Department;
        $where['parent_id'] = $id;
        $department_list = $department->where($where)->order("sort ASC")->select();
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
        $department_list = array("status" => $status, "data" => $html, "message" => $message);
        //return json($region_list);
        echo json_encode($department_list, JSON_UNESCAPED_UNICODE);exit;
    }
    public function talent() {
		parent::userauth2(123);
		$department = input('request.department');
		$post_id = input('request.post');
		$degree = input('request.degree');
		$term = input('request.term');
		$school_level = input('request.school_level');
		$id = input('request.id');
		$keywords = input('request.keywords');
		$where = array();
		if($keywords!=""){
		    $where['phone|member_name|email|sex|id_number'] = array("LIKE","%$keywords%");
		}
		if($department!=""){
			$where['department']= $department;
		}
		if($post_id!=""){
			$where['post']= $post_id ;
		}
		if($degree!=""){
			$where['degree']= $degree ;
		}
		if($term!=""){
			$where['term']= $term ;
		}
		
		//$lists  = $activities->where($where)->paginate();
		//$volist = $lists->toArray();
		//print_r($volist);
		//$this->assign('volist',$volist);
		//$this->assign('keyword',$keyword);
		//$this->assign('lists',$lists);
		if($school_level =="double_first_rate"){
			$where['school_level']= $school_level;
		}
		if($school_level =="is_province"){
			$where['is_province']= "yes" ;
			
		}
		if($school_level =="other"){
			$where['is_province'] = null;
			$where['school_level']= null;
		}
		
		$member = new \app\common\model\Member;
		$department = new \app\common\model\Department;
		$department_list =$department->where('id<>0')->select();
		
		
		$where_string ="";
		$lists  = $jinali->where($where)->order('id DESC')->paginate();
		$volist = $lists->toArray();
		foreach($volist['data'] as $k=>$v){
			$volist['data'][$k]['department_name']    = $department->where("id='{$v['department']}'")->value('department_name');
			$volist['data'][$k]['post_name']    = $post->where("id='{$v['post']}'")->value('post_name');
		}
		$this->assign('volist',$volist);
		$this->assign('lists',$lists);
		$this->assign('department_list',$department_list);
		$this->assign('post_list',$post_list);
		$this->assign('keyword',$keyword);
		return $this->fetch();
	}
	public function memberadd() {
		//parent::win_userauth(3);
		$wbs= new \app\common\model\Wbs;
		$wbs_list=$wbs->where('id<>0')->paginate();
		
		//查询project表
		$project = new \app\common\model\Project;
		$project_lists=$project->where('id<>0')->select();
		$this->assign('project_lists',$project_lists);
		
		//查询task表
		$task = new \app\common\model\Task;
		$task_lists=$task->where('id<>0')->select();
		$this->assign('task_lists',$task_lists);
		
		//查询activies表
		$activities = new \app\common\model\Activities;
		$activities_lists=$activities->where('id<>0')->select();
		$this->assign('activities_lists',$activities_lists);
		
		$this->assign('volist',$volist);
		$this->assign('wbs_list',$wbs_list);

		
		
		return $this->fetch('add');
	}
	//添加任务
	public function memberadd_do() {
		//验证用户权限
		//parent::userauth(3);
		$data=array();
		if (request()->isPost()) {
			$data=array();
			$cont=array();
			$data_projectuser=array();
			
			$data['project_id']    = input('post.project_id');
			$data['task_id']    = input('post.task_id');
			$data['activities_id']    = input('post.activities_id');
			$data['estimated_time']    = input('post.estimated_time');
			$data['estimated_hr']    = input('post.estimated_hr');
			$data['estimated_resources']    = input('post.estimated_resources');
			$data['estimated_cost']    = input('post.estimated_cost');
			$data['expected_days']    = input('post.expected_days');
			//$wbsuser  =   new \app\common\model\Wbsuser;
			//$wbsuser_lists=$wbsuser->where("id='{$data['project_id']}'")->select();
			
			$wbs  =   new \app\common\model\Wbs;
			$wbs_info = $wbs->where("activities_id = '{$data['activities_id']}' and project_id = '{$data['project_id']}'" )->find();
			if(!empty($wbs_info)){
			    $this->error("该活动已存在!");
			}
			
			
			$projectuser  =   new \app\common\model\Projectuser;
			$projectuser_lists=$projectuser->where('project_id',$data['project_id'])->column('id,project_id,user_id');
			
			$wbsuser = new \app\common\model\Wbsuser;
			if ($wbs->save($data)) {
			    parent::operating(request()->path(),0,'新增WBS工作结构分解：'.$data['activities_name']);
				
				$wbs_id = $wbs->id;
				foreach($projectuser_lists as $k=>$v){
				$data_projectuser[$k]['project_id']    = $v['project_id'];
				$data_projectuser[$k]['user_id']    =  $v['user_id'];
				$data_projectuser[$k]['wbs_id'] = $wbs_id;
				}
				
				if ($wbsuser->saveAll($data_projectuser)) {
			    parent::operating(request()->path(),0,'新增WBS工作结构分解人员：');

				}else {
				parent::operating(request()->path(),1,'新增WBS工作结构分解人员失败：'.$wbsuser->getError());
				$this->error($wbs->getError());
				}
				
				
				$this->success('添加WBS工作结构分解成功',url('Wbs/wbsadd'),3);
			}else {
				parent::operating(request()->path(),1,'新增失败：'.$wbs->getError());
				$this->error($wbs->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//修改界面
	public function memberedit() {
		//parent::win_userauth(4);
	    
		$id = input('param.member_id');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(request()->path(),1,'参数错误');
			$this->assign('content','参数ID类型错误，请关闭本窗口');
			return $this->fetch('public/err');
		}
		$id=intval($id);
		$member = new \app\common\model\Member;
        $dmenu = new \app\common\model\Dmenu;

        //查询服务队库
		$department = new \app\common\model\Department;	
		$member =new \app\common\model\Member;
		$department_id =$member->where("member_id = '{$id}'")->value('department_id');

		$department_type =$department->where("id = '{$department_id}'")->value('department_type');
        $dmenu_list = $dmenu->where('Sid = 109')->order('Sortid ASC')->select();
        //a($department_type);
		$department_list_1=$department->where('department_type = 110')->order('id ASC')->select();
		$department_list_2=$department->where('department_type = 111 and parent_id = 0')->order('id ASC')->select();
		$department_list_3=$department->where('department_type = 111 and parent_id <> 0')->order('id ASC')->select();

        if ($lists=$member->where("member_id=$id")->find()) {
		    $result = $lists ->toArray();		    
		    if($result['sex']=='73'){
		        $result['sex_name'] = "男";
		    }else {
		        $result['sex_name'] = "女";
		    }

		    $result['department_name']    =$department->where("id='{$result['department_id']}'")->value('department_name');
            $result['department_type']    =$department->where("id='{$result['department_id']}'")->value('department_type');
            $result['parent_id']          =$department->where("id='{$result['department_id']}'")->value('parent_id');
            $result['parent_name']        =$department->where("id='{$result['department_id']}'")->value('parent_name');

            $parent_id_list = $department->where("department_type = '{$result['department_type']}' AND parent_id=0 ")->order('id ASC')->select();
            $department_id_list = $department->where("parent_id = '{$result['parent_id']}' AND {$department_id}!=0")->order('id ASC')->select();
            if($result['parent_id']==0){
                $department_id_list = [];
            }
            $this->assign('result',$result);
			$this->assign('department_type',$department_type);
            $this->assign('parent_id_list',$parent_id_list);
            $this->assign('department_id_list',$department_id_list);

            $this->assign('department_list_1',$department_list_1);
			$this->assign('department_list_2',$department_list_2);
			$this->assign('department_list_3',$department_list_3);
		    $this->assign('user_list',$user_list);
            $this->assign('dmenu_list',$dmenu_list);
		    $this->assign('lists',$lists);
		    $this->assign('keyword',$keyword);
			
		    return $this->fetch("edit2");
		}else {
		    parent::operating(request()->path(),1,'数据不存在');
		    $this->assign('content','没有找到相关数据，请关闭本窗口');
		    return $this->fetch('public/err');
		}
	}
	//任务修改处理
	public function memberedit_do() {
		//验证用户权限
		//parent::win_userauth(123);
		if (request()->isPost()) {
			$data=array();
			$data_service=array();
			//项目资料信息
            $parent_id = input('post.parent_id');
			$data['member_id'] = input('post.member_id');
			$data['member_name']    = input('post.member_name');
			$data['real_name']    = input('post.real_name');
			$data['sex']    = input('post.sex');
            $data['political_status']    = input('post.political_status');
            $data['phone'] = input('post.phone');
			$data['email'] = input('post.email');
			$data['id_number'] = input('post.id_number');
//			$data['member_points'] = input('post.member_points');
			//$data['register_time'] = input('post.register_time');
//			$data_service['department_s'] = input('post.department_s');
            $data['department_id'] = input('post.department_id');
            $department = new \app\common\model\Department;
            $parent_info = $department->where("parent_id='{$parent_id}' ")->find();

            if($data['department_id']=="-1" && empty($parent_info)){
                $data['department_id']=$parent_id;
            }
			$service_id = $_REQUEST['service_id'];
//			if($data_service['department_s'] == 110){
//				$data['department_id'] = input('post.department_id_unit');
//			}else if($data_service['department_s'] == 111 && input('post.department_id1')<>0){
//				$data['department_id'] = input('post.department_id1');
//			}else if($data_service['department_s'] == 111 && input('post.department_id1')==0){
//				$data['department_id'] = input('post.department_id');
//			}

            if ( $data['department_id']=="-1") {
                $this->error('请选择单位');
            }
			//a($data['department_id']);
			$member  =   new \app\common\model\Member;
			$member_info = $member->where("member_id = '{$data['member_id']}'" )->find();
			/*
			if(!empty($member_info)){
			echo("会员可以查到");
			    //$this->error("该活动已存在!");  
			}
			if(!empty($memberservice_info)){
			    echo("service_id可以查到！！");
			    //$this->error("该活动已存在!");
			}
			exit;*/
			if (!empty($member_info)) {
				//插入 tp_member_service 记录
				$member->save($data,"member_id='{$data['member_id']}'");

				parent::operating(request()->path(),0,'修改会员信息：'.$data['member_id']);
				$this->success('修改会员信息成功',url('Member/memberedit','member_id='.$data['member_id']));
			}else {
				parent::operating(request()->path(),1,'更新失败：'.$data['member_id']);
				$this->error($member->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//个别信息修改
	public function tedit() {
		//parent::win_userauth(18);
		$id = session('ThinkActivities.ID');
		$activities= new \app\common\model\Activities;
		$data=array('ID' => $id);
		if ($result=$activities->where($data)->find()) {
			$this->assign('result',$result);
			//获取权限数据
			return $this->fetch('tedit');
		}else {
			parent::operating(request()->path(),1,'没有找到相关数据：'.$id);		
			$this->assign('content','没有找到相关数据，请关闭本窗口');
			$this->fetch('public/err');
		}
	}
	
	//删除数据
	public function member_del() {
		parent::userauth(135);

		if (request()->isAjax()) {
		    $member_id  =   input('post.member_id');
		    $serviceteam_id  =   input('post.serviceteam_id');
		    if ($member_id=='' || !is_numeric($member_id)) {
				parent::operating(request()->path(),1,'参数错误');
				return array('s'=>'参数ID类型错误');
			}else {
			    $member_id =   intval($member_id);
				$member =   new \app\common\model\Member;

				$where  =   array('member_id'=>$member_id);
				$where2  =   array('id'=>$member_id);
				if ($member->where($where)->value('member_id')) {
					if ($serviceteam_id <> '') 
					{
						$member->where($where)->delete();
					}
				    parent::operating(request()->path(),0,'删除成功：'.$member_id);
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
	//批量删除
	public function in_member_del() {
		//验证用户权限
		//parent::userauth(135);
		if (request()->isAjax()) {
			if (!$delid=explode(',',input('post.delid'))) {
				return array('s'=>'请选中后再删除');
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			if (in_array(1,$delid)) {
				return array('s'=>'不能删除ID号为1的数据');
			}
			$member_id=join(',',$delid);
			$member= new \app\common\model\Member;
			//if ($member->save(array('is_delete' => 1),$map)) {
			if ($member->where('member_id','IN',$member_id)->delete()) {
			    parent::operating(request()->path(),0,'批量删除ID为：'.$member_id.'的数据');
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'批量删除失败：'.$member->getError());
				return array('s'=>$member->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	public function batch_member_del() {
        if (!$delid=explode(',',input('param.delid'))) {
            return array('s'=>'请选中后再删除');
        }
        //将最后一个元素弹出栈
        array_pop($delid);
        $id=join(',',$delid);
        $member= new \app\common\model\Member;
        if ($member->where('ID','IN',$id)->delete()) {
            return array('s'=>'ok');
        }else {
            return array('s'=>$member->getError());
        }
	}
	//添加处理
	public function memberexport() {
		//验证用户权限
		parent::win_userauth(136);
		\think\Loader::import('PHPExcel.PHPExcel');
		\think\Loader::import('PHPExcel.PHPExcel.IOFactory');
		$PHPExcel  = new \PHPExcel();
		$PHPReader = new \PHPExcel_Reader_Excel2007();
		/*数据查询,与列表页保持一致，开始*/
        $department_id = input('request.department_id');
        $department_type = input('request.department_type');
        $parent_id = input('request.parent_id');
        $real_name = input('request.real_name');
        $member_id = input('request.id');
        $keywords = input('request.keywords');

        $member = new \app\common\model\Member;

        //查询部门库
        $department = new \app\common\model\Department;
        $dmenu = new \app\common\model\Dmenu;

        $department_list =$department->where('department_type = 118')->order('id ASC')->select();
        $dmenu_list =$dmenu->where('Sid = 109')->order('Sortid ASC')->select();

        $where = array();
        if($keywords!=""){
            $where['keywords'] = $keywords;
        }
        if($real_name!=""){
            $where['real_name']= $real_name;
        }
        if($parent_id!=""){
            //数据库查询
            $parent_info = $department->where("parent_id='{$parent_id}' ")->find();
            if(empty($parent_info)){
                $where['department_id']= $parent_id ;
            }else{
                $where['parent_id']= $parent_id ;
            }
        }
        if($department_id!=""){
            $where['department_id']= $department_id ;
        }
        if($department_type!=""){
            $where['department_type']= $department_type ;
        }

//		$lists  = $member->where($where)->order('member_id ASC')->paginate();
//		//$lists   = $member->paginate(config('paginate.list_rows'),$volist['total']);
//		$volist = $lists->toArray();
        $volist = $member->getMemberList($where);
        foreach($volist['data'] as $k=>$v){

            if($volist['data'][$k]['sex']=='73')
            {
                $volist['data'][$k]['sex_name']    ="男";
            }else {
                $volist['data'][$k]['sex_name']    ="女";
            }
            $volist['data'][$k]['department_name']    =$department->where("id='{$volist['data'][$k]['department_id']}'")->value('department_name');
        }
        $lists   = $member->paginate(config('paginate.list_rows'),$volist['total'],array('query'=>$where));
        if($department_type!=""){
            $parent_id_list = $department->where("department_type = '{$department_type}' AND parent_id=0 ")->order('id ASC')->select();
        }
        if($parent_id!=""){
            $department_id_list = $department->where("parent_id = '{$parent_id}'")->order('id ASC')->select();
        }
		/*数据查询,与列表页保持一致，结束*/
		$save_filename = "会员信息导出";
		$objPHPExcel = new \PHPExcel();
		// Set properties
		$objPHPExcel->getProperties()->setCreator('www');
		$objPHPExcel->getProperties()->setLastModifiedBy('www');
		$objPHPExcel->getProperties()->setTitle('www');
		$objPHPExcel->getProperties()->setSubject('www');
		$objPHPExcel->getProperties()->setDescription('www'.$save_filename);
		$objPHPExcel->getProperties()->setKeywords('www'.$save_filename);
		$objPHPExcel->getProperties()->setCategory('会员信息');
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
		
		if($where['department']!=""){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "会员信息（{$volist['data'][0]['department_name']}）");
		}else if($where['serviceteam_id']!=""){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "会员信息（{$volist['data'][0]['service_name']}）");
		}else {
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "会员信息");
		}
		$ki = 2;
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$ki, '序号')
		->setCellValue('B'.$ki, '真实姓名')
		->setCellValue('C'.$ki, '性别')
        ->setCellValue('D'.$ki, '政治面貌')
        ->setCellValue('E'.$ki, '手机号码')
		->setCellValue('F'.$ki, '身份证号码')
		->setCellValue('G'.$ki, '注册时间')
        ->setCellValue('H'.$ki, '部门类型')
        ->setCellValue('I'.$ki, '上级部门名称')
        ->setCellValue('J'.$ki, '部门名称')
		;
		$sheet = $objPHPExcel->getActiveSheet();
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(35);
        foreach ($volist['data'] as $k=>$v){
			$i = $k+3;
            $j = $k+1;
            $objPHPExcel->getActiveSheet()->getStyle($i)->getAlignment()->setWrapText(true);

			$sheet
            ->setCellValueExplicit("A{$i}", $j, \PHPExcel_Cell_DataType::TYPE_NUMERIC)
			->setCellValueExplicit("B{$i}", $v['real_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("C{$i}", $v['sex_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit("D{$i}", $v['political_status'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit("E{$i}", $v['phone'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("F{$i}", $v['id_number'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("G{$i}", $v['register_time'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("H{$i}", $v['department_type_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit("I{$i}", $v['parent_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit("J{$i}", $v['department_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ;
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
		header('Content-Disposition: attachment;filename="'.iconv("UTF-8", "GBK", $save_filename).'.xlsx"');
		header('Cache-Control: max-age=0');
		
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save("php://output");
		exit;
	}
	//添加处理
	public function talentexport() {
		//验证用户权限
		parent::win_userauth(124);
		\think\Loader::import('PHPExcel.PHPExcel');
		\think\Loader::import('PHPExcel.PHPExcel.IOFactory');
		$PHPExcel  = new \PHPExcel();
		$PHPReader = new \PHPExcel_Reader_Excel2007();
		/*数据查询,与列表页保持一致，开始*/
		$department = input('request.department');
		$post_id = input('request.post');
		$degree = input('request.degree');
		$term = input('request.term');
		$school_level = input('request.school_level');
		$id = input('request.id');
		$keywords = input('request.keywords');
		
		$where = array();
		if($keywords!=""){
			$where['name|sex|hometown|id_number|phone|department|school_ranking|degree|profession|email'] = array("LIKE","%$keywords%");
		}
		if($department!=""){
			$where['department']= $department;
		}
		if($post_id!=""){
			$where['post']= $post_id ;
		}
		if($degree!=""){
			$where['degree']= $degree ;
		}
		if($term!=""){
			$where['term']= $term ;
		}
		$where['is_delete']= 1;
		if($school_level =="double_first_rate"){
			$where['school_level']= $school_level;
		}
		if($school_level =="is_province"){
			$where['is_province']= "yes" ;
			
		}
		if($school_level =="other"){
			$where['is_province'] = null;
			$where['school_level']= null;
		}
		$member = new \app\common\model\Member;
		$department = new \app\common\model\Department;
		$department_list =$department->where('id<>0')->select();
		
		$post = new \app\common\model\Post;
		$post_list =$post->where('id<>0')->select();
		
		$where_string ="";
		$lists  = $jinali->where($where)->order('id DESC')->paginate(9999);
		
		$volist = $lists->toArray();
		foreach($volist['data'] as $k=>$v){
			$volist['data'][$k]['department_name']    = $department->where("id='{$v['department']}'")->value('department_name');
			$volist['data'][$k]['post_name']    = $post->where("id='{$v['post']}'")->value('post_name');
		}
		/*数据查询,与列表页保持一致，结束*/
		
		
		
		$save_filename = "简历信息导出";
		$objPHPExcel = new \PHPExcel();
		// Set properties
		$objPHPExcel->getProperties()->setCreator('www');
		$objPHPExcel->getProperties()->setLastModifiedBy('www');
		$objPHPExcel->getProperties()->setTitle('www');
		$objPHPExcel->getProperties()->setSubject('www');
		$objPHPExcel->getProperties()->setDescription('www'.$save_filename);
		$objPHPExcel->getProperties()->setKeywords('www'.$save_filename);
		$objPHPExcel->getProperties()->setCategory('简历信息');
		// Add some data
		$objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setBold(true);      //第一行是否加粗
		$objPHPExcel->getActiveSheet(0)->getStyle('A2:P2')->getFont()->setBold(true);      //第二行是否加粗
		$objPHPExcel->getActiveSheet(0)->getStyle('A1')->getFont()->setSize(18);         //第一行字体大小
		// 设置垂直居中
		$objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		// 设置水平居中
		$objPHPExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet(0)->getRowDimension('1')->setRowHeight(38);    //第一行行高
		$objPHPExcel->getActiveSheet(0)->mergeCells('A1:P1');
		
		if($post_id!=""){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "甘肃日报报业集团公司2020年公开招聘简历投递信息名册（{$volist['data'][0]['post_name']}）");
		}else if($where['department']!=""){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "甘肃日报报业集团公司2020年公开招聘简历投递信息名册（{$volist['data'][0]['department_name']}）");
		}else{
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "甘肃日报报业集团公司2020年公开招聘简历投递信息名册");
		}
		$ki = 2;
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$ki, '编号')
		->setCellValue('B'.$ki, '姓名')
		->setCellValue('C'.$ki, '性别')
		->setCellValue('D'.$ki, '出生年月')
		->setCellValue('E'.$ki, '籍贯')
		->setCellValue('F'.$ki, '学历')
		->setCellValue('G'.$ki, '毕业院校')
		->setCellValue('H'.$ki, '学校排名')
		->setCellValue('I'.$ki, '学校层次')
		->setCellValue('J'.$ki, '专业')
		->setCellValue('K'.$ki, '学业绩点')
		->setCellValue('L'.$ki, '应/往届')
		->setCellValue('M'.$ki, '联系方式')
		->setCellValue('N'.$ki, '邮箱地址')
		->setCellValue('O'.$ki, '部门')
		->setCellValue('P'.$ki, '岗位');
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->getColumnDimension('A')->setWidth(10);
		foreach ($volist['data'] as $k=>$v){
			$i = $k+3;
			$sheet->setCellValueExplicit("A{$i}", $v['id'], \PHPExcel_Cell_DataType::TYPE_NUMERIC)
			->setCellValueExplicit("B{$i}", $v['name'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("C{$i}", $v['sex'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("D{$i}", $v['birthday'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("E{$i}", $v['hometown'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("F{$i}", $v['degree'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("G{$i}", $v['graduated_school'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("H{$i}", $v['school_ranking'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("I{$i}", $v['school_level'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("J{$i}", $v['profession'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("K{$i}", $v['grade'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("L{$i}", $v['term'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("M{$i}", $v['phone'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("N{$i}", $v['email'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("O{$i}", $v['department_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit("P{$i}", $v['post_name'], \PHPExcel_Cell_DataType::TYPE_STRING);
		}
		$styleThinBlackBorderOutline = array(
				'borders' => array(
						'allborders' => array( //设置全部边框
								'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
						),
				),
		);
		$objPHPExcel->getActiveSheet(0)->getStyle("A1:P{$i}")->applyFromArray($styleThinBlackBorderOutline);
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
//个人积分明细、积分列表
    public function points_log(){
        $member_id = input('request.member_id');

        $points_log  = new \app\common\model\Pointslog;
    //获取积分表数据
        $points=$points_log->where(['member_id'=>$member_id])->order('id desc')->column('id,member_name,points,add_time,desc');

    //将获取到的数据返回至edit模板
        $this->assign('points',$points);
        return $this->fetch('points');
    }


}
