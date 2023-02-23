<?php
//项目管理类
namespace app\exam\controller;
use think\Controller;
use think\Request;
use think\Url;
class Department extends Admin {
	public function index() {
		parent::userauth2(138);
        $department_type         = input('request.department_type');
        $parent_id        = input('request.parent_id');
        $keywords        = input('request.keywords');
        $where = array();
        if($department_type!=""){
            $where['department_type'] = array("LIKE","%$department_type%");
        }else{

        }
        if($parent_id!=""){
            $where['parent_id'] = $parent_id;
        }else{
            $where['parent_id'] =0;
        }

        if($keywords!=""){
            $where['department_name'] = array("LIKE","%$keywords%");
        }

        $department=new \app\common\model\Department;
        $dmenu = new \app\common\model\Dmenu;
        $list = $department->where($where)->paginate(null,false,array('query'=>$where));
        $department_list = $list->toArray();
        foreach($department_list['data'] as $k=>$v){
            //获取人员对应的职责信息
            $department_list['data'][$k]['department_type_name'] = $dmenu->where(['ID'=>$department_list['data'][$k]['department_type']])->value('MenuName');
            if($department_list['data'][$k]['parent_id']==0){
                $department_list['data'][$k]['parent_name']='无';
            }else{
                $department_list['data'][$k]['parent_name'] = $department->where(['id'=>$department_list['data'][$k]['parent_id']])->value('department_name');
            }
        }

        $this->assign('department_list',$department_list);
        $this->assign('list',$list);
        return $this->fetch();
	}

