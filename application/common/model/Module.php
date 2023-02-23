<?php
namespace app\common\model;
class Module extends \think\Model {
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
	//管理员ID
	protected function uid() {
		return session('ThinkUser.ID');
	}
	public $module_array = array(
		'member' => array(64),//志愿者
		'service' => array(65),//服务队
		'item' => array(68),//点单管理
		'system' => array(1,2,3),//系统配置
		'order' => array(62,144),//订单管理
		'data' => array(75),//数据统计
		'screen' => array(80),//可视化
		'store' => array(88),//积分兑换管理
		'exam' => array(110),//活动管理
		'goods' => array(112,137,141),//新闻管理
		'source' => array(117),//媒资管理
	);
	public function getModuleCompetence($module){
		$admin = new \app\admin\controller\Admin;
		if($module == "goods"){
			$flag = $admin->userauth3(350)||$admin->userauth3(355)||$admin->userauth3(363)||$admin->userauth3(288)||$admin->userauth3(294);
		}else if($module == "system"){
			$flag = $admin->userauth3(2)||$admin->userauth3(7)||$admin->userauth3(12)||$admin->userauth3(82)||$admin->userauth3(19)
			||$admin->userauth3(180)||$admin->userauth3(60)||$admin->userauth3(25)||$admin->userauth3(21);
		}else if($module == "data"){
			$flag = $admin->userauth3(324)||$admin->userauth3(325);
		}else if($module == "order"){
			$flag = $admin->userauth3(365)||$admin->userauth3(366)||$admin->userauth3(367)||$admin->userauth3(368)||$admin->userauth3(369);
		}else{
			$flag = true;
		}
		return $flag;
	}
	public function getModuleList(){
		return array(
			array('mod'=>'goods','icon'=>'goods.png','url'=>true,"mod_text"=>"商品管理"),
			array('mod'=>'order','icon'=>'order.png','url'=>true,"mod_text"=>"订单管理"),
//			array('mod'=>'data','icon'=>'index_06.png','url'=>true,"mod_text"=>"线索统计"),
			array('mod'=>'system','icon'=>'index_08.png','url'=>true,"mod_text"=>"系统配置"),
		);
	}
	public function getModuleListByCompetence(){
		$data = $this->getModuleList();
		foreach ($data as $k=>$v){
			if(!$this->getModuleCompetence($v['mod'])){
				unset($data[$k]);
			}
		}
		return array_values($data);
	}
}
?>