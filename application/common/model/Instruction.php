<?php
namespace app\common\model;
class Instruction extends \think\Model
{
	//区县接收指令
	public function instruction_receive($data= array()){
		$instruction_data = array();
		$instruction_data['instruction_id'] 	= $data['instruction_id'];
		$instruction_data['instruction_name'] 	= $data['instruction_name'];
		$instruction_data['category_id'] 		= $data['category_id'];
		$instruction_data['category_name'] 		= $data['category_name'];
		$instruction_data['instruction_content']= $data['instruction_content'];
		$instruction_data['url'] 				= $data['url'];
		$instruction_data['contact'] 			= $data['contact'];
		$instruction_data['attachment'] 		= $data['attachment'];
		$instruction_data['user_id'] 			= $data['user_id'];
		$instruction_data['name'] 				= $data['name'];
		$instruction_data['add_time'] 			= $data['add_time'];
		$instruction_data['send_time'] 			= $data['send_time'];
		$instruction_data['type'] 				= $data['type'];
		$instruction_data['instruction_status'] = $data['instruction_status'];
		$instruction_data['qx_status'] 			= 0;
		$instruction_data['receive_time'] 		= date("Y-m-d H:i:s");
		$this->save($instruction_data);
	}
	//区县回复指令
	public function instruction_reply($reply_id){
		$instruction_reply = new \app\common\model\Instructionreply;
		$reply_info = $instruction_reply->where("reply_id='{$reply_id}'")->find()->toArray();
		$reply_info['attachment'] 	= $reply_info['attachment'];
		$reply_info['image'] 		= $reply_info['image'];
		return $this->instruction_reply_api($reply_info);
	}
	//区县回复指令调用api
	public function instruction_reply_api($data){
		$ch = curl_init();
		$url = config('jsl.city_api_url')."api/instruction/reply";
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
		$api_log = new \app\common\model\Apilog;
		$api_log->save($log_data);
		return json_decode($result);
	}
	//市级指令回复处理
	public function instruction_reply_process($data= array()){
		$instruction_data = array();
		$reply_data['instruction_id'] 	= $data['instruction_id'];
		$reply_data['qx_code'] 			= $data['qx_code'];
		$reply_data['reply_content'] 	= $data['reply_content'];
		$reply_data['attachment'] 		= $data['attachment'];
		$reply_data['image'] 			= $data['image'];
		$reply_data['user_id'] 			= $data['user_id'];
		$reply_data['user_name'] 		= $data['user_name'];
		$reply_data['reply_time'] 		= $data['reply_time'];
		$reply_data['api_status'] 		= $data['api_status'];
		$summary_instruction_reply = new \app\common\model\Summaryinstructionreply;
		$summary_instruction_reply->save($reply_data);
	}
}
