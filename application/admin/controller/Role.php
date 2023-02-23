<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Url;

class Role extends Admin
{
    public function index()
    {
        $id = input('request.ID');
        return $this->fetch();
    }

    public function getRole_list()
    {
        parent::userauth2(7);
        $id = input('request.ID');
        $keyword = input('request.keywords');
        $user = new \app\common\model\Role;
        $where = array();
        if ($keyword != "") {
            $where['Rolename'] = array("LIKE", "%$keyword%");
        }
        $lists = $user->where($where)->paginate();
        $volist = $lists->toArray();
        $result = array("code" => 0, "count" => $volist['total'], "data" => $volist['data']);
        echo json_encode($result);
        exit;
    }

    public function roleadd()
    {
        parent::win_userauth(8);
        $co = new \app\common\model\Competence;
        $volist = $co->where('Sid=0 AND Status=0')->order('Dtime asc')->column('ID,Cname,Status');
        $slist = $co->where('Sid<>0 AND Status=0')->order('Dtime asc')->column('ID,Sid,Cname,Status');
        $this->assign('slist', $slist);
        $this->assign('volist', $volist);
        return $this->fetch('add');
    }

    //新增角色
    public function roleadd_do()
    {
        //验证用户权限
        parent::userauth(8);
        $data = array();
        if (request()->isAjax()) {
            $data['Rolename'] = input('post.rolename');
            $data['Description'] = input('post.description');
            $data['Competence'] = implode(",", input('post.comp/a'));
            $data['Status'] = input('post.status');
            $data['Dtime'] = date("Y-m-d H:i:s");
            $role = new \app\common\model\Role;
            if ($role->data($data)) {
                $role->save();
                parent::operating(request()->path(), 0, '新增成功');
                return array('s' => 'ok');
            } else {
                parent::operating(request()->path(), 1, '新增失败' . $role->getError());
                return array('s' => $role->getError());
            }
        } else {
            parent::operating(request()->path(), 1, '非法请求');
            return array('s' => '非法请求');
        }
    }

    //修改角色信息
    public function roleedit()
    {
        parent::win_userauth(9);
        $id = input('get.ID');
        if ($id == '' || !is_numeric($id)) {
            parent::operating(request()->path(), 1, '参数错误');
            $this->assign('content', '参数ID类型错误，请关闭本窗口');
            return $this->fetch('public/err');
        }
        $id = intval($id);
        $role = new \app\common\model\Role;
        if ($result = $role->where("ID = $id")->find()) {
            $this->assign('result', $result);
            //获取权限数据
            $Competence_count=count(explode(',',$result['Competence']));
            $co = new \app\common\model\Competence;
            $volist = $co->where('Sid=0 AND Status=0')->order('Dtime asc')->column('ID,Cname,Status');
            $slist = $co->where('Sid<>0 AND Status=0')->order('Dtime asc')->column('ID,Sid,Cname,Status');
            $count_slist = $co->where('Sid<>0 AND Status=0')->count();
            if($Competence_count === $count_slist){
                $switch=1;
            }else{
                $switch=0;
            }
            $this->assign('switch', $switch);
            $this->assign('volist', $volist);
            $this->assign('slist', $slist);
            return $this->fetch('edit');
        } else {
            parent::operating(request()->path(), 1, '数据不存在');
            $this->assign('content', '没有找到相关数据，请关闭本窗口');
            return $this->fetch('public/err');
        }
    }

    //修改处理
    public function roleedit_do()
    {
        //验证用户权限
        parent::userauth(9);
        $data = array();

        $ID = input('post.ID');
        $data['Rolename'] = input('post.rolename');
        $data['Description'] = input('post.description');
        $data['Competence'] = implode(",", input('post.comp/a'));
        $data['Status'] = input('post.status');
        $role = new \app\common\model\Role;
        $where = array('ID' => $ID);
        if ($role->save($data, $where)) {
            //修改所有属于该角色的用户权限
            $user = new \app\common\model\User;
            $user->where('Roleid=' . $ID)->setField(array('Competence' => $data['Competence']));
            parent::operating(request()->path(), 0, '更新成功');
            return array('s' => 'ok');
        } else {
            parent::operating(request()->path(), 1, '更新失败：' . $role->getError());
            return array('s' => $role->getError());
        }

    }

    //删除数据
    public function roledel()
    {
        //验证用户权限
        parent::userauth(10);

        $id = input('post.ID');
        if ($id == '' || !is_numeric($id)) {
            parent::operating(request()->path(), 1, '参数错误');
            return array('s' => '参数ID类型错误');
        } else {
            $id = intval($id);
            if ($id == 1) {
                parent::operating(request()->path(), 1, '不能删除系统默认角色');
                return array('s' => '不能删除此角色');
            }
            $role = new \app\common\model\Role;
            $where = array('ID' => $id);
            if ($role->where($where)->value('ID')) {
                $role->where($where)->delete();
                parent::operating(request()->path(), 0, '删除成功');
                return array('s' => 'ok');
            } else {
                parent::operating(request()->path(), 1, '数据不存在：' . $id);
                return array('s' => '数据不存在');
            }
        }

    }
}
