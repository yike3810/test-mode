<?php
//项目管理类
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Url;

class Department extends Admin
{
    public function index()
    {
        parent::userauth2(303);

        $clues_tree_list = $this->getTreelist(1);
        //var_dump($clues_tree_list);exit;
        $this->assign('clues_tree_list', json_encode($clues_tree_list));
        return $this->fetch();
    }
    public function media()
    {
        parent::userauth2(299);
        $clues_tree_list = $this->getMediaTreelist(1);
        //var_dump($clues_tree_list);exit;
        $this->assign('clues_tree_list', json_encode($clues_tree_list));
        return $this->fetch();
    }

    //新增部门
    public function add()
    {
        parent::userauth2(321);

        $region_id = input('get.region_id');
//        a($region_id);
        $dmenu = new \app\common\model\Dmenu;
        $user = new \app\common\model\User;
        $department = new \app\common\model\Department;
        $parent_id = input('request.parent_id');
        $dmenu_list = $dmenu->where('Sid = 109')->order('Sortid asc')->select();
        $this->assign('dmenu_list', $dmenu_list);
        $user_list = $user->where('Status = 0')->order('ID ASC')->value("Password");
        $this->assign('user_list', $user_list);
        $department_info = $department->where(["department_id"=> $parent_id])->find();
        $department_info['parent_type_name'] = $dmenu->where(["ID"=>$department_info['department_type']])->value("MenuName");
        $this->assign('department_info', $department_info);
        $industry = new \app\common\model\Industry;
        $industry_list = $industry->order('sort asc')->select();
        $this->assign('industry_list',$industry_list);
        $leader = new \app\common\model\Leader;
        $leader_list = $leader->order('sort asc')->select();
        $this->assign('leader_list',$leader_list);
        $department = new \app\common\model\Department;//父级单位仅为省级单位，若有后续修改，请提出后修改
        $department_list = $department->where(['district'=>0])->order('parent_id asc')->column('');
        $this->assign('department_list',$department_list);
        return $this->fetch();
    }
    //添加处理
    public function add_do()
    {
        //验证用户权限
        parent::userauth(321);
        $request = Request::instance();
        $department = new \app\common\model\Department;
        $region = new \app\common\model\Region;
        $data = array();
        $data['industry_id'] = input('industry_id');
        $data['department_name'] = input('department_name');
        $data['telephone'] = input('telephone');
        $data['contact'] = input('contact');
        $data['leader_id'] = input('leader_id');
        $data['phone'] = input('phone');
        $data['parent_id'] = input('parent_id');
        $region_id= input('region_id');
        $reg = '/^1[3456789]{1}\d{9}$/';
        if ($data['phone'] != "" && !preg_match($reg, $data['phone'])) {
            return array('s'=>'手机号码格式不正确，请重新输入!');
        }
        $data['add_time'] = date("Y-m-d H:i:s");
        $where['region_id']= $region_id;
        $region_info =$region->where($where)->find();
        if ($region_info['region_type']== "1") {
            $data['province'] = $region_id;
            $data['city'] = 0;
            $data['district'] = 0;
        } elseif ($region_info['region_type']== "2") {
            $data['province'] = $region_info['parent_id'];
            $data['city'] = $region_id;
            $data['district'] = 0;
        } elseif ($region_info['region_type']== "3") {
            $parent_info =$region->where(["region_id"=>$region_info['parent_id']])->find();
            $data['province'] = $parent_info['parent_id'];
            $data['city'] = $region_info['parent_id'];
            $data['district'] = $region_id;
        }
        $data['status'] = 1;
        $department_info = $department->where(["department_name" => $data['department_name']])->whereOr(["phone" =>$data['phone']])->find();
        if (!empty($department_info)) {
            return array('s'=>'该单位名称或联系人手机号码已存在，请核对！');
        }
         try {
                $department->save($data);
                $member = new \app\common\model\Member;
                $member_data = array();
                $member_data['member_name'] =$data['department_name'];
                $member_data['department_id'] =$department->department_id;
                $member_data['phone'] = $data['phone'];
                $member_data['telephone'] = $data['telephone'];
                $member_data['real_name'] = $data['contact'];
                $member_data['member_passwd'] = input('Password');
                $member_data['member_passwd'] = sha1(md5($member_data['member_passwd']));
                $member_data['id_number'] = input('id_number');
                $member_data['register_time'] = $department->add_time;
                $member->save($member_data);
                $data['member_id'] =$member->member_id;
                $department->save($data);
//             $department->save(array("member_id"=>$member->member_id),"department_id='{$department->department_id}'");
            return array("s" =>'ok');
        } catch (Exception $e) {
        return array('s'=>$e->getMessage());
    }

    }

