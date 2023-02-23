<?php
namespace app\exam\controller;
use think\Controller;
use think\Request;
use think\Url;

class Set extends Admin
{
	public function index() {
		$parameter	=	new \app\common\model\Parameter;
		$parameter_list = $parameter->column('param_value','param_name');
		$this->assign('parameter_list',$parameter_list);
		return $this->fetch();
	}
	public function base_save() {
		$data=array();
		if (request()->isAjax()) {
			//自动完成验证与新增
			$parameter=new \app\common\model\Parameter;
			$data1['param_name']  = 'index_put_gray';
			$data1['param_value'] = input('index_put_gray')?input('index_put_gray'):0;
			try{
				$parameter->saveAll(array($data1));
				parent::operating(request()->path(),0,'保存基本设置参数成功');
				return array('s'=>'ok');
			}catch(\Exception $e){
				parent::operating(request()->path(),1,'保存基本设置参数失败');
				return array('s'=>$e->getMessage());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	public function point_save() {
		$data=array();
		if (request()->isAjax()) {
			//自动完成验证与新增
			$parameter=new \app\common\model\Parameter;
			$data1['param_name']  = 'activity_evaluation_score_coefficient';
			$data1['param_value'] = input('activity_evaluation_score_coefficient');
			$data2['param_name']  = 'activity_time_long_coefficient';
			$data2['param_value'] = input('activity_time_long_coefficient');
			try{
				$parameter->saveAll(array($data1,$data2));
				parent::operating(request()->path(),0,'保存积分设置参数成功');
				return array('s'=>'ok');
			}catch(\Exception $e){
				parent::operating(request()->path(),1,'保存积分设置参数失败');
				return array('s'=>$e->getMessage());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	public function screen_save() {
		$data=array();
		if (request()->isAjax()) {
			//自动完成验证与新增
			$parameter=new \app\common\model\Parameter;
			$data1['param_name']  = 'center_longitude';
			$data1['param_value'] = input('center_longitude');
			$data2['param_name']  = 'center_latitude';
			$data2['param_value'] = input('center_latitude');
			$data3['param_name']  = 'system_name';
			$data3['param_value'] = input('system_name');
			$data3['param_name']  = 'map_zoom';
			$data3['param_value'] = input('map_zoom');
			try{
				$parameter->saveAll(array($data1,$data2,$data3));
				parent::operating(request()->path(),0,'保存大屏可视化设置参数成功');
				return array('s'=>'ok');
			}catch(\Exception $e){
				parent::operating(request()->path(),1,'保存大屏可视化设置参数失败');
				return array('s'=>$e->getMessage());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
}
