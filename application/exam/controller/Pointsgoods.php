<?php
//商品管理类
namespace app\exam\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Url;

class Pointsgoods extends Admin
{
	public function unverify()
    {
        parent::userauth2(187);
        $keywords = input('request.keywords');
        if ($keywords != "") {
            $where['goods_name'] = array("LIKE", "%$keywords%");
        }

        $points_goods = new \app\common\model\Pointsgoods;
        $store = new \app\common\model\Store;
        $where['goods_state'] = '1';

        $lists = $points_goods->where($where)->paginate();
        $goods_list = $lists->toArray();
        // a($goods_list);

        $this->assign('lists', $lists);
        $this->assign('goods_list', $goods_list);
        return $this->fetch();
    }
	public function index()
    {
        parent::userauth2(191);
        $keywords = input('request.keywords');
        if ($keywords != "") {
            $where['goods_name'] = array("LIKE", "%$keywords%");
        }

        $points_goods = new \app\common\model\Pointsgoods;
        $store = new \app\common\model\Store;
        $where['store_id'] = Session::get('Store.store_id');
        $where['goods_state'] = '2';
        $where['goods_show'] = '1';

        $lists = $points_goods->where($where)->paginate();
        $goods_list = $lists->toArray();
        // a($goods_list);

        $this->assign('lists', $lists);
        $this->assign('goods_list', $goods_list);
        return $this->fetch();
    }
    //修改商品信息
    public function edit()
    {
    	parent::userauth2(228);
        $points_goods = new \app\common\model\Pointsgoods;
        $store = new \app\common\model\Store;

        $where['goods_id'] = input('param.goods_id');


        $lists = $points_goods->where($where)->find();

//        $this->assign('lists', $lists);
        $this->assign('goods_list', $lists);
        return $this->fetch();
    }
    //修改商品信息处理
    public function edit_do()
    {
        //验证用户权限
        parent::win_userauth(228);
        if (request()->isPost()) {
            $data = $member_data = array();
            $data['goods_name'] = input('request.goods_name');
            $data['goods_price'] = input('request.goods_price');
            $data['goods_points'] = input('request.goods_points');
            $data['goods_id'] = input('request.goods_id');
            $data['goods_islimit'] = input('post.goods_islimit');
            $data['goods_limitnum'] = input('post.goods_limitnum');
            $data['goods_sort'] = input('post.goods_sort');
            $data['barcode'] = input('post.barcode');

            $points_goods = new \app\common\model\Pointsgoods;
            $store = new \app\common\model\Store;

            $goods_info = $points_goods->where("goods_name = '{$data['goods_name']}' ")->find();
            if (!(empty($goods_info) || $goods_info['goods_id'] == $data['goods_id'])) {
                $this->error("商品异常，修改失败!");
            }

            //修改图标
            if (!empty($_FILES['points_image']['tmp_name'])) {
                $file_info = pathinfo($_FILES['points_image']['name']);//函数以数组的形式返回文件路径的信息。
                //验证格式
                if (!in_array(strtolower($file_info['extension']), array("jpg", "jpeg", "png", "gif"))) {
                    $this->error("上传的文件格式不正确，请上传后缀为.jpg、.jpeg、.gif文件或者.png格式的文件！");
                }
                //限制大小
                if ($_FILES['points_image']['size'] > 1024 * 1024 * 5) {
                    $this->error("上传的图片不能超过5M，请重新上传！");
                }
                //上传文件
                $file = $this->upload();
                //判断是否上传
                if ($file) {
                    //成功返回文件名
                    $data['goods_image'] = $file->getSaveName();
                } else {
                    $this->error($file->getError());
                }
            }
            if ($points_goods->save($data, "id='{$data['id']}'")) {
                parent::operating(request()->path(), 0, '更新商品信息：' . $data['goods_name']);
                $this->success('商品信息更新成功', url('Pointsgoods/edit', 'goods_id=' .  $data['goods_id']));
            } else {
                parent::operating(request()->path(), 1, '更新失败：' . $data['goods_name']);
                $this->error($points_goods->getError());
            }
        } else {
            parent::operating(request()->path(), 1, '非法请求');
            $this->error('非法请求');
        }
    }

//删除商品到回收站
    public function pointsgoods_del()
    {
        parent::userauth(189);
        //判断是否是ajax请求
        if (request()->isAjax()) {
            $id = input('param.goods_id');

            if ($id == '' || !is_numeric($id)) {
                parent::operating(request()->path(), 1, '参数错误');
                return array('s' => '参数ID类型错误');
            } else {
                $id = intval($id);
                $points_goods = new \app\common\model\Pointsgoods;
                $where = array('goods_id' => $id);
                if ($points_goods->where($where)->value('goods_id')) {
                    $points_goods->where($where)->delete();
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

    //批量删除
    public function pointsgoods_indel() {
        //验证用户权限
        parent::userauth(189);
        if (request()->isAjax()) {
            if (!$delid=explode(',',input('post.delid'))) {
                return array('s'=>'请选中后再删除');
            }
            //将最后一个元素弹出栈
            array_pop($delid);
            $id=join(',',$delid);
            $points_goods = new \app\common\model\Pointsgoods;
            $map['goods_id'] = array('in',$id);
            if ($points_goods->where($map)->delete()) {
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
    //删除商品到回收站
    public function verify()
    {
    	parent::userauth(192);
    	//判断是否是ajax请求
    	if (request()->isAjax()) {
    		$id = input('param.goods_id');
    		$goods_state = input('param.goods_state');
    		if ($id == '' || !is_numeric($id)) {
    			parent::operating(request()->path(), 1, '参数错误');
    			return array('s' => '参数ID类型错误');
    		} else {
    			$id = intval($id);
    			$points_goods = new \app\common\model\Pointsgoods;
    			$where = array('goods_id' => $id);
    			$data  = array("goods_state"=>$goods_state);
    			if($goods_state==2){
    				$data['goods_show'] = 1;
    			}else{
    				$data['goods_show'] = 0;
    			}
    			if ($points_goods->save($data,$where)) {
    				parent::operating(request()->path(), 0, '操作成功：' . $id);
    				return array('s' => 'ok');
    			} else {
    				parent::operating(request()->path(), 1, '操作失败：' . $this->getError());
    				return array('s' => $this->getError());
    			}
    		}
    	} else {
    		parent::operating(request()->path(), 1, '非法请求');
    		$this->error('非法请求');
    	}
    }
    //批量审核
    public function batch_verify() {
    	//验证用户权限
    	//parent::userauth(130);
    	$goods_state = input('param.goods_state');
    	if (request()->isAjax()) {
    		if (!$delid=explode(',',input('post.delid'))) {
    			return array('s'=>'请选中后再操作');
    		}
    		//将最后一个元素弹出栈
    		array_pop($delid);
    		$id=join(',',$delid);
    		$points_goods = new \app\common\model\Pointsgoods;
    		$map['goods_id'] = array('in',$id);
    		$data  = array("goods_state"=>$goods_state);
    		if($goods_state==2){
    			$data['goods_show'] = 1;
    		}else{
    			$data['goods_show'] = 0;
    		}
    		if ($points_goods->save($data,$map)) {
    			parent::operating(request()->path(),0,'操作成功：'.$id);
    			return array('s'=>'ok');
    		}else {
    			parent::operating(request()->path(),1,'操作失败：'.$id);
    			return array('s'=>'操作失败');
    		}
    	}else {
    		parent::operating(request()->path(),1,'非法请求');
    		$this->error('非法请求');
    	}
    }
    public function off_shelf_do()
    {
    	//判断是否是ajax请求
    	if (request()->isAjax()) {
    		$id = input('param.goods_id');
    		if ($id == '' || !is_numeric($id)) {
    			parent::operating(request()->path(), 1, '参数错误');
    			return array('s' => '参数ID类型错误');
    		} else {
    			$id = intval($id);
    			$points_goods = new \app\common\model\Pointsgoods;
    			$where = array('goods_id' => $id);
    			$data = array();
    			$data['goods_show'] = 0;
    			if ($points_goods->save($data,$where)) {
    				parent::operating(request()->path(), 0, '操作成功：' . $id);
    				return array('s' => 'ok');
    			} else {
    				parent::operating(request()->path(), 1, '操作失败：' . $this->getError());
    				return array('s' => $this->getError());
    			}
    		}
    	} else {
    		parent::operating(request()->path(), 1, '非法请求');
    		$this->error('非法请求');
    	}
    }
    public function batch_off_shelf_do() {
    	//验证用户权限
    	//parent::userauth(130);
    	$goods_state = input('param.goods_state');
    	if (request()->isAjax()) {
    		if (!$delid=explode(',',input('post.delid'))) {
    			return array('s'=>'请选中后再操作');
    		}
    		//将最后一个元素弹出栈
    		array_pop($delid);
    		$id=join(',',$delid);
    		$points_goods = new \app\common\model\Pointsgoods;
    		$map['goods_id'] = array('in',$id);
    		$data  = array();
    		$data['goods_show'] = 0;
    		if ($points_goods->save($data,$map)) {
    			parent::operating(request()->path(),0,'操作成功：'.$id);
    			return array('s'=>'ok');
    		}else {
    			parent::operating(request()->path(),1,'操作失败：'.$id);
    			return array('s'=>'操作失败');
    		}
    	}else {
    		parent::operating(request()->path(),1,'非法请求');
    		$this->error('非法请求');
    	}
    }
}