    //修改部门资料
    public function edit()
    {
        parent::userauth2(306);

        $department_id = input('get.department_id');
        $member = new \app\common\model\Member;
        $industry = new \app\common\model\Industry;
        $industry_list = $industry->order('industry_id asc')->select();
        $this->assign('industry_list',$industry_list);
        $leader = new \app\common\model\Leader;
        $leader_list = $leader->order('leader_id asc')->select();
        $this->assign('leader_list',$leader_list);
        $department = new \app\common\model\Department;
        $department_list = $department->where(['district'=>0])->order('parent_id asc')->column('');
        $this->assign('department_list',$department_list);
        if ($result=$department->where(["department_id"=>$department_id])->find()) {
            $department_info=$department->where(["department_id"=>$department_id])->find();
            $result['id_number'] = $member->where(["department_id"=>$department_id])->value("id_number");
            $result['member_passwd'] = $member->where(["department_id"=>$department_id])->value("member_passwd");
            $result['leader_name'] = $leader->where(["leader_id"=>$department_info['leader_id']])->value("leader_name");
            $result['industry_name'] = $industry->where(["industry_id"=>$department_info['industry_id']])->value("industry_name");

            $this->assign('result',$result);
            return $this->fetch();
        }else {
            parent::operating(request()->path(), 1, '数据不存在');
            $this->error('没有找到相关数据');
        }
    }
    //修改部门资料处理
    public function edit_do()
    {if (request()->isAjax()) {
			$data = array();
			$department_id              = input('post.department_id');
			$data['department_name']    = input('post.department_name');
			$data['telephone']          = input('post.telephone');
			$data['contact']  	        = input('post.contact');
			$data['leader_id']       	= input('post.leader_id');
            $data['industry_id']       	= input('post.industry_id');
            $data['phone']       	    = input('post.phone');
            $data1['id_number']       	= input('post.id_number');
            $data['parent_id']       	= input('post.parent_id');
            $reg = '/^1[3456789]{1}\d{9}$/';
            if ($data['phone'] != "" && !preg_match($reg, $data['phone'])) {
            $this->error("手机号码格式不正确，请重新输入！");
            }
        if(input('post.member_passwd') != "") {
            $data1['member_passwd'] = sha1(md5(input('post.member_passwd')));
        }
            $department = new \app\common\model\Department;
            $member = new \app\common\model\Member;
            $leader = new \app\common\model\Leader;
			//自动创建并验证数据
			$department->save($data,"department_id=".$department_id);
                $department_info=$department->where(["department_id"=>$department_id])->find();
                $data1['member_name']   =$data['department_name'] ;
                $data1['phone']         =$data['phone'] ;
                $data1['real_name']     =$data['contact'] ;
                $data1['telephone']     =$data['telephone'] ;
            $member->save($data1,"department_id=".$department_id);
				parent::operating(request()->path(),0,'更新成功');
				return array('s'=>'ok');
//                parent::operating(request()->path(), 1, '更新失败：' . $department->getError());
//                return array('s' => $department->getError());
		}else {
    parent::operating(request()->path(),1,'非法请求');
    $this->error('非法请求');
}
	}
    //删除部门资料到回收站
    public function department_del()
    {
        parent::userauth(141);
        //判断是否是ajax请求
        if (request()->isAjax()) {
            $id = input('post.department_id');
            if ($id == '' || !is_numeric($id)) {
                parent::operating(request()->path(), 1, '参数错误');
                return array('s' => '参数ID类型错误');
            } else {
                $id = intval($id);
                $department = new \app\common\model\Department();
                $where = array('department_id' => $id);
                if ($department->where($where)->value('department_id')) {
                    $department->where($where)->delete();
                    parent::operating(request()->path(), 0, '删除成功：' . $id);
                    return array('s' => 'ok');
                } else {
                    parent::operating(request()->path(), 1, '删除失败：' . $this->getError());
                    return array('s' => $this->getError());
                }
            }
        } else {
            parent::operating(request()->path(), 1, '非法请求');
            $this->error('非法请求');
        }
    }

