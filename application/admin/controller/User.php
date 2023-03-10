<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Url;

class User extends Admin
{
    public function index() {
        $id = input('request.ID');
        return $this->fetch();
    }
    public function getUser_list() {
        parent::userauth2(2);//权限判断
        $id = input('request.ID');
        $keywords = input('request.keywords');
        $limit = input('request.limit');
        $user = new \app\common\model\User;
        $role = new \app\common\model\Role;
        $where = array();
        if($keywords!=""){
            $where['Username'] = array("LIKE","%$keywords%");
        }
        $lists  = $user->where($where)->paginate($limit);
        $volist = $lists->toArray();
        foreach($volist['data'] as $k=>$v){
            $volist['data'][$k]['Rolename']   = $role->where("ID='{$v['Roleid']}'")->value('Rolename');
        }
        $result = array("code"=>0,"count"=>$volist['total'],"data"=>$volist['data']);
        echo json_encode($result);exit;
    }
	public function useradd() {
		parent::win_userauth(3);
		$role=new \app\common\model\Role;
		$volist=$role->where('Status=0')->column('ID,Rolename,Status');
		$user=new \app\common\model\User;
		$user_list=$user->where('Status=0')->column('ID,Username,Status');
		$this->assign('volist',$volist);
		$this->assign('user_list',$user_list);
		return $this->fetch('add');
	}
	//添加用户
	public function useradd_do() {
		//验证用户权限
		parent::userauth(3);
		$data=array();
		if (request()->isAjax()) {
		    //自动完成验证与新增
		    $user=new \app\common\model\User;

			$data['Roleid']      = input('request.roleid');
			$data['Username']    = input('request.username');
			$data['Password']    = input('request.password');
			$data['Email']       = input('request.email');
			$data['Description'] = input('request.description');
			$data['name'] = input('request.name');
			$data['phone'] = input('request.phone');
			$data['Status']      = input('request.status');
			$data['leader_id']   = input('request.leader_id');
			if ($user->save($data)) {
				$uid=$user->ID;
				$role=new \app\common\model\Role;
				$r=$role->where('ID='.$data['Roleid'])->value('Competence');
				//给新用户添加默认权限
				$user->where("ID=$uid")->update(array('Competence' => $r));
				parent::operating(request()->path(),0,'新增用户：'.$data['Username']);
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'新增失败：'.$data['Username']);
				return array('s'=>$user->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	//修改界面
	public function useredit() {
		parent::win_userauth(4);
		$id = input('request.ID');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(request()->path(),1,'参数错误');
			$this->assign('content','参数ID类型错误，请关闭本窗口');
			return $this->fetch('public/err');
		}
		$id=intval($id);
		$role =   new \app\common\model\Role;
		$user =   new \app\common\model\User;
		//获取分类信息
		$volist=$role->where('Status=0')->order('Dtime')->column('ID,Rolename,Status');
		$data=array('ID' => $id);
		if ($result=$user->where($data)->find()) {
			//获取权限数据
			$co= new \app\common\model\Competence;
			$aulist=$co->where('Sid=0 AND Status=0')->order('Dtime asc')->column('ID,Cname,Status');
			$slist=$co->where('Sid<>0 AND Status=0')->order('Dtime asc')->column('ID,Sid,Cname,Status');
			$user_list=$user->where("Status=0 AND ID<>{$result['ID']}")->column('ID,Username,Status');
            $Competence_count=count(explode(',',$result['Competence']));
            $count_slist = $co->where('Sid<>0 AND Status=0')->count();
            if($Competence_count === $count_slist){
                $switch=1;
            }else{
                $switch=0;
            }
            $this->assign('switch', $switch);
			$this->assign('result',$result);
			$this->assign('volist',$volist);
			$this->assign('user_list',$user_list);
			$this->assign('aulist',$aulist);
			$this->assign('slist',$slist);
			return $this->fetch('edit');
		}else {
			parent::operating(request()->path(),1,'数据不存在');
			$this->assign('content','没有找到相关数据，请关闭本窗口');
			return $this->fetch('public/err');
		}
	}
	//用户修改处理
	public function useredit_do() {
		//验证用户权限
		parent::userauth(4);
		$data=array();
		if (request()->isAjax()) {
			$ID                  = input('post.ID');
			$data['Roleid']      = input('post.roleid');
			$data['Username']    = input('post.username');
			if(input('post.password')!=""){
			    $data['Password']    = input('post.password');
                if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,32}$/i',$data['Password'])) {
                    return array('s'=>'nopass');
                }
			}
			$data['name'] 		 = input('request.name');
			$data['Email']       = input('post.email');
			$data['Description'] = input('post.description');
            $data['phone']      = input('post.phone');
			$data['Competence']  = implode(",",input('post.comp/a'));
			$data['Status']      = input('post.status');
			$data['leader_id']   = input('post.leader_id');
			$user = new \app\common\model\User;
			if ($user->save($data,"ID = {$ID}")) {
				parent::operating(request()->path(),0,'更改用户资料：'.$data['Username']);
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'更新失败：'.$data['Username']);
				return array('s'=>$user->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求：');
			return array('s'=>'非法请求');
		}
	}
	//个别信息修改
	public function uedit() {
		//parent::win_userauth(18);
		$id = session('ThinkUser.ID');
		$user= new \app\common\model\User;
		$data=array('ID' => $id);
		if ($result=$user->where($data)->find()) {
			$this->assign('result',$result);
			//获取权限数据
			return $this->fetch('uedit');
		}else {
			parent::operating(request()->path(),1,'没有找到相关数据：'.$id);
			$this->assign('content','没有找到相关数据，请关闭本窗口');
			$this->fetch('public/err');
		}
	}
	//个别信息修改处理
	public function uedit_do() {
		//验证用户权限
		//parent::userauth(18);
		$data=array();
		if (request()->isAjax()) {
			$ID              = session('ThinkUser.ID');
			$password        = input('post.password');
			$data['Email']   = input('post.email');
//			if (strlen($password)<6 || strlen($password)>18) {
//				return array('s'=>'请输入6～18位数的安全密码');
//			}
			if (!preg_match('/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/i',$data['Email'])) {
				return array('s'=>'请输入正确的邮箱地址');
			}
			if($password!=''){
                $data['Password']=$password;
            }
			$user= new \app\common\model\User;;
			if ($user->save($data,"ID = $ID")) {
				parent::operating(request()->path(),0,'修改密码成功');
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'更新失败：'.$user->getError());
				return array('s'=>$user->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	//删除数据
	public function userdel() {
		//验证用户权限
		parent::userauth(5);
		//判断是否是ajax请求

				$id=input('post.ID');

				if ($id=='' || !is_numeric($id)) {
					parent::operating(request()->path(),1,'参数错误');
					return array('s'=>'参数ID类型错误');
				}else {
					$id=intval($id);
					if ($id==1) {
						parent::operating(request()->path(),1,'不能删除系统默认用户');
						return array('s'=>'不能删除系统默认用户');
					}
					$user= new \app\common\model\User;
					$where=array('ID'=>$id);
					if ($user->where($where)->value('ID')) {
						$user->where($where)->delete();
						parent::operating(request()->path(),0,'删除用户ID为：'.$id.'的数据');
						return array('s'=>'ok');
					}else {
						parent::operating(request()->path(),1,'删除用户失败：'.$user->getError());
						return array('s'=>$user->getError());
					}
				}
	}
	//批量删除
	public function in_user_del() {
		//验证用户权限
		parent::userauth(5);
		if (request()->isAjax()) {
			if (!$delid=explode(',',input('post.delid'))) {
				return array('s'=>'请选中后再删除');
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			if (in_array(1,$delid)) {
				return array('s'=>'不能删除ID号为1的数据');
			}
			$id=join(',',$delid);
			$user= new \app\common\model\User;
			if ($user->where('ID','IN',$id)->delete()) {
				parent::operating(request()->path(),0,'批量删除ID为：'.$id.'的数据');
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'批量删除用户失败：'.$user->getError());
				return array('s'=>$user->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	public function batch_user_del() {
        if (!$delid=explode(',',input('param.delid'))) {
            return array('s'=>'请选中后再删除');
        }
        //将最后一个元素弹出栈
        array_pop($delid);
        $id=join(',',$delid);
        $user= new \app\common\model\User;
        if ($user->where('ID','IN',$id)->delete()) {
            return array('s'=>'ok');
        }else {
            return array('s'=>$user->getError());
        }
	}
}