	//新增部门
	public function add() {
		parent::win_userauth(139);
		$dmenu = new \app\common\model\Dmenu;
		$user  = new \app\common\model\User;
		$department  = new \app\common\model\Department;
        $parent_id   = input('request.parent_id');
		$dmenu_list=$dmenu->where('Sid = 109')->order('Sortid asc')->select();
		$this->assign('dmenu_list',$dmenu_list);
		$user_list=$user->where('Status = 0')->order('ID ASC')->select();
		$this->assign('user_list',$user_list);
		$department_info=$department->where("id= '{$parent_id}'")->find();
		$department_info['parent_type_name'] =$dmenu->where("ID='{$department_info['department_type']}'")->value("MenuName");
		$this->assign('department_info',$department_info);
		return $this->fetch();
	}
	//添加处理
	public function add_do() {
		//验证用户权限
		parent::win_userauth(139);
		if (request()->isPost()) {
			$data=array();
			$cont=array();
			$data['department_name']    = input('post.department_name');
			$data['parent_id']          = input('post.parent_id');
			$data['parent_name']        = input('post.parent_name');
			$data['address']        = input('post.address');
			$data['telephone']        = input('post.telephone');
			$data['department_type']    = input('post.department_type');
			$reg  ='/^1[3456789]{1}\d{9}$/';
			if($data['telephone'] !="" && !preg_match($reg ,$data['telephone']  )){
                $this->error("手机号码格式不正确，请重新输入！");
            }
			$department=   new \app\common\model\Department;
			$department_info = $department->where("department_name = '{$data['department_name']}'")->find();
			if(!empty($department_info)){
			    $this->error("该部门已存在!");
			}
			if ($department->save($data)) {
//				$department_id = $department->id;
				/*
				 * 增加乡镇村默认生成对应的所站服务队及登录账户
				 * */
//				if($data['department_type']==111){
//					$service	=   new \app\common\model\Service;
//					$member 	=   new \app\common\model\Member;
//					$service_data = array();
//					if($data['parent_id']==0){
//						$service_data['service_name'] = $data['department_name']."所";
//						$service_data['parent_id']    = 0;
//					}else{
//						$service_data['service_name'] = $data['department_name']."站";
//						$parent_info = $service->where("department_id='{$data['parent_id']}'")->find();
//						$service_data['parent_id']    = $parent_info['id'];
//					}
//					$member_data = array();
//					$member_data['member_name'] 	= $service_data['service_name'];
//					$member_data['real_name'] 		= $service_data['service_name'];
//					$member_data['type'] 			= 2;
//					$member_data['register_time'] 	= date("Y-m-d H:i:s");
//					$member_data['member_passwd'] 	= sha1(md5(config("service.service_member_default_password")));
//					$member->save($member_data);
//					$service_data['service_cate']       = 117;
//					$service_data['department_id']    	= $department_id;
//					$service_data['service_function']   = "";
//					$service_data['address']   			= $data['address'];
//					$service_data['telephone']   		= $data['telephone'];
//					$service_data['member_id']    		= $member->member_id;
//					$service->save($service_data);
//				}
			    parent::operating(request()->path(),0,'新增部门：'.$data['department_name']);
				$this->success('添加成功',url('Department/add'),3);
			}else {
				parent::operating(request()->path(),1,'新增失败：'.$department->getError());
				$this->error($department->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//修改部门资料
    public function edit() {
        parent::win_userauth(140);
        $id = input('param.id');
        if ($id=='' || !is_numeric($id)) {

            parent::operating(request()->path(),1,'参数错误');
            $this->error('参数ID类型错误，请关闭本窗口');
        }
        $uid = session('ThinkUser.ID');
        //下拉菜单数据
        $dmenu = new \app\common\model\Dmenu;
        $department  = new \app\common\model\Department;
        $dmenu_list=$dmenu->where('Sid = 109')->order('Sortid asc')->select();
        //查出相应数据
        $department = new \app\common\model\Department();
        $result = $department->where("id = '{$id}' ")->find();
        if (!$result) {
            parent::operating(request()->path(),1,'没有找到数据：'.$id);
            $this->error('不存在你要修改的数据，请关闭本窗口');
        }
// 		$user = new \app\common\model\User;
// 		$user_list=$user->where("Status=0")->column('');
        $result['parent_name'] = $department->where("id='{$result['parent_id']}'")->value('department_name');
// 		$this->assign('user_list',$user_list);
        $department_list=$department->where('parent_id = 0')->order('id ASC')->select();
        $this->assign('department_list',$department_list);
        $department_info=$department->where("id= '{$parent_id}'")->find();
        $this->assign('dmenu_list',$dmenu_list);
        $this->assign('result',$result);
        return $this->fetch();
    }
    //修改部门资料处理
    public function edit_do() {
        //验证用户权限
        parent::win_userauth(140);
        if (request()->isPost()) {
            $data=array();
            //部门资料信息
            $data['department_name']       = input('post.department_name');
            $data['id']    	= input('post.id');
            $data['department_type']       = input('post.department_type');
            $data['parent_id']        	= input('post.parent_id');
            $data['address']        	= input('post.address');
            $data['telephone']        	= input('post.telephone');
            $reg  ='/^1[3456789]{1}\d{9}$/';
            if($data['telephone'] !="" && !preg_match($reg ,$data['telephone']  )){
                $this->error("手机号码格式不正确，请重新输入！");
            }

            $department=   new \app\common\model\Department;
            $department_info = $department->where("department_name = '{$data['department_name']}' AND id != '{$data['id']}'")->find();
            if(!empty($department_info)){
                $this->error("该部门已存在!");
            }
            if ($department ->save($data,"id='{$data['id']}'")) {
            	/*
            	 * 修改乡镇村默认生成对应的所站服务队及登录账户
            	 * */
            	if($data['department_type']==111){
            		$service	=   new \app\common\model\Service;
            		$member 	=   new \app\common\model\Member;
            		$service_data = array();
            		if($data['parent_id']==0){
            			$service_data['service_name'] = $data['department_name']."所";
            			$service_data['parent_id']    = 0;
            		}else{
            			$service_data['service_name'] = $data['department_name']."站";
            			$parent_info = $service->where("department_id='{$data['parent_id']}'")->find();
            			$service_data['parent_id']    = $parent_info['id'];
            		}
            		$service_info = $service->where("department_id='{$data['id']}'")->find();
            		$member_data = array();
            		$member_data['member_name'] 	= $service_data['service_name'];
            		$member_data['real_name'] 		= $service_data['service_name'];
            		$member->save($member_data,"member_id='{$service_info['member_id']}'");
            		$service_data['address']   			= $data['address'];
            		$service_data['telephone']   		= $data['telephone'];
            		$service->save($service_data,"id='{$service_info['id']}'");
            	}
                parent::operating(request()->path(),0,'更新部门资料：'.$data['department_name']);
                $this->success('部门资料更新成功',url('Department/edit','id='.$data['id']));
            }else {
                parent::operating(request()->path(),1,'更新失败：'.$data['department_name']);
                $this->error($department->getError());
            }
        }else {
            parent::operating(request()->path(),1,'非法请求');
            $this->error('非法请求');
        }
    }
	//删除部门资料到回收站
	public function department_del() {
		parent::userauth(141);
		//判断是否是ajax请求
		if (request()->isAjax()) {
			$id  =   input('post.id');
			if ($id=='' || !is_numeric($id)) {
				parent::operating(request()->path(),1,'参数错误');
				return array('s'=>'参数ID类型错误');
			}else {
				$id =   intval($id);
				$department =   new \app\common\model\Department();
				$where  =   array('id'=>$id);
				if ($department->where($where)->value('id')) {
                    $department->where($where)->delete();
					parent::operating(request()->path(),0,'删除成功：'.$id);
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
	//批量删除部门资料到回收站
	public function department_indel() {
		//验证用户权限
		parent::userauth(141);
		if (request()->isAjax()) {
			if (!$delid=explode(',',input('post.delid'))) {
				return array('s'=>'请选中后再删除');
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
            $department  =   new \app\common\model\Department();
			$map['id'] = array('in',$id);
			if ($department->where($map)->delete()) {
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
	public function department_c_indel() {
		//验证用户权限
		//parent::userauth(131);
		if (request()->isAjax()) {
			if (!$delid=explode(',',input('post.delid'))) {
				return array('s'=>'请选中后再删除');
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$department  =   new \app\common\model\Department;
			if ($department->where('id','IN',$id)->delete()) {
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
	    $file = request()->file('file_upload');
	    //echo "<pre>";print_r($file);exit;
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    $result = array();
	    foreach($file as $k=>$v){
	        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads'. DS . 'department');
	        if($info){
	           $result[] = $info;
	        }else{
	           
	        } 
	    }
	    return $result;
	}
}
?>