    //批量删除部门资料到回收站
    public function department_indel()
    {
        //验证用户权限
        parent::userauth(141);
        if (request()->isAjax()) {
            if (!$delid = explode(',', input('post.delid'))) {
                return array('s' => '请选中后再删除');
            }
            //将最后一个元素弹出栈
            array_pop($delid);
            $id = join(',', $delid);
            $department = new \app\common\model\Department();
            $map['id'] = array('in', $id);
            if ($department->where($map)->delete()) {
                parent::operating(request()->path(), 0, '删除成功：' . $id);
                return array('s' => 'ok');
            } else {
                parent::operating(request()->path(), 1, '删除失败：' . $id);
                return array('s' => '删除失败');
            }
        } else {
            parent::operating(request()->path(), 1, '非法请求');
            $this->error('非法请求');
        }
    }

    //批量删除
    public function department_c_indel()
    {
        //验证用户权限
        //parent::userauth(131);
        if (request()->isAjax()) {
            if (!$delid = explode(',', input('post.delid'))) {
                return array('s' => '请选中后再删除');
            }
            //将最后一个元素弹出栈
            array_pop($delid);
            $id = join(',', $delid);
            $department = new \app\common\model\Department;
            if ($department->where('id', 'IN', $id)->delete()) {
                parent::operating(request()->path(), 0, '删除成功');
                return array('s' => 'ok');
            } else {
                parent::operating(request()->path(), 1, '删除失败');
                return array('s' => '删除失败');
            }
        } else {
            parent::operating(request()->path(), 1, '非法请求');
            R('Public/errjson', array('非法请求'));
        }
    }

    //下载文件
    public function down()
    {
        parent::userauth2(121);
        $string = input('get.filename');
        if (file_exists($string)) {
            $string = iconv("utf-8", "gb2312", $string);
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=" . basename($string));
            readfile($string);
        } else {
            parent::operating(request()->path(), 1, '文件不存在：' . $string);
            $this->error('文件不存在');
        }
    }

