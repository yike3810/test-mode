<?php
namespace app\common\model;
class Huaweiyun extends \think\Model {
	public $text_categories = array(
		"politics"		=>	"涉政",
		"porn"			=>	"涉黄",
		"ad"			=>	"广告",
		"abuse"			=>	"辱骂",
		"contraband"	=>	"违禁品",
		"flood"			=>	"灌水"
	);
	public $image_categories = array(
		"politics"		=>	"涉及政治人物",
		"terrorism"		=>	"包含涉政暴恐元素",
		"porn"			=>	"包含涉黄内容元素",
		"ad"			=>	"包含广告",
	);
	public $video_categories = array(
		"politics"		=>	"涉及政治人物",
		"terrorism"		=>	"包含涉政暴恐元素",
		"porn"			=>	"包含涉黄内容元素",
		"ad"			=>	"包含广告",
	);
	public $review_categories = array(
		"block"		=>	"",
		"review"	=>	"可能",
	);
	public $video_review_state   = array(
        "PENDING_CREATE"=>"等待中",
        "SCHEDULING"=>"调度中",
        "CREATE_FAIL"=>"创建失败",
        "STARTING"=>"启动中",
        "RUNNING"=>"运行中",
        "SUCCEEDED"=>"运行成功",
        "FAILED"=>"运行失败",
        "PENDING_DELETE"=>"删除中",
        "DELETE_FAIL"=>"删除失败",
        "ABNORMAL"=>"运行异常",
        "UPDATING"=>"更新中",
        "PENDING_FREEZE"=>"结中",
        "FROZEN"=>"已冻结",
        "RECOVERING"=>"恢复中",
    );
	public $transcodings_status = array(
        "WAITING"=>"等待启动",
        "TRANSCODING"=>"转码中",
        "SUCCEEDED"=>"转码成功",
        "FAILED"=>"转码失败",
        "CANCELED"=>"已删除",
        "NEED_TO_BE_AUDIT"=>"片源待审核"
    );
	public function getToken(){
		$username = "jsljsgs";
		$password = "Gsda1ly@123Jsl";
		$domainName = "gsxmtjt";
		$requestBody = $this->requestbody_get_token($username, $password, $domainName);
		$ch = curl_init();
		$url = "https://iam.cn-north-1.myhuaweicloud.com/v3/auth/tokens";
		$header[] = "Content-type: application/json;charset=utf8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
		$result=curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // 获得响应头大小
		$header = substr($result, 0, $header_size); // 根据头大小获取头信息
		curl_close($ch);
		$hwy_api_log = new Hwyapilog;
		$log_data 	 = array();
		$log_data['api_url']			= $url;
		$log_data['request_parameter']	= $header.$requestBody;
		$log_data['response_data']		= $result;
		$hwy_api_log = $hwy_api_log->save($log_data);
		return $this->get_token_by_headers($header);
	}
	/*
	 * 		$data = '{
	 *	 "items": [
	 *	  {
	 *	   "text": "11"
	 *	  }
	 *	 ]
	 *	}';
	 * 获取文本审核结果
	 * */
	public function getModerationText($token,$data){

		$ch = curl_init();
		$url = "https://moderation.cn-north-1.myhuaweicloud.com/v1.0/moderation/text";
		$header[] = "Content-type:application/json;charset=utf8";
		$header[] = "X-Auth-Token:".$token;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data,JSON_UNESCAPED_UNICODE));
		$result=curl_exec($ch);
		curl_close($ch);
		$hwy_api_log = new Hwyapilog;
		$log_data 	 = array();
		$log_data['api_url']			= $url;
		$log_data['request_parameter']	= $header.json_encode($data,JSON_UNESCAPED_UNICODE);
		$log_data['response_data']		= $result;
		$hwy_api_log = $hwy_api_log->save($log_data);
		return json_decode($result);
	}
	/*stdClass Object
	(
	    [result] => stdClass Object
	        (
	            [detail] => stdClass Object
	                (
	                    [abuse] => Array
	                        (
	                            [0] => 沙比
	                        )
	                )
	            [suggestion] => block
	        )
	)*/
	/*
	 * 获取文本审核结果-直接调用
	 * */
	public function getXskModerationText($data){
		$token = $this->getToken();
		return $this->getModerationText($token,$data);
	}
	/*
	 * $data = '{
	 *	 "urls": [
	 *	  {
	 *	   "https://bucketname.obs.myhwclouds.com/ObjectName1",
	 *	   "https://bucketname.obs.myhwclouds.com/ObjectName2",
	 *	  }
	 *	 ]
	 *	}';
	 * 获取图像审核结果
	 * */
	public function getModerationImageBatch($token,$data){
		
		$ch = curl_init();
		$url = "https://moderation.cn-north-1.myhuaweicloud.com/v1.0/moderation/image/batch";
		$header[] = "Content-type:application/json;charset=utf8";
		$header[] = "X-Auth-Token:".$token;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data,JSON_UNESCAPED_UNICODE));
		$result=curl_exec($ch);
		curl_close($ch);
		$hwy_api_log = new Hwyapilog;
		$log_data 	 = array();
		$log_data['api_url']			= $url;
		$log_data['request_parameter']	= $header.json_encode($data,JSON_UNESCAPED_UNICODE);
		$log_data['response_data']		= $result;
		$hwy_api_log = $hwy_api_log->save($log_data);
		return json_decode($result);
	}
	/*
	 * 获取图片审核结果-直接调用
	 * */
	public function getXskModerationImageBatch($data){
		$token = $this->getToken();
		return $this->getModerationImageBatch($token,$data);
	}
    /*
     * 视频审核创建作业-直接调用
     * */
    public function servicesVideoModerationCreateTasks($data){
        $token = $this->getToken();
        return $this->servicesVideoModerationTasks($token,$data);
    }
    /*
     * $data = '{
     *	 "urls": [
     *	  {
     *	   "https://bucketname.obs.myhwclouds.com/ObjectName1",
     *	   "https://bucketname.obs.myhwclouds.com/ObjectName2",
     *	  }
     *	 ]
     *	}';
     * 获取图像审核结果
     * */
    public function servicesVideoModerationTasks($token,$data){

        $ch = curl_init();
        $url = "https://iva.cn-north-1.myhuaweicloud.com/v1/2a766913780b47f3aea107b8f72b56e5/services/video-moderation/tasks";
        $header[] = "Content-type:application/json;charset=utf8";
        $header[] = "X-Auth-Token:".$token;
        $data->serviceVersion = "1.2";
        $ss = new \stdclass();
        $ss->common['frame_interval'] = 1;
        $ss->common['categories'] = 'all';
        $data->serviceVersion->serviceConfig = $ss;
        $data->output->obs->bucket = 'jslclues';
        $data->output->obs->path = 'output/';
        $data->output->hosting = new \stdClass();
        $data->serviceConfig->common->frame_interval = 5;
        $data->serviceConfig->common->categories = "porn,terrorism,politics";
        $data->serviceConfig->common->text_categories = "porn,politics";
        $data->serviceConfig->common->upload = "false";
        $data->input->type = "url";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data,JSON_UNESCAPED_UNICODE));
        $result=curl_exec($ch);
        curl_close($ch);
        $hwy_api_log = new Hwyapilog;
        $log_data 	 = array();
        $log_data['api_url']			= $url;
        $log_data['request_parameter']	= $header.json_encode($data,JSON_UNESCAPED_UNICODE);
        $log_data['response_data']		= $result;
        $hwy_api_log = $hwy_api_log->save($log_data);
        return json_decode($result);
    }
    public function getservicesVideoModerationTaskAndToken($tasks_id){
        $token = $this->getToken();
        return $this->getservicesVideoModerationTasks($token,$tasks_id);
    }
    function getservicesVideoModerationTasks($token,$tasks_id){
        $url = "https://iva.cn-north-1.myhuaweicloud.com/v1/2a766913780b47f3aea107b8f72b56e5/services/video-moderation/tasks/{$tasks_id}";
        $header[] = "Content-type:application/json;charset=utf8";
        $header[] = "X-Auth-Token:".$token;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $hwy_api_log = new Hwyapilog;
        $log_data 	 = array();
        $log_data['api_url']			= $url;
        $log_data['request_parameter']	= $header.$tasks_id;
        $log_data['response_data']		= $result;
        $hwy_api_log = $hwy_api_log->save($log_data);
        return json_decode($result);
    }

	/**
	 * 拼接json信息，获得请求体信息
	 * @param $username 用户名
	 * @param $password 密码
	 * @param $domain 与用户名一致
	 *
	 */
	function requestbody_get_token($username, $password, $domainName)
	{
		$regionName = "cn-north-1";
		
		$param = array (
				"auth" => array (
						"identity" => array (
								"password" => array (
										"user" => array (
												"password" => $password,
												"domain" => array (
														"name" => $domainName 
												),
												"name" => $username 
										) 
								),
								"methods" => array (
										"password" 
								) 
						
						),
						"scope" => array (
								"project" => array (
										"name" => $regionName 
								) 
						) 
				) 
		);
		return urldecode ( json_encode ( $param ) );
	}
	/**
	 * 根据请求体的信息，获取到token的内容
	 * @param $headers
	 * @return string
	 */
	function get_token_by_headers($headers)
	{
		
		$headArr = explode("\r\n", $headers);
		foreach ($headArr as $loop) {
			if (strpos($loop, "X-Subject-Token") !== false) {
				$token = trim(substr($loop, 17));
				return $token;
			}
		}
	}
	/*
	 *
	 * 	    $a = new \app\common\model\Huaweiyun;
        $data = array();
        $ss = new \stdclass();
        $ss->bucket='jsl-video';
        $ss->location='cn-north-1';
        $ss->object='/uploads/20180612qiao_H.264_1920x1080_AAC_2100.mp4';
        $dd = new \stdclass();
        $dd->bucket='jsl-video';
        $dd->location='cn-north-1';
        $dd->object='/uploads/20180612qiao_H.264_1920x1080_AAC_2100_zhihou.mp4';
        $data['input']   = $ss;
        $data['output']  = $dd;
        $data['trans_template_id'] = '514756';
	 * */
    function add_news_transcodings($data){
        $token = $this->getToken();
        return $this->add_transcodings($token,$data);
    }
    public function add_transcodings($token,$data){

        $ch = curl_init();
        //$url = "https://obs.cn-north-1.myhuaweicloud.com/v1/cn-north-1/transcodings";
        $url = "https://mts.cn-north-1.myhuaweicloud.com/v1/2a766913780b47f3aea107b8f72b56e5/transcodings";
        $header[] = "Content-type:application/json;charset=utf8";
        $header[] = "X-Auth-Token:".$token;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data,JSON_UNESCAPED_UNICODE));
        $result=curl_exec($ch);
        curl_close($ch);
        $hwy_api_log = new Hwyapilog;
        $log_data 	 = array();
        $log_data['api_url']			= $url;
        $log_data['request_parameter']	= $header.json_encode($data,JSON_UNESCAPED_UNICODE);
        $log_data['response_data']		= $result;
        $hwy_api_log = $hwy_api_log->save($log_data);
        return json_decode($result);
    }
    function query_news_transcodings($data){
        $token = $this->getToken();
        return $this->query_transcodings($token,$data);
    }
    public function query_transcodings($token,$data){

        $ch = curl_init();
        //$url = "https://obs.cn-north-1.myhuaweicloud.com/v1/cn-north-1/transcodings";
        $url = "https://mts.cn-north-1.myhuaweicloud.com/v1/2a766913780b47f3aea107b8f72b56e5/transcodings?".http_build_query($data);
        $header[] = "Content-type:application/json;charset=utf8";
        $header[] = "X-Auth-Token:".$token;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
        $result=curl_exec($ch);
        curl_close($ch);
        $hwy_api_log = new Hwyapilog;
        $log_data 	 = array();
        $log_data['api_url']			= $url;
        $log_data['request_parameter']	= $header.json_encode($data,JSON_UNESCAPED_UNICODE);
        $log_data['response_data']		= $result;
        $hwy_api_log = $hwy_api_log->save($log_data);
        return json_decode($result);
    }
}