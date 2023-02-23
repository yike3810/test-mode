<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Url;

class System extends Admin
{
	//IP操作开始
	public function ip() {
        $id = input('request.ID');
		return $this->fetch();
	}
    public function getip_list() {
        parent::userauth2(21);
        $id = input('request.ID');
        $keywords  = input('request.keywords');
        $ip       = new \app\common\model\Ip;
        $user     = new \app\common\model\User;
        $where    = array();
        if($keywords!=""){
            $where_d['IP'] = array("LIKE","%$keywords%");
        }
        $lists  = $ip->where($where)->where($where_d)->paginate();
        $volist = $lists->toArray();
        foreach($volist['data'] as $k=>$v){
            $volist['data'][$k]['Username'] = $user->where("ID=".$v['Uid'])->value('Username');
        }
        $result = array("code"=>0,"count"=>$volist['total'],"data"=>$volist['data']);
        echo json_encode($result);exit;
    }
	public function ipadd() {
		parent::win_userauth(22);
		return $this->fetch();
	}
	public function ipadd_do() {
		//验证用户权限
		parent::userauth(22);
		//判断请求类型
        $data=array();
		if (request()->isAjax()) {
			$data['Uid']         = session('ThinkUser.ID');
			$data['Ip']          = input('post.IP');
			$data['StartTime']   = input('post.StartTime');
			$data['EndTime']     = input('post.EndTime');
			$data['Status']      = input('post.status');
			$data['Description'] = input('post.description');
			$data['Dtime']       = date('Y-m-d H:i:s');
			$ip = new \app\common\model\Ip;
			if ($ip->data($data)) {
				$ip->save();
				parent::operating(request()->path(),0,'新增IP限制登录成功');
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'新增失败：',$ip->getError());
				return array('s'=>$ip->getError());
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	//删除数据
	public function del() {
		//验证用户权限
		parent::userauth(23);
		//判断是否是ajax请求
		if (request()->isAjax()) {
			$id = input('post.ID');
			if ($id=='' || !is_numeric($id)) {
				parent::operating(request()->path(),1,'参数错误');
				return array('s'=>'参数ID类型错误');
			}else {
				$id=intval($id);
				$ip=new \app\common\model\Ip;
				$where=array('ID'=>$id);
				if ($ip->where($where)->value('ID')) {
					$ip->where($where)->delete();
					parent::operating(request()->path(),0,'删除成功');
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
		parent::userauth(23);
		if (request()->isAjax()) {
			if (!$delid=explode(',',$this->_post('delid'))) {
				return array('s'=>'请选中后再删除');
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$ip=new \app\common\model\Ip;
			if ($ip->delete("$id")) {
				parent::operating(request()->path(),0,'删除成功');
				return array('s'=>'ok');
			}else {
				parent::operating(request()->path(),1,'删除失败：'.$ip->getError());
				return array('s'=>'删除失败');
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			return array('s'=>'非法请求');
		}
	}
	//IP操作结束
	

	//系统配置
	public function systemconfig() {
		parent::userauth2(30);
		$config['tarce'] = C('SHOW_PAGE_TRACE');
		$config['debug'] = C('APP_STATUS');
		$config['sessionuser'] = C('USER_AUTH_SESSION');
		$config['log'] = C('LOG_RECORD');
		$logclass = explode(',',C('LOG_LEVEL'));
		$config['url'] = C('URL_MODEL');						//URL模式
		$config['urlcase'] = C('URL_CASE_INSENSITIVE');			//URL大小写是否开启
		$config['tokenon'] = C('TOKEN_ON');						// 是否开启令牌验证
		$config['tokenreset'] = C('TOKEN_RESET');				//令牌验证出错后是否重置令牌 默认为true
		$config['dbfieldcheck'] = C('DB_FIELDTYPE_CHECK');		//是否开启字段类型验证
		foreach($logclass as $val) {
			$config['typelog'][$val]=$val;
		}
		$this->assign('config',$config);
		$this->display();
	}
	//系统配置修改
	public function systemconfig_do() {
		//验证用户权限
		parent::userauth2(30);
		if (request()->isPost()) {
			$config['SHOW_PAGE_TRACE']       = input('post.trace');
			$config['APP_STATUS']            = input('post.debug');
			$config['USER_AUTH_SESSION']     = input('post.sessionuser');
			$config['LOG_RECORD']            = input('post.log');
			$config['LOG_LEVEL']             = join(',',$_POST['typelog']);
			$config['URL_MODEL']             = input('post.url');						//Url模式
			$config['URL_CASE_INSENSITIVE']  = input('post.urlcase');			//URL大小写是否开启
			$config['TOKEN_ON']              = input('post.tokenon');
			$config['TOKEN_RESET']           = input('post.tokenreset');
			$config['DB_FIELDTYPE_CHECK']    = input('post.dbfieldcheck');
			$settingstr="<?php \n return array(\n";
			foreach($config as $key => $val){
				if ($val == 'false') {
					$settingstr.= "\t'".$key."'=>false,\n";
				}elseif ($val == 'true') {
					$settingstr.= "\t'".$key."'=>true,\n";
				}else {
					$settingstr.= "\t'".$key."'=>'".$val."',\n";
				}
			}
			$settingstr.="\n);\n?>";
			if (file_put_contents(CONF_PATH.'setting.php',$settingstr,FILE_USE_INCLUDE_PATH)) {
				parent::operating(request()->path(),0,'系统配置修改成功');
				$this->success('修改成功','systemconfig',2);
			}else {
				parent::operating(request()->path(),1,'系统配置修改失败');
				$this->error('修改失败，可能是由于没有写入权限导致。');
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//核心配置
	public function systemcore() {
		parent::userauth2(33);
		$config = require(CONF_PATH.'core.php');
		$this->assign('config',$config);
		$this->display();
	}
	//核心配置修改
	public function systemcore_do() {
		//验证用户权限
		parent::userauth2(33);
		if (request()->isPost()) {
			$config = $_POST;
			$settingstr="<?php \n return array(\n";
			foreach($config as $key => $val){
				$settingstr.= "\t'".$key."'=>'".strtolower($val)."',\n";
			}
			$settingstr.="\n);\n?>";
			if (file_put_contents(CONF_PATH.'core.php',$settingstr,FILE_USE_INCLUDE_PATH)) {
				parent::operating(request()->path(),0,'核心配置文件修改成功');
				$this->success('修改成功','systemcore',2);
			}else {
				parent::operating(request()->path(),1,'核心配置文件修改失败，可能是没有写入权限导致');
				$this->error('修改失败，可能是由于没有写入权限导致。');
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//网站配置
	public function systemwebsite() {
		parent::userauth2(53);
		$config = require(CONF_PATH.'webconfig.php');
		$this->assign('config',$config);
		$this->display();
	}
	//网站配置修改
	public function systemwebsite_do() {
		//验证用户权限
		parent::userauth2(53);
		if (request()->isPost()) {
			$config = $_POST;
			$settingstr="<?php \n return array(\n";
			foreach($config as $key => $val){
				$settingstr.= "\t'".$key."'=>'".$val."',\n";
			}
			$settingstr.="\n);\n?>";
			if (file_put_contents(CONF_PATH.'webconfig.php',$settingstr,FILE_USE_INCLUDE_PATH)) {
				parent::operating(request()->path(),0,'网站配置文件修改成功');
				$this->success('修改成功','systemwebsite',2);
			}else {
				parent::operating(request()->path(),1,'网站配置文件修改失败');
				$this->error('修改失败，可能是由于没有写入权限导致。');
			}
		}else {
			parent::operating(request()->path(),1,'非法请求');
			$this->error('非法请求');
		}
	}
	//获取省平台接口得到token
	public function getXgsyToken(){
		if(config('jsl.newgscloud_type') == "rb"){
			$data = array();
			$data['appId'] = config('jsl.appId');
			$appSecret = config('jsl.appSecret');
			$data['username']  = session('ThinkUser.Username');
			$data['timestamp'] = time();
			$data['timestamp'] = $data['timestamp'].'000';
			$data['signature'] = md5($data['appId'].$appSecret.$data['username'].'#_#'.$data['timestamp']);
			$result = $this->curl_api($data);
			$array = array();
			$array['appId'] 	= $data['appId'];
			$array['username'] 	= $data['username'];
			$array['token'] 	= $result->data->access_token;
			$array['errPage'] 	= config('jsl.errPage');
			if($result->status == 200){
				$_array_data = array(); 
				$_array_data['url'] 		= "http://app.newgscloud.com/portal/serverApi/login?".http_build_query($array);
				$_array_data['status'] 		= 1 ;
				$_array_data['msg'] 		= "操作成功！" ;
				echo json_encode($_array_data);exit;
			}else{
				echo json_encode(array("status"=>0,"msg"=>"操作异常，请联系技术人员！"));exit;
			}
		}elseif (config('jsl.newgscloud_type') == "fz"){
			$_array_data = array();
			$_array_data['url'] 		= config('jsl.newgscloud_url');
			$_array_data['status'] 		= 1 ;
			$_array_data['msg'] 		= "操作成功！" ;
			echo json_encode($_array_data);exit;
		}else{
			echo json_encode(array("status"=>0,"msg"=>"参数未配置，请联系技术人员！"));exit;
		}
	}
	public function curl_api($data){
		$ch = curl_init();
		$url = "http://app.newgscloud.com/portal/serverApi/getUserToken";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
		$result=curl_exec($ch);
		curl_close($ch); 
		$log_data = array();
		$log_data['api_url'] 			= $url;
		$log_data['request_parameter'] 	= http_build_query($data);
		$log_data['response_data'] 		= $result;
		$log_data['api_time'] 			= date("Y-m-d H:i:s");
		$rb_api_log = new \app\common\model\Rbapilog;
		$rb_api_log->save($log_data);
		return json_decode($result);
	}
}
