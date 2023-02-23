<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Url;

class Module extends Admin
{

	//模块管理开始
	public function module() {
        return $this->fetch();
	}
    public function getModule_list() {
        parent::userauth2(25);
        $module   = new \app\common\model\Module;
        $user     = new \app\common\model\User;
        $where    = array();
        $keywords = input('request.keywords');
        if($keywords!=""){
            $where['ModuleName'] = array("LIKE","%$keywords%");
        }
        $volist   = $module->where($where)->select();
        $volist = $this->classid($volist);
//        a($volist);
        foreach($volist as $k=>$v){
            $volist[$k]['Username'] = $user->where("ID=".$v['Uid'])->value('Username');
            $volist[$k]['MName']=$v['html'].$v['ModuleName'];
        }
        $result = array("code"=>0,"count"=>$volist,"data"=>$volist);
        echo json_encode($result);exit;
    }
	//添加模块
	public function module_add() {
		parent::win_userauth(26);
		$module = new \app\common\model\Module;
		$list=$module->order('Msort asc')->column('ID,Sid,ModuleName,Msort');
		$competence = new \app\common\model\Competence;
		$competence_list=$competence->where("Sid = 0")->order('ID DESC')->select();
		$arr=$this->classid($list);
		$this->assign('list',$arr);
		$this->assign('competence_list',$competence_list);
		return $this->fetch('moduleadd');
	}
	//无限循环分类
	protected function classid($volist,$nan=0,$html='──',$level=0) {
		$arr=array();
		foreach($volist as $val) {
			if ($val['Sid'] == $nan) {
				$val['classname'] = $val['ModuleName'];
				$val['html'] = str_repeat($html,$level);
				$arr[] = $val;
				$arr = array_merge($arr,self::classid($volist,$val['ID'],$html,$level+1));
			}
		}
		return $arr;
	}
	//添加模块处理
	public function module_add_do() {
		parent::userauth(26);
		if (request()->isAjax()) {
			$data['Sid']         = input('post.sid');
			$data['ModuleName']  = input('post.mname');
			$data['ModuleImg']   = input('post.img');
			$data['ModuleUrl']   = input('post.url');
			$data['Status']      = input('post.status');
			$data['Msort']       = input('post.msort');
			$data['Description'] = input('post.description');
			$data['Competence']  = input('post.Competence');
			$module = new \app\common\model\Module;
			if ($module->data($data)) {
				$module->save();
				parent::operating(request()->path(),0,'新增模块成功');
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'新增失败：'.$module->getError());
				return array('s'=>$module->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	//修改模块
	public function module_edit() {
		parent::win_userauth(27);
		$ID = input('get.ID');
//		if ($id=='' || !is_numeric($id)) {
//			parent::operating(request()->path(),1,'参数错误');
//			$this->assign('content','参数ID类型错误，请关闭本窗口');
//			return $this->fetch('public/err');
//		}
        $ID=intval($ID);
		$module=new \app\common\model\Module;
		//获取分类信息
		$data=array('ID' => $ID);
		if ($result=$module->where($data)->find()) {
			$this->result=$result;
			$list=$module->order('Msort asc')->column('ID,Sid,ModuleName,Msort');
			$arr=$this->classid($list);
			$competence = new \app\common\model\Competence;
			$competence_list=$competence->where("Sid = 0")->order('ID DESC')->select();
			$this->assign('list',$arr);
			$this->assign('competence_list',$competence_list);
			$this->assign('result',$result);
			return $this->fetch('moduleedit');
		}else {
			parent::operating(request()->path(),1,'数据不存在');
			$this->assign('content','没有找到相关数据，请关闭本窗口');
			return $this->fetch('public/err');
		}
	}

	//修改模块处理
	public function module_edit_do() {
		parent::userauth(27);
		if (request()->isAjax()) {
			$ID                  = input('post.ID');
			$data['Sid']         = input('post.sid');
			$data['ModuleName']  = input('post.mname');
			$data['ModuleImg']   = input('post.img');
			$data['ModuleUrl']   = input('post.url');
			$data['Status']      = input('post.status');
			$data['Msort']       = input('post.msort');
			$data['Description'] = input('post.description');
			$data['Competence']  = input('post.Competence');
			$module = new \app\common\model\Module;
			if ($module->save($data,"ID=".$ID)) {
				parent::operating(request()->path(),0,'更新成功');
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'更新失败：'.$module->getError());
				return array('s'=>$module->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	//模块删除
	public function moduledel() {
		//验证用户权限
		parent::userauth(28);
		//判断是否是ajax请求
		if (request()->isAjax()) {
			$id = input('post.ID');
			if ($id=='' || !is_numeric($id)) {
				parent::operating(request()->path(),1,'参数错误');
				return array('s'=>'参数ID类型错误');
			}else {
				$id=intval($id);
				$module= new \app\common\model\Module;
				$where=array('ID'=>$id);
				if ($module->where($where)->value('ID')) {
					$module->where($where)->delete();
					parent::operating(request()->path(),0,'删除模块成功');
					return array('s'=>'ok');
				}else {
					parent::operating(request()->path(),1,'数据不存在');
					return array('s'=>'数据不存在');
				}
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
}