    public function upload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file_upload');
        //echo "<pre>";print_r($file);exit;
        // 移动到框架应用根目录/public/uploads/ 目录下
        $result = array();
        foreach ($file as $k => $v) {
            $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'department');
            if ($info) {
                $result[] = $info;
            } else {

            }
        }
        return $result;
    }

    public function tree()
    {
        parent::userauth2(172);
        $clues_id = input('request.clues_id');
        $sid = input('request.sid');


        //(b($exam_tree_list));
// 		a(json_encode($clues_tree_list));
        $clues_tree_list = $this->getTreelist(1);
        //a($exam_tree_list);
        $this->assign('clues_tree_list', json_encode($clues_tree_list));
        $this->assign('clues_id', $clues_id);
        return $this->fetch();
    }

    public function getTreelist($parent_id)
    {
        $clues_tree_list = array();
        $region = new \app\common\model\Region;
        $where['parent_id'] = $parent_id;
        if($parent_id==1){
//        	$where['region_id'] = 29;
        }
        $clues_list = $region->where($where)->order("sort ASC")->column('');
        foreach ($clues_list as $k => $v) {
            $flag = false;
            if ($v['region_type'] == 1) {
                $flag = true;
            }
            $clues_children = array(
                'id' => $v['region_id'],
                'title' => $v['region_name'],
                'spread' => $flag,
                'children' => $this->getTreelist($v['region_id']),
            );
            if (!$parent_id) {
                $clues_tree_list[$parent_id]['children'][] = $clues_children;
            } else {
                $clues_tree_list[] = $clues_children;
            }
        }
        return $clues_tree_list;
    }
    public function getMediaTreelist($parent_id)
    {
    	$clues_tree_list = array();
    	$region = new \app\common\model\Region;
    	$where['parent_id'] = $parent_id;
    	$clues_list = $region->where($where)->order("sort ASC")->column('');
    	foreach ($clues_list as $k => $v) {
    		$flag = false;
    		if ($v['region_type'] == 1) {
    			$flag = true;
    		}
    		if($v['region_type']==2){		
	    		$clues_children = array(
	    				'id' => $v['region_id'],
	    				'title' => $v['region_name'],
	    				'spread' => $flag,
	    		);
    		}elseif ($v['region_type'] == 1){
    			$clues_children = array(
    					'id' => $v['region_id'],
    					'title' => $v['region_name'],
    					'spread' => $flag,
    					'children' => $this->getMediaTreelist($v['region_id']),
    			);
    		}
    		if (!$parent_id) {
    			$clues_tree_list[$parent_id]['children'][] = $clues_children;
    		} else {
    			$clues_tree_list[] = $clues_children;
    		}
    	}
    	return $clues_tree_list;
    }
    public function getDepartmentList()
    {
        parent::userauth2(303);

        $department = new \app\common\model\Department;
        $region = new \app\common\model\Region;
        $leader = new \app\common\model\Leader;
        $industry= new \app\common\model\Industry;
        $where = $where_d = array();
        $region_id = input('request.region_id');
        $limit = input('request.limit');
        $is_admin_array =$department->is_admin_array;
        $keywords = input('request.keywords');
        if($keywords!=""){
            $where_d['department_name|telephone|contact|phone'] = array("LIKE","%$keywords%");
        }

        //获取地区信息
        $where['region_id']= $region_id;
        $region_info =$region->where($where)->find();
        //判断 $region_type 的值  1 province  2 city 3 district
            if ($region_info['region_type']== "1") {
                $where_d['province'] = $region_id;
            } elseif ($region_info['region_type']== "2") {
                $where_d['city'] = $region_id;
            } elseif ($region_info['region_type']== "3") {
                $where_d['district'] = $region_id;
            }
        $where_d['status']= 1;
//        $where_d['industry_id'] = array("neq",1);
        $list = $department->where($where_d)->paginate($limit);
        foreach ($list as $k=>$v){
            $list[$k]['province'] 	= $region->where(["region_id"=>$v['province']])->value("region_name");
            $list[$k]['city'] 		= $region->where(["region_id"=>$v['city']])->value("region_name");
            $list[$k]['district'] 	= $region->where(["region_id"=>$v['district']])->value("region_name");
            $list[$k]['region_name']    = $list[$k]['province'].$list[$k]['city'].$list[$k]['district'];
            $list[$k]['is_admin_name']  = $is_admin_array[$list[$k]['is_admin']];
            $list[$k]['leader']         = $leader->where(["leader_id"=>$v['leader_id']])->value("leader_name");
            $list[$k]['industry']       = $industry->where(["industry_id"=>$v['industry_id']])->value("industry_name");
        }
        $department_list = $list->toArray();
        $result = array("code" => 0, "count" => $department_list['total'], "data" => $department_list['data']);
        echo json_encode($result);
        exit;
    }
    public function getMediaDepartmentList()
    {
        parent::userauth2(299);

        $department = new \app\common\model\Department;
        $region = new \app\common\model\Region;
        $leader = new \app\common\model\Leader;
        $industry= new \app\common\model\Industry;
        $where = $where_d = array();
        $region_id = input('request.region_id');
        $limit = input('request.limit');
        $is_admin_array =$department->is_admin_array;
        $keywords = input('request.keywords');
        if($keywords!=""){
            $where_d['department_name|telephone|contact|phone'] = array("LIKE","%$keywords%");
        }

        //获取地区信息
        $where['region_id']= $region_id;
        $region_info =$region->where($where)->find();
        //判断 $region_type 的值  1 province  2 city 3 district
            if ($region_info['region_type']== "1") {
                $where_d['province'] = $region_id;
            } elseif ($region_info['region_type']== "2") {
                $where_d['city'] = $region_id;
            } elseif ($region_info['region_type']== "3") {
                $where_d['district'] = $region_id;
            }
        $where_d['status']= 1;
        $where_d['industry_id']= 1;
        $list = $department->where($where_d)->paginate($limit);
        foreach ($list as $k=>$v){
            $list[$k]['province'] 	= $region->where(["region_id"=>$v['province']])->value("region_name");
            $list[$k]['city'] 		= $region->where(["region_id"=>$v['city']])->value("region_name");
            $list[$k]['district'] 	= $region->where(["region_id"=>$v['district']])->value("region_name");
            $list[$k]['region_name']     = $list[$k]['province'].$list[$k]['city'].$list[$k]['district'];
            $list[$k]['is_admin_name'] = $is_admin_array[$list[$k]['is_admin']];
            $list[$k]['leader']         = $leader->where(["leader_id"=>$v['leader_id']])->value("leader_name");
            $list[$k]['industry']         = $industry->where(["industry_id"=>$v['industry_id']])->value("industry_name");
        }
        $department_list = $list->toArray();
        $result = array("code" => 0, "count" => $department_list['total'], "data" => $department_list['data']);
        echo json_encode($result);
        exit;
    }
    public function pending() {
        parent::userauth2(308);

        return $this->fetch();
    }
    public function getPendingList()
    {
        parent::userauth2(308);

        $department = new \app\common\model\Department;
        $region 	= new \app\common\model\Region;
        $leader = new \app\common\model\Leader;

        $where =$where_d= array();
        $keywords 	= input('request.keywords');
        $limit 		= input('request.limit');
        if($keywords!=""){
            $where_d['department_name|telephone|contact|phone'] = array("LIKE","%$keywords%");
        }
        $where['status'] = 0;
        $lists = $department->where($where)->where($where_d)->order("department_id DESC")->paginate($limit);
        $department_list = $lists->toArray();
        foreach ($department_list['data'] as $k=>$v){
            $department_list['data'][$k]['province_name'] 	= $region->where(["region_id"=>$v['province']])->value("region_name");
            $department_list['data'][$k]['city_name'] 		= $region->where(["region_id"=>$v['city']])->value("region_name");
            $department_list['data'][$k]['district_name'] 	= $region->where(["region_id"=>$v['district']])->value("region_name");
            $department_list['data'][$k]['region_name']     = $department_list['data'][$k]['province_name']
                .$department_list['data'][$k]['city_name'].$department_list['data'][$k]['district_name'];
            $department_list['data'][$k]['leader']          = $leader->where(["leader_id"=>$v['leader_id']])->value("leader_name");
        }
        $result = array("code"=>0,"count"=>$department_list['total'],"data"=>$department_list['data']);
        echo json_encode($result);exit;


    }
    public function chenggong() {
        parent::userauth(309);

        //判断是否是ajax请求
        if (request()->isAjax()) {
            $department_id = input('post.department_id');
            $department= new \app\common\model\Department;
            if ($department->save(array("status"=>1),"department_id='{$department_id}'")) {
                $smsdepartment = new \app\common\model\Smsdepartment;
                $phone=$department->where(["department_id"=>$department_id])->value("phone");
                $status =$smsdepartment->Smssendmessage($phone);
                parent::operating(request()->path(),0,'审核成功');
                return array('s'=>'ok','status'=>$status);
            }else {
                parent::operating(request()->path(),1,'审核失败');
                return array('s'=>'数据不存在');
            }
        }else {
            parent::operating(request()->path(),1,'非法请求');
            return array('s'=>'非法请求');
        }
    }
    public function reject() {
        parent::userauth(310);

        //判断是否是ajax请求
        if (request()->isAjax()) {
            $department_id = input('post.department_id');
            $department = new \app\common\model\Department;
            $member = new \app\common\model\Member;
            if ($department->where(["department_id"=>$department_id])->delete()) {
                ($member->where(["department_id"=>$department_id])->delete());
                parent::operating(request()->path(),0,'审核不通过成功');
                return array('s'=>'ok');
            }else {
                parent::operating(request()->path(),1,'审核不通过失败');
                return array('s'=>'数据不存在');
            }
        }else {
            parent::operating(request()->path(),1,'非法请求');
            return array('s'=>'非法请求');
        }
    }
    public function addAdmin() {
        parent::userauth(304);

        //判断是否是ajax请求
        if (request()->isAjax()) {
            $department_id = input('post.department_id');
            $department= new \app\common\model\Department;
            if ($department->save(array("is_admin"=>1),"department_id='{$department_id}'")) {
                parent::operating(request()->path(),0,'添加为管理员成功');
                return array('s'=>'ok');
            }else {
                parent::operating(request()->path(),1,'添加为管理员失败');
                return array('s'=>'数据不存在');
            }
        }else {
            parent::operating(request()->path(),1,'非法请求');
            return array('s'=>'非法请求');
        }
    }
    public function cancleAdmin() {
        parent::userauth(305);

        //判断是否是ajax请求
        if (request()->isAjax()) {
            $department_id = input('post.department_id');
            $department= new \app\common\model\Department;
            if ($department->save(array("is_admin"=>0),"department_id='{$department_id}'")) {
                parent::operating(request()->path(),0,'取消管理员成功');
                return array('s'=>'ok');
            }else {
                parent::operating(request()->path(),1,'取消管理员失败');
                return array('s'=>'数据不存在');
            }
        }else {
            parent::operating(request()->path(),1,'非法请求');
            return array('s'=>'非法请求');
        }
    }

    public function management()
    {
        $clues_tree_list = $this->getmanagementTreelist(1);
        $this->assign('clues_tree_list', json_encode($clues_tree_list));
        return $this->fetch();
    }
    public function getmanagementTreelist($parent_id)
    {
        $clues_tree_list = array();
        $region = new \app\common\model\Region;
        $where['parent_id'] = $parent_id;
        if($parent_id==1){
            $where['region_id'] = 29;
        }
        $clues_list = $region->where($where)->order("sort ASC")->column('');
        foreach ($clues_list as $k => $v) {
            $flag = false;
            if ($v['region_type'] == 1) {
                $flag = true;
            }
            $clues_children = array(
                'id' => $v['region_id'],
                'title' => $v['region_name'],
                'spread' => $flag,
                'children' => $this->getTreelist($v['region_id']),
            );
            if (!$parent_id) {
                $clues_tree_list[$parent_id]['children'][] = $clues_children;
            } else {
                $clues_tree_list[] = $clues_children;
            }
        }
        return $clues_tree_list;
    }

    public function getmanagamentList()
    {
        $department = new \app\common\model\Department;
        $region = new \app\common\model\Region;
        $member = new \app\common\model\Member;
        $where = $where_d = array();
        $keywords = input('request.keywords');
        $limit = input('request.limit');
        $region_id = input('request.region_id');
        if ($keywords != "") {
            $where_d['real_name|phone'] = array("LIKE", "%$keywords%");
        }
//       //获取地区信息
        if($region_id!=''){
            $where['region_id'] = $region_id;
            $region_info = $region->where($where)->find();
            //判断 $region_type 的值  1 province  2 city 3 district
            if ($region_info['region_type'] == "1") {
                $where_d['province'] = $region_id;
            } elseif ($region_info['region_type'] == "2") {
                $where_d['city'] = $region_id;
            } elseif ($region_info['region_type'] == "3") {
                $where_d['district'] = $region_id;
            }
        }

        $where1['industry_id'] = 1;
//        张昊注释
//        $list = $department->where($where_d)->where($where1)->pagniate($limit);
//        $department_list= $list->toArray();
//        $department_info =array();
//        $ii=0;
//        foreach ($department_list['data'] as $key => $value){
//            $departmentlist = $member->where(['department_id'=>$value['department_id'],'parent_id'=>[neq,0]])->select();
//            foreach ($departmentlist as $k => $v){
//                $province =$department->where("department_id='{$v['department_id']}'")->value("province");
//                $city =$department->where("department_id='{$v['department_id']}'")->value("city");
//                $district =$department->where("department_id='{$v['department_id']}'")->value("district");
//                $department_info[$ii]['province'] = $region->where("region_id='{$province}'")->value("region_name");
//                $department_info[$ii]['city'] = $region->where("region_id='{$city}'")->value("region_name");
//                $department_info[$ii]['district'] = $region->where("region_id='{$district}'")->value("region_name");
//                $department_info[$ii]['region_name'] =  $department_info[$k]['province'] .  $department_info[$k]['city'] . $department_info[$k]['district'];
//                $department_info[$ii]['department_name'] = $department->where("department_id='{$v['department_id']}'")->value("department_name");
//                $department_info[$ii]['phone'] = $v['phone'];
//                $department_info[$ii]['real_name'] = $v['real_name'];
//                $department_info[$ii]['register_time'] = $v['register_time'];
//                $ii++;
//            }
//        }
        $list = $department->where($where_d)->where($where1)->column('department_id');

        $memberlist = $member->where(['department_id'=>['in',$list],'parent_id'=>['neq',0]])->order('member_id asc')->paginate($limit)->toArray();
        foreach ($memberlist['data'] as $k => $v){
                $province =$department->where(["department_id"=>$v['department_id']])->value("province");
                $city =$department->where(["department_id"=>$v['department_id']])->value("city");
                $district =$department->where(["department_id"=>$v['department_id']])->value("district");
                $memberlist['data'][$k]['province'] = $region->where(["region_id"=>$province])->value("region_name");
                $memberlist['data'][$k]['city'] = $region->where(["region_id"=>$city])->value("region_name");
                $memberlist['data'][$k]['district'] = $region->where(["region_id"=>$district])->value("region_name");
                $memberlist['data'][$k]['region_name'] =  $memberlist['data'][$k]['province'] .  $memberlist['data'][$k]['city'] . $memberlist['data'][$k]['district'];
                $memberlist['data'][$k]['department_name'] = $department->where(["department_id"=>$v['department_id']])->value("department_name");
        }
        $result = array("code" => 0, "count" => $memberlist['total'], "data" => $memberlist['data']);
        echo json_encode($result);
        exit;
    }
    public function addchild()
    {
        $dmenu = new \app\common\model\Dmenu;
        $user = new \app\common\model\User;
        $department = new \app\common\model\Department;
        $region_id = input('get.region_id');
        $parent_id = input('request.parent_id');
        $department_info = $department->where(["department_id"=> $parent_id])->find();
        $department_info['parent_type_name'] = $dmenu->where(["ID"=>$department_info['department_type']])->value("MenuName");
        $this->assign('department_info', $department_info);
        $this->assign('region_id', $region_id);
        return $this->fetch();
    }

    //添加处理
    public function addchild_do()
    {
        //验证用户权限
//        parent::win_userauth(139);
        $request = Request::instance();
        $member = new \app\common\model\Member;
        $department = new \app\common\model\Department;
        $data = array();
        $region_id = input('region_id');
        $data['real_name'] = input('real_name');
        $data['phone'] = input('phone');
        $data['member_passwd'] = input('Password');
        $data['member_passwd'] =sha1(md5($data['member_passwd']));
        $reg = '/^1[3456789]{1}\d{9}$/';
        if ($data['phone'] != "" && !preg_match($reg, $data['phone'])) {
            return array('s' => '手机号码格式不正确，请重新输入!');
        }
        $data['register_time'] = date("Y-m-d H:i:s");
        if (session("Department.district") != 0) {
            $where['district'] = session("Department.district");
        } elseif (session("Department.city") != 0) {
            $where['city'] = session("Department.city");
        } elseif (session("Department.province") != 0) {
            $where['province'] = session("Department.province");
        }
        $where_d['district']=$region_id;
        $where_d['industry_id']=1;
        $member_id=$department->where($where_d)->value('member_id');
        $department_id=$department->where($where_d)->value('department_id');
        $department_name=$department->where($where_d)->value('department_name');
        $data['parent_id'] = $member_id;
        $data['department_id'] = $department_id;
        $data['member_name'] = $department_name;
        $member_info = $member->where(["phone" =>$data['phone']])->find();
        if (!empty($member_info)) {
            return array('s' => '该手机号码已存在，请核对！');
        }
        try {
            $member->save($data);
            return array("s" => 'ok');
        } catch (Exception $e) {
            return array('s' => $e->getMessage());
        }

    }

    public function del()
    {
//        parent::userauth(320);
        $member = new \app\common\model\Member;
        $member_id = input('post.member_id');
        if (($member = $member->where(['member_id' => $member_id]))->delete()) {
            parent::operating(request()->path(), 0, '删除成功');
            return array('s' => 'ok');
        } else {
            parent::operating(request()->path(), 1, '删除失败');
            return array('s' => '数据不存在');
        }
    }

    public function editchild()
    {
//        $member_id = input('member_id');
//        if ($member_id == '' || !is_numeric($member_id)) {
//            parent::operating(request()->path(), 1, '参数错误');
//            $this->error('参数ID类型错误');
//        }
//        $member = new \app\common\model\Member;
        if ($result = $member->where("member_id='{$member_id}'")->find()) {
            $this->assign('result', $result);
            return $this->fetch();
        } else {
            parent::operating(request()->path(), 1, '数据不存在');
            $this->error('没有找到相关数据');
        }
    }

    //修改处理
    public function editchild_do()
    {
        if (request()->isAjax()) {
            $data =array();
            $member = new \app\common\model\Member;
            $member_id = input('member_id');
            $data['real_name'] = input('real_name');
            $data['member_passwd'] = input('member_passwd');
            $data['member_passwd'] = sha1(md5($data['member_passwd']));
            $data['phone']=input('phone');
            $member->save($data, ["member_id"=>$member_id]);
                parent::operating(request()->path(), 0, '更新成功');
                return array('s' => 'ok');
        } else {
            parent::operating(request()->path(), 1, '非法请求');
            $this->error('非法请求');
        }
    }





}

?>