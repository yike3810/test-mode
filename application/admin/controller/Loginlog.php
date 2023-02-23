<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Url;

class Loginlog extends Admin
{
	public function index() {
		parent::userauth2(19);
		$keyword  = input('request.keyword');
		$loginlog = new \app\common\model\Loginlog;
		$user     = new \app\common\model\User;
		$where    = array();
		//$where['Username']=$keyword;
		$lists  = $loginlog->where($where)->order('ID desc')->paginate();
		$volist = $lists->toArray();
		foreach($volist['data'] as $k=>$v){
		    $volist['data'][$k]['Username'] = $user->where("ID='{$v['Uid']}'")->value('Username');
		}
		$this->assign('volist',$volist);			
		$this->assign('keyword',$keyword);
		$this->assign('lists',$lists);
//		return $this->fetch("public/loginlog");
        return $this->fetch();
	}
    public function getLoginlog_List() {
        parent::userauth2(19);

        $keywords = input('request.keywords');
        $limit = input('request.limit');
        $publish_time1 = input('request.publish_time1');
        $publish_time2 = input('request.publish_time2');

        $loginlog = new \app\common\model\Loginlog;
        $user     = new \app\common\model\User;
        $where    = array();
        if($keywords!=""){
            $where['User|Description|Loginip'] = array("LIKE","%$keywords%");
        }
        if($publish_time1!=""){
            $where['Dtime'] = array(">=",$publish_time1);
        }
        if($publish_time2!=""){
            $where_d1['Dtime'] = array("<=",$publish_time2);
        }
        $lists  = $loginlog->where($where)->where($where_d1)->order('ID desc')->paginate($limit);
        $volist = $lists->toArray();
        foreach($volist['data'] as $k=>$v){
                $volist['data'][$k]['Username'] = $user->where("ID='{$v['Uid']}'")->value('Username');
            }
        $result = array("code"=>0,"count"=>$volist['total'],"data"=>$volist['data']);
        echo json_encode($result);exit;

    }
	//删除数据
	public function del() {
		//验证用户权限
		parent::userauth(20);
		//判断是否是ajax请求
		if (request()->isAjax()) {
				$id = input('post.ID');
				if ($id=='' || !is_numeric($id)) {
					return array('s'=>'参数ID类型错误');
				}else {
					$id=intval($id);
					$log= new \app\common\model\Loginlog;
					$where=array('ID'=>$id);
					if ($log->where($where)->value('ID')) {
						$log->where($where)->delete();
						parent::operating(request()->path(),0,'登录日志删除成功');
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
	//批量删除
	public function indel() {
		//验证用户权限
		parent::userauth(20);
		if (request()->isAjax()) {
			if (!$delid=explode(',',input('post.delid'))) {
				return array('s'=>'请选中后再删除');
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$log= new \app\common\model\Loginlog;
			if ($log->where('ID','IN',$id)->delete()) {
				parent::operating(request()->path(),0,'登录日志删除成功');
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'登录日志删除失败');
				return array('s'=>'删除失败');
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
}
