<?php
namespace app\common\model;
use think\Db;
// 声明命名空间
use Obs\ObsClient;
class News extends \think\Model
{
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),		
		array('Sid','require','分类ID获取失败'),								
		array('Sid','number','分类ID获取失败'),
		array('Title','require','请填写标题'),
		array('Title','1,80','标题请在80个字符以内！',0,'length'),
		array('Sortid','require','请填写排序ID'),								
		array('Sortid','number','排序ID必须是数字'),	
	);
	protected $insert  = ['dtime'];
	protected function setdtimeAttr()
	{
	    return date('Y-m-d H:i:s');
	}
	protected function setdepartment_idAttr()
	{
	    return session('Department.department_id');
	}
	public function profile()
	{
	    return $this->hasOne('User','uid')->field('Username');
	}
	public $news_type = array(
		1=>'图文',
		2=>'组图',
		3=>'视频',
		4=>'链接',
	);
	public $news_status = array(
		0=>'草稿',
		1=>'智能审核中',
		7=>'智能审核未通过',
		8=>'已撤回',
		9=>'已删除',
		10=>'已发布',
	);
    public function rareWordReplace($str="")
    {
        $str = str_replace("@xi","𠅤",$str);
        return $str;
    }
    /**
     * 获取热门线索
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getChooseList($param);
     */
    public function getHotList($param =array()){
    	$where = " WHERE 1 AND n.status = 10 AND n.news_id IS NOT NULL ";
    	$page_size = 1;
    	if($param['keywords'] !=""){
    		$where.=" AND (n.title LIKE '%{$param['keywords']}%')";
    	}
    	if($param['publish_start_time'] !=""){
    		$where.=" AND n.publish_time >='{$param['publish_start_time']}'";
    	}
    	if($param['publish_end_time'] !=""){
    		$where.=" AND n.publish_time <='{$param['publish_end_time']}'";
    	}
    	if($param['department_id'] !=""){
    		$where.=" AND (n.department_id ='{$param['department_id']}')";
    	}
        if($param['department_name'] !=""){
            $where.=" AND (d.department_name = '{$param['department_name']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (d.district ='{$param['district']}')";
        }
        if($param['industry_id'] !="" ){
            $where.=" AND (d.industry_id ='{$param['industry_id']}')";
        }
    	$page_config = config('paginate');
    	$database_prefix = config('database.prefix');
    	$page =  $param['page'];
    	if($param['list_rows']!=""){
    		$page_config['list_rows'] = $param['list_rows'];
    	}
    	if($page>=2){
    		$start_page = ($page-1)*$page_config['list_rows'];
    	}else{
    		$start_page = 0;
    	}
    	if($order_by_str==""){
    		$order_by_str = " count(*) DESC ";
    	}
    	$sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}news_choose_record AS r 
    	LEFT JOIN {$database_prefix}news AS n ON r.news_id = n.news_id
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY r.news_id) AS temp ";
    	$total = Db::query($sql);
    	$sql = "SELECT n.*,r.*,d.department_name,pr.region_name AS province_name,
			cr.region_name AS city_name,dr.region_name AS district_name
    	FROM {$database_prefix}news_choose_record AS r 
    	LEFT JOIN {$database_prefix}news AS n ON r.news_id = n.news_id
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY r.news_id ORDER BY {$order_by_str} ";
    	$sql.=" LIMIT $start_page,{$page_config['list_rows']}";
    	$hot_list = Db::query($sql);
    	return array("total"=>$total[0]['t'],"data"=>$hot_list);
    }
    /**
     * 获取我的选用线索
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getChooseList($param);
     */
    public function getChoosedList($param =array()){
        $where = " WHERE 1 AND n.status = 10 AND n.news_id IS NOT NULL ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%')";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['choose_start_time'] !=""){
            $where.=" AND r.choose_time >='{$param['choose_start_time']}'";
        }
        if($param['choose_end_time'] !=""){
            $where.=" AND r.choose_time <='{$param['choose_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (r.department_id ='{$param['department_id']}')";
        }
        if($param['department_name'] !=""){
            $where.=" AND (d.department_name = '{$param['department_name']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (d.district ='{$param['district']}')";
        }
        if($param['industry_id'] !=""){
            $where.=" AND (n.industry_id ='{$param['industry_id']}')";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " n.news_id DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM  {$database_prefix}news_choose_record AS r 
        LEFT JOIN {$database_prefix}news AS n ON r.news_id = n.news_id
        LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	{$where} GROUP BY n.news_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT n.*,r.*,d.department_name,pr.region_name AS province,
			cr.region_name AS city,dr.region_name AS district
    	FROM {$database_prefix}news_choose_record AS r  
    	LEFT JOIN {$database_prefix}news AS n ON r.news_id = n.news_id
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        $news = new News();
        foreach ($news_list as $k=>$v){
            $news_list[$k]['type_name']     = $news->news_type[$v['type']];
            $news_list[$k]['status_name']   = $news->news_status[$v['status']];
        }
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
    public function department()
    {
    	return $this->hasOne('Department','department_id','department_id');
    }
    /**
     * 获取我的选用线索总数
     * @param integer  		department_id 单位id
     * @param dateTime  	choose_start_time 选用时间-开始
     * @param dateTime  	choose_end_time 选用时间-结束
     * @return array        格式：$count
     * 调用方式举例:$news = news();$news->getChooseCountByDepartment($param);
     */
    public function getChooseCountByDepartment($param =array()){
    	$where = " WHERE 1 ";
    	$page_size = 1;
    	if($param['publish_start_time'] !=""){
    		$where.=" AND r.choose_time >='{$param['choose_start_time']}'";
    	}
    	if($param['publish_end_time'] !=""){
    		$where.=" AND r.choose_time <='{$param['choose_end_time']}'";
    	}
    	if($param['department_id'] !=""){
    		$where.=" AND (r.department_id ='{$param['department_id']}')";
    	}
    	$page_config = config('paginate');
    	$database_prefix = config('database.prefix');
    	$page = input('param.page');
    	if($param['list_rows']!=""){
    		$page_config['list_rows'] = $param['list_rows'];
    	}
    	if($page>=2){
    		$start_page = ($page-1)*$page_config['list_rows'];
    	}else{
    		$start_page = 0;
    	}
    	if($order_by_str==""){
    		$order_by_str = " r.choose_time DESC ";
    	}
    	$sql = "SELECT count(*) AS t FROM ( 
		SELECT 1 FROM {$database_prefix}news_choose_record AS r
    	{$where} GROUP BY r.news_id) AS temp ";
    	$total = Db::query($sql);
    	return $total[0]['t'];
    }
    /**
     * 获取我的发布线索总数
     * @param integer  		department_id 单位id
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @return array        格式：$count
     * 调用方式举例:$news = news();$news->getChooseList($param);
     */
    public function getNewsCountByDepartment($param =array()){
    	$where = " WHERE 1 AND n.status = 10 ";
    	$page_size = 1;
    	if($param['publish_start_time'] !=""){
    		$where.=" AND n.publish_time >='{$param['publish_start_time']}'";
    	}
    	if($param['publish_end_time'] !=""){
    		$where.=" AND n.publish_time <='{$param['publish_end_time']}'";
    	}
    	if($param['department_id'] !=""){
    		$where.=" AND (n.department_id ='{$param['department_id']}')";
    	}
    	$page_config = config('paginate');
    	$database_prefix = config('database.prefix');
    	$page = input('param.page');
    	if($param['list_rows']!=""){
    		$page_config['list_rows'] = $param['list_rows'];
    	}
    	if($page>=2){
    		$start_page = ($page-1)*$page_config['list_rows'];
    	}else{
    		$start_page = 0;
    	}
    	if($order_by_str==""){
    		$order_by_str = " n.publish_time DESC ";
    	}
    	$sql = "SELECT count(*) AS t FROM (
    	SELECT 1 FROM {$database_prefix}news AS n
    	{$where} GROUP BY n.news_id) AS temp ";
    	$total = Db::query($sql);
    	return $total[0]['t'];
    }
    /**
     * 获取某段时间内发布的线索资源总数
     * @param integer  		department_id 单位id
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @return array        格式：$count
     * 调用方式举例:$news = news();$news->getNewsCount($param);
     * 近两周线索总数 $param['publish_start_time'] = date("Y-m-d H:i:s",strtotime("-2 week"));
     */
    public function getNewsCount($param =array()){
    	$where = " WHERE 1 AND n.status = 10 ";
    	$page_size = 1;
    	if($param['publish_start_time'] !=""){
    		$where.=" AND n.publish_time >='{$param['publish_start_time']}'";
    	}
    	if($param['publish_end_time'] !=""){
    		$where.=" AND n.publish_time <='{$param['publish_end_time']}'";
    	}
    	if($param['department_id'] !=""){
    		$where.=" AND (n.department_id ='{$param['department_id']}')";
    	}
    	$page_config = config('paginate');
    	$database_prefix = config('database.prefix');
    	$page = input('param.page');
    	if($param['list_rows']!=""){
    		$page_config['list_rows'] = $param['list_rows'];
    	}
    	if($page>=2){
    		$start_page = ($page-1)*$page_config['list_rows'];
    	}else{
    		$start_page = 0;
    	}
    	if($order_by_str==""){
    		$order_by_str = " n.publish_time DESC ";
    	}
    	$sql = "SELECT count(*) AS t FROM (
    	SELECT 1 FROM {$database_prefix}news AS n
    	{$where} GROUP BY n.news_id) AS temp ";
    	$total = Db::query($sql);
    	return $total[0]['t'];
    }
    /**
     * 获取我的线索被选用总数
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer       list_rows 每页条数
     * @return array        格式：$count
     * 调用方式举例:$news = news();$news->getChooseList($param);
     */
    public function getMySelectedCount($param =array()){
    	$where = " WHERE 1 ";
    	$page_size = 1;
    	if($param['keywords'] !=""){
    		$where.=" AND (n.title LIKE '%{$param['keywords']}%')";
    	}
    	if($param['publish_start_time'] !=""){
    		$where.=" AND n.publish_time >='{$param['publish_start_time']}'";
    	}
    	if($param['publish_end_time'] !=""){
    		$where.=" AND n.publish_time <='{$param['publish_end_time']}'";
    	}
    	if($param['department_id'] !=""){
    		$where.=" AND (n.department_id ='{$param['department_id']}')";
    	}
    	$page_config = config('paginate');
    	$database_prefix = config('database.prefix');
    	$page = input('param.page');
    	if($param['list_rows']!=""){
    		$page_config['list_rows'] = $param['list_rows'];
    	}
    	if($page>=2){
    		$start_page = ($page-1)*$page_config['list_rows'];
    	}else{
    		$start_page = 0;
    	}
    	if($order_by_str==""){
    		$order_by_str = " n.publish_time DESC ";
    	}
    	$sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}news_choose_record AS r
    	LEFT JOIN {$database_prefix}news AS n ON r.news_id = n.news_id
    	{$where}  GROUP BY r.news_id) AS temp ";
    	$total = Db::query($sql);
    	return $total[0]['t'];
    }
    /**
     * 获取被选用线索总数
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer       list_rows 每页条数
     * @return array        格式：$count
     * 调用方式举例:$news = news();$news->getChooseNum($param);
     */
    public function getChooseNum($param =array()){
        $where = " WHERE 1 AND n.status = 10 ";
        $page_size = 1;
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page = input('param.page');
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $sql = "SELECT count(*) AS t FROM (
        SELECT 1 FROM {$database_prefix}news_choose_record AS r
    	LEFT JOIN {$database_prefix}news AS n ON r.news_id = n.news_id
    	{$where} GROUP BY n.department_id,n.news_id )  AS temp ";
        $total = Db::query($sql);
        return $total[0]['t'];
    }
    /**
     * 获取被选用线索总次数
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer       list_rows 每页条数
     * @return array        格式：$count
     * 调用方式举例:$news = news();$news->getChooseTimes($param);
     */
    public function getChooseTimes($param =array()){
        $where = " WHERE 1 AND n.status = 10  ";
        $page_size = 1;
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page = input('param.page');
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $sql = "SELECT count(*) AS t  FROM {$database_prefix}news_choose_record AS r
    	LEFT JOIN {$database_prefix}news AS n ON r.news_id = n.news_id
    	{$where}";
        $total = Db::query($sql);
        return $total[0]['t'];
    }
    //文本智能审核
    public function moderationText(){
    	//获取待审核稿件
    	$where = array();
    	$where['status'] = array("IN","1");
    	$where['text_status'] = '';
    	$unreview_list = $this->where($where)->order("news_id ASC")->select();
    	//调用审核接口
    	$huaweiyun = new Huaweiyun();
    	$news_content = new Newscontent();
    	$result_data = array();
    	foreach ($unreview_list as $k=>$v){
    		$news = new News();
    		$news_review = new Newsreview();
    		$news_review_history = new Newsreviewhistory();
    		$data = array();
    		$v['content'] = $news_content->where("news_id='{$v['news_id']}'")->value("content");
    		$data['items'][0]['text'] = $v['title'].strip_tags($v['content']).$v['notes'];
    		$result = $huaweiyun->getXskModerationText($data);
    		if($result->error_code == ""){
    			//接口调用成功
    			if($result->result->suggestion == "pass" && $v['image_status'] == "pass" && $v['video_status'] == "pass"){
    				$status = 10;
    			}else{
    			    if(($result->result->suggestion == "block" || $result->result->suggestion == "review")&& ($v['image_status'] != "" && $v['video_status'] != "")){
                        $status = 7;
                    }
    			    if($v['image_status'] == "" || $v['video_status'] == ""){
                        $status = 1;
                    }
    			}
    			//$news->save(array("text_status"=>$result->result->suggestion,"status"=>$status),"news_id='{$v['news_id']}'");
    			$news->save(array("text_status"=>$result->result->suggestion),"news_id='{$v['news_id']}'");
    			/*if($status == 10){
                    $message = new Message();
                    $message_data = array();
                    $message_data['message_type']       = 1;
                    $message_data['message_title']      = "《{$v['title']}》";
                    $message_data['message_content']    = "您的线索《{$v['title']}》智能审核通过，发布成功！";
                    $message_data['send_time']          = date("Y-m-d H:i:s");
                    $message_data['department_id']      = $v['department_id'];
                    $message_data['is_read'] = 0;
                    $message->save($message_data);
                }elseif ($status == 7 && $result->result->suggestion != "pass"){
                    $message = new Message();
                    $message_data = array();
                    $message_data['message_type']       = 1;
                    $message_data['message_title']      = "《{$v['title']}》";
                    $message_data['message_content']    = "您的线索《{$v['title']}》文本内容智能审核未通过，发布失败！<a href='' data-title='{$v['news_id']}' id='viewNews'>请点击查看详情！</a>";
                    $message_data['send_time']          = date("Y-m-d H:i:s");
                    $message_data['department_id']      = $v['department_id'];
                    $message_data['is_read'] = 0;
                    $message->save($message_data);
                }*/
    			$review_data = array();
    			$str =  "";
    			$review_data['news_id'] 	= $v['news_id'];
    			foreach ($result->result->detail as $k1=>$v1){
    				$str.=$huaweiyun->text_categories[$k1].":";
    				foreach ($v1 as $key=>$value){
    					$str.=$value.",";
    				}
    				$str = trim($str,",");
    				$str.=";";
    			}
    			$review_info = array();
    			$review_info = $news_review->where("news_id='{$v['news_id']}'")->find();
    			$review_data['text_result'] = $str;
    			$review_data['text_time'] 	= date("Y-m-d H:i:s");
    			if(empty($review_info)){
    				$news_review->save($review_data);
    			}else{
    				$news_review->save($review_data,"news_id='{$v['news_id']}'");
    			}
    			$news_review_history->save($review_data);
    			$result_data[$k]['status'] 	= $result->result->suggestion;
    		}else{
    			$result_data[$k]['status'] 	= $result->error_code;
    		}
    		$result_data[$k]['news_id'] 	= $v['news_id'];
    	}
    	return $result_data;
    }
    //图片智能审核
    public function moderationImage(){
    	//获取待审核稿件
    	$where = array();
    	$where['status'] = array("IN","1");
    	$where['image_status'] = '';
    	$unreview_list = $this->where($where)->order("news_id ASC")->select();
    	//调用审核接口
    	$huaweiyun = new Huaweiyun();
    	$news_content = new Newscontent();
    	$result_data = array();
    	foreach ($unreview_list as $k=>$v){
    		$news = new News();
    		$news_review = new Newsreview();
    		$news_review_history = new Newsreviewhistory();
    		$data = array();
    		if($v['image'] !=""){
    			$data['urls'][] = "http://www.gsxwxsk.com/uploads/news/".$v['image'];
    		}
    		$news_content_info = $news_content->where("news_id='{$v['news_id']}'")->find();
    		$result = preg_match_all('/<img.+?src=\"(.+?)\".+?>/i',$news_content_info['content'],$match);
    		if(!empty($match[1])){
    			foreach ($match[1] as $kimg=>$vimg){
                    if($kimg>8)continue;
    				if(preg_match("/(https:\/\/|http:\/\/)/i", $vimg)){
    					$data['urls'][] = $vimg;
    				}else{
    					$data['urls'][] = "http://www.gsxwxsk.com".$vimg;
    				}
    			}
    		}
    		$data['categories'] ="all";
    		if (!empty($data['urls'])){
                $result = $huaweiyun->getXskModerationImageBatch($data);
            }else{
                if($v['text_status'] == "pass" && $v['video_status'] == "pass"){
                    $status = 10;
                }else if($v['text_status'] == "" || $v['video_status'] == ""){
                    $status = 1;
                }else{
                    $status = 7;
                }
                //$news->save(array("image_status"=>"pass","status"=>$status),"news_id='{$v['news_id']}'");
                $news->save(array("image_status"=>"pass"),"news_id='{$v['news_id']}'");
                continue;
            }
    		if($result->error_code == ""){
    			//接口调用成功
    			$new_array = array();
    			foreach ($result->result as $ki=>$vi){
    				$new_array[] = $vi->suggestion;
    			}
    			$suggestion = "pass";
    			if(in_array("block", $new_array)){
    				$suggestion = "block";
    			}else if(in_array("review", $new_array)){
    				$suggestion = "review";
    			}else{
    				$suggestion = "pass";
    			}
                /*if($suggestion == "pass" && $v['text_status'] == "pass" && $v['video_status'] == "pass"){
                    $status = 10;
                }else{
                    if(($suggestion == "block" || $suggestion == "review")&& ($v['text_status'] != "" && $v['video_status'] != "")){
                        $status = 7;
                    }
                    if($v['text_status'] == "" || $v['video_status'] == "" ){
                        $status = 1;
                    }
//                    if($suggestion == "pass" && $v['text_status'] != "" && ($v['video_status'] == "block" || $v['video_status'] == "review" )
//                        || $suggestion == "pass" && $v['video_status'] != "" && ($v['text_status'] == "block" || $v['text_status'] == "review" )){
//                        $status = 7;
//                    }
                }*/
                if($suggestion=="pass" &&  $v['text_status'] == "pass" && $v['video_status'] == "pass" ){
                    $status = 10;
                }else if($v['text_status'] == "" || $v['video_status'] == ""){
                    $status = 1;
                }else{
                    $status = 7;
                }
    			//$news->save(array("image_status"=>$suggestion,"status"=>$status),"news_id='{$v['news_id']}'");
    			$news->save(array("image_status"=>$suggestion),"news_id='{$v['news_id']}'");
                /*if($status == 10){
                    $message = new Message();
                    $message_data = array();
                    $message_data['message_type']       = 1;
                    $message_data['message_title']      = "您的线索《{$v['title']}》智能审核通过，发布成功！";
                    $message_data['message_content']    = "您的线索《{$v['title']}》智能审核通过，发布成功！";
                    $message_data['send_time']          = date("Y-m-d H:i:s");
                    $message_data['department_id']      = $v['department_id'];
                    $message_data['is_read'] = 0;
                    $message->save($message_data);
                }elseif ($status == 7 && $suggestion != "pass"){
                    $message = new Message();
                    $message_data = array();
                    $message_data['message_type']       = 1;
                    $message_data['message_title']      = "您的线索《{$v['title']}》图片智能审核未通过，发布失败！";
                    $message_data['message_content']    = "您的线索《{$v['title']}》图片智能审核未通过，发布失败！<a href='' data-title='{$v['news_id']}' id='viewNews'>请点击查看详情！</a>";
                    $message_data['send_time']          = date("Y-m-d H:i:s");
                    $message_data['department_id']      = $v['department_id'];
                    $message_data['is_read'] = 0;
                    $message->save($message_data);
                }*/
    			$review_data = array();
    			$str =  "";
    			$review_data['news_id'] 	= $v['news_id'];
    			foreach ($result->result as $k1=>$v1){
    				$cs = $v1->category_suggestions;
    				if($cs->politics == "block"){
    					if($k1 == 0){
    						$str .=  "标题图：".$huaweiyun->review_categories[$cs->politics].$huaweiyun->image_categories["politics"];
    					}else{
    						$str .=  "内容第{$k1}张：".$huaweiyun->review_categories[$cs->politics].$huaweiyun->image_categories["politics"];
    					}
    				}
    				if($cs->politics == "review"){
    					if($k1 == 0){
    						$str .=  "标题图：".$huaweiyun->review_categories[$cs->politics].$huaweiyun->image_categories["politics"];
    					}else{
    						$str .=  "内容第{$k1}张：".$huaweiyun->review_categories[$cs->politics].$huaweiyun->image_categories["politics"];
    					}
    				}
    				if($cs->terrorism == "block"){
    					if($k1 == 0){
    						$str .=  "标题图：".$huaweiyun->review_categories[$cs->terrorism].$huaweiyun->image_categories["terrorism"];
    					}else{
    						$str .=  "内容第{$k1}张：".$huaweiyun->review_categories[$cs->terrorism].$huaweiyun->image_categories["terrorism"];
    					}
    				}
    				if($cs->terrorism == "review"){
    					if($k1 == 0){
    						$str .=  "标题图：".$huaweiyun->review_categories[$cs->terrorism].$huaweiyun->image_categories["terrorism"];
    					}else{
    						$str .=  "内容第{$k1}张：".$huaweiyun->review_categories[$cs->terrorism].$huaweiyun->image_categories["terrorism"];
    					}
    				}
    				if($cs->porn == "block"){
    					if($k1 == 0){
    						$str .=  "标题图：".$huaweiyun->review_categories[$cs->porn].$huaweiyun->image_categories["porn"];
    					}else{
    						$str .=  "内容第{$k1}张：".$huaweiyun->review_categories[$cs->porn].$huaweiyun->image_categories["porn"];
    					}
    				}
    				if($cs->porn == "review"){
    					if($k1 == 0){
    						$str .=  "标题图：".$huaweiyun->review_categories[$cs->porn].$huaweiyun->image_categories["porn"];
    					}else{
    						$str .=  "内容第{$k1}张：".$huaweiyun->review_categories[$cs->porn].$huaweiyun->image_categories["porn"];
    					}
    				}
    				if($cs->ad == "block"){
    					if($k1 == 0){
    						$str .=  "标题图：".$huaweiyun->review_categories[$cs->ad].$huaweiyun->image_categories["ad"];
    					}else{
    						$str .=  "内容第{$k1}张：".$huaweiyun->review_categories[$cs->ad].$huaweiyun->image_categories["ad"];
    					}
    				}
    				if($cs->ad == "review"){
    					if($k1 == 0){
    						$str .=  "标题图：".$huaweiyun->review_categories[$cs->ad].$huaweiyun->image_categories["ad"];
    					}else{
    						$str .=  "内容第{$k1}张：".$huaweiyun->review_categories[$cs->ad].$huaweiyun->image_categories["ad"];
    					}
    				}
    				$str .=";";
    			}    			
    			$review_info = array();
    			$review_info = $news_review->where("news_id='{$v['news_id']}'")->find();
    			$review_data['image_result'] = $str;
    			$review_data['image_time'] 	 = date("Y-m-d H:i:s");
    			if(empty($review_info)){
    				$news_review->save($review_data);
    			}else{
    				$news_review->save($review_data,"news_id='{$v['news_id']}'");
    			}
    			$news_review_history->save($review_data);
    			$result_data[$k]['status'] 	= $suggestion;
    		}else{
    			$result_data[$k]['status'] 	= $result->error_code;
    		}
    		$result_data[$k]['news_id'] 	= $v['news_id'];
    	}
    	return $result_data;
    }
    //视频上传智能审核
    public function moderationVideo(){
        //获取待审核稿件
        $where = array();
        $where['status'] = array("IN","1");
        $where['video_status'] = '';
        $unreview_list = $this->where($where)->order("news_id ASC")->select();
        //调用审核接口
        $huaweiyun = new Huaweiyun();
        $news_content = new Newscontent();
        $result_data = array();
        foreach ($unreview_list as $k=>$v){
            $news = new News();
            $news_review = new Newsreview();
            $news_review_history = new Newsreviewhistory();
            $data_video = new \stdClass();
            $data_video_array =  array();$inde_i = 0;
            if($v['video_path'] !=""){
                $data_video->url = "http://www.gsxwxsk.com/uploads/news/".$v['video_path'];
                $data_video->index=$inde_i;
                $data_video_array[] = $data_video;
            }
            $news_content_info = $news_content->where("news_id='{$v['news_id']}'")->find();
            $result = preg_match_all('/<video.+?src=\"(.+?)\".+?>/i',$news_content_info['content'],$match);
            if(!empty($match[1])){
                /*foreach ($match[1] as $kimg=>$vimg){
                    if(preg_match("/(https:\/\/|http:\/\/)/i", $vimg)){
                        $data_video->url   = $vimg;
                        $data_video->index = $inde_i+1;
                    }else{
                        $data_video->url   = "http://www.gsxwxsk.com".$vimg;
                        $data_video->index = $inde_i+1;
                    }
                    $data_video_array[] = $data_video;
                }*/
                foreach ($match[1] as $kimg=>$vimg){
                    if(preg_match("/(https:\/\/|http:\/\/)/i", $vimg)){
                        $data_video->url   = $vimg;
                        $data_video->index = $inde_i;
                    }else{
                        $data_video->url   = "http://www.gsxwxsk.com".$vimg;
                        $data_video->index = $inde_i;
                    }
                    $data_video_array[] = $data_video;
                    break;
                }
            }
            if (!empty($data_video_array)){
                $data = new \stdClass();
                $data->taskName = "news_id".$v['news_id'];
                $data->input->data = $data_video_array;
                $result = $huaweiyun->servicesVideoModerationCreateTasks($data);
            }else{
                if($v['text_status'] == "pass" && $v['image_status'] == "pass"){
                    $status = 10;
                }else if($v['text_status'] == "" || $v['image_status'] == ""){
                    $status = 1;
                }else{
                    $status = 7;
                }
                //$news->save(array("video_status"=>"pass","status"=>$status),"news_id='{$v['news_id']}'");
                $news->save(array("video_status"=>"pass"),"news_id='{$v['news_id']}'");
                /*if($status == 10){
                    $message = new Message();
                    $message_data = array();
                    $message_data['message_type']       = 1;
                    $message_data['message_title']      = "您的线索《{$v['title']}》智能审核通过，发布成功！";
                    $message_data['message_content']    = "您的线索《{$v['title']}》智能审核通过，发布成功！";
                    $message_data['send_time']          = date("Y-m-d H:i:s");
                    $message_data['department_id']      = $v['department_id'];
                    $message_data['is_read'] = 0;
                    $message->save($message_data);
                }*/
                continue;
            }
            if($result->error_code == ""){
                //接口调用成功
                if($result[0]->id!=""){
                    $news->save(array("video_status"=>"RUNNING"),"news_id='{$v['news_id']}'");
                    $news_review =  new Newsreview();
                    $news_review_history =  new Newsreviewhistory();
                    $review_info = $news_review->where("news_id='{$v['news_id']}'")->find();
                    $review_data = array("video_hwy_task_id"=>$result[0]->id);
                    $review_data['news_id'] 	= $v['news_id'];
                    if(empty($review_info)){
                        $news_review->save($review_data);
                    }else{
                        $news_review->save($review_data,"news_id='{$v['news_id']}'");
                    }
                    $news_review_history->save($review_data);
                    $result_data[$k]['video_hwy_task_id'] 	= $result[0]->id;
                    $result_data[$k]['status'] 	= $result[0]->id;
                }
            }else{
                $result_data[$k]['status'] 	= $result->error_code;
            }
            $result_data[$k]['news_id'] 	= $v['news_id'];
        }
        return $result_data;
    }
    //获取视频审核结果
    public function getVideoVeviewResult(){
        //获取待审核稿件
        $where = array();
        $where['status'] = array("IN","1");
        $where['video_status'] = 'RUNNING';
        $unreview_list = $this->where($where)->order("news_id ASC")->select();
        //调用审核接口
        $huaweiyun = new Huaweiyun();
        $news = new News();
        $news_review =  new Newsreview();
        $news_review_history =  new Newsreviewhistory();
        $result_data = array();
        foreach ($unreview_list as $k=>$v){
            $str = "";
            $video_hwy_task_id = $news_review->where("news_id='{$v['news_id']}'")->value("video_hwy_task_id");
            $result = $huaweiyun->getservicesVideoModerationTaskAndToken($video_hwy_task_id);
            if($result->state == "SUCCEEDED"){
                if($result->hostingResult->status == "AVAILABLE"){
                    $data = json_decode($result->hostingResult->data);
                    $suggestion = $data->result->suggestion;
                    foreach ($data->result->frames as $kf=>$vf){
                        $suspect_categories = "";
                        foreach($vf->suspect_categories as $ks=>$vs){
                            $suspect_categories.= $huaweiyun->video_categories[$vs]."、";
                        }
                        $suspect_categories = trim($suspect_categories,"、");
                        $str .=  "视频场景帧审核结果：第{$vf->frame_begin}秒至第{$vf->frame_end}秒,$suspect_categories;";
                    }
                    foreach ($data->result->voices as $kf=>$vf){
                        $suspect_categories = "";
                        foreach($vf->categories as $ks=>$vs){
                            $suspect_categories.= $huaweiyun->text_categories[$vs]."、";
                        }
                        $suspect_categories = trim($suspect_categories,"、");
                        $str .=  "语音场景审核结果：第{$vf->voice_begin}秒至第{$vf->voice_end}秒,$suspect_categories;";
                    }
                    //$this->save(array("video_status"=>$suggestion),"news_id='{$v['news_id']}'");
                }else{
                    //$suggestion = "FAILED";
                }
                if($v['text_status'] == "pass" && $v['image_status'] == "pass" && $suggestion=="pass"){
                    $status = 10;
                }else if($v['text_status'] == "" || $v['image_status'] == ""){
                    $status = 1;
                }else{
                    $status = 7;
                }
                //$news->save(array("video_status"=>$suggestion,"status"=>$status),"news_id='{$v['news_id']}'");
                $news->save(array("video_status"=>$suggestion),"news_id='{$v['news_id']}'");
                /*if($status == 10){
                    $message = new Message();
                    $message_data = array();
                    $message_data['message_type']       = 1;
                    $message_data['message_title']      = "您的线索《{$v['title']}》智能审核通过，发布成功！";
                    $message_data['message_content']    = "您的线索《{$v['title']}》智能审核通过，发布成功！";
                    $message_data['send_time']          = date("Y-m-d H:i:s");
                    $message_data['department_id']      = $v['department_id'];
                    $message_data['is_read'] = 0;
                    $message->save($message_data);
                }elseif ($status == 7 && $suggestion != "pass"){
                    $message = new Message();
                    $message_data = array();
                    $message_data['message_type']       = 1;
                    $message_data['message_title']      = "您的线索《{$v['title']}》视频智能审核未通过，发布失败！";
                    $message_data['message_content']    = "您的线索《{$v['title']}》视频智能审核未通过，发布失败！<a href='' data-title='{$v['news_id']}' id='viewNews'>请点击查看详情！</a>";
                    $message_data['send_time']          = date("Y-m-d H:i:s");
                    $message_data['department_id']      = $v['department_id'];
                    $message_data['is_read'] = 0;
                    $message->save($message_data);
                }*/
                $review_info = $review_data = array();
                $news_review = new Newsreview();
                $news_review_history = new Newsreviewhistory();
                $review_info = $news_review->where("news_id='{$v['news_id']}'")->find();
                $review_data['video_result'] = $str;
                $review_data['video_time'] 	 = date("Y-m-d H:i:s");
                if(empty($review_info)){
                    $news_review->save($review_data);
                }else{
                    $news_review->save($review_data,"news_id='{$v['news_id']}'");
                }
                $news_review_history->save($review_data);
            }else{
                continue;
            }
        }
    }
    /**
     * 获取所有线索列表
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer  		industry_id 行业id
     * @param integer  		province 省id
     * @param integer  		city 城市id
     * @param integer  		district 区县id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getNewsList($param);
     */
    public function getNewsList($param =array()){
        $where = " WHERE 1 AND n.status = 10  ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%') ";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        if($param['department_name'] !=""){
            $where.=" AND (d.department_name = '{$param['department_name']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (n.district ='{$param['district']}')";
        }
        if($param['industry_id'] !=""){
            $where.=" AND (d.industry_id ='{$param['industry_id']}')";
        }
        if($param['is_authority'] !=""){
            $where.=" AND (d.is_authority ='{$param['is_authority']}')";
        }
        if($param['department_type'] !=""){
            $where.=" AND (d.department_type ='{$param['department_type']}')";
        }
        //非省委宣传部单位发的信息
        if($param['is_not_provincial_party_committee_propaganda'] !=""){
            $where.=" AND (d.department_id NOT IN (3535,3551,3552,3563))";
        }
        if($param['region'] !=""){
            if($param['region'] == "city-district"){
                $where.=" AND d.city NOT IN (0,3878)";
            }
            if($param['region'] == "district-town"){
                $department = new Department();
                $where.=" AND d.district NOT IN ({$department->city_level_region_id_str}) ";
            }
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " n.publish_time DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT n.*,d.department_name,d.is_admin,pr.region_name AS province,
			cr.region_name AS city,dr.region_name AS district
    	FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        foreach ($news_list as $k=>$v){
            $news_list[$k]['type_name']     = $this->news_type[$v['type']];
            $news_list[$k]['status_name']   = $this->news_status[$v['status']];
            //查询改线索本单位是否已选用
            $department_id = $param['current_department_id'];//当前登录部门id
            $newschoose = new Newschooserecord();
            $newschoose_info = $newschoose->where(['news_id' => $v['news_id'], 'department_id' => $department_id])->find();
            $news_list[$k]['choose_status'] = $newschoose_info?1 : 0;       //1为已选用
            $news_list[$k]['department_role_id'] = $param['current_department_role_id'];//当前登录部门角色id
        }
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
    /**
     * 获取所有线索列表 通过elasticsearch方式获取
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer  		industry_id 行业id
     * @param integer  		province 省id
     * @param integer  		city 城市id
     * @param integer  		district 区县id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getNewsListByEs($param);
     */
    public function getNewsListByEs($param =array()){
        $where = " WHERE 1 AND n.status = 10  ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%') ";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        if($param['department_name'] !=""){
            $where.=" AND (d.department_name = '{$param['department_name']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (n.district ='{$param['district']}')";
        }
        if($param['industry_id'] !=""){
            $where.=" AND (d.industry_id ='{$param['industry_id']}')";
        }
        if($param['is_authority'] !=""){
            $where.=" AND (d.is_authority ='{$param['is_authority']}')";
        }
        if($param['department_type'] !=""){
            $where.=" AND (d.department_type ='{$param['department_type']}')";
        }
        if($param['region'] !=""){
            if($param['region'] == "city-district"){
                $where.=" AND d.city NOT IN (0,3878)";
            }
            if($param['region'] == "district-town"){
                $department = new Department();
                $where.=" AND d.district NOT IN ({$department->city_level_region_id_str}) ";
            }
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " n.publish_time DESC ";
        }
        $param['start_page'] = $start_page;
        $param['list_rows']  = $page_config['list_rows'];
        $es = new Es();
        $es_query = $es->search($param);
        $news_list = $es_query['hits']['hits'];
        $news_data = array();
        foreach ($news_list as $k=>$v){
            $news_data[$k] = $v['_source'];
            $news_data[$k]['type_name']     = $this->news_type[$v['_source']['type']];
            $news_data[$k]['status_name']   = $this->news_status[$v['_source']['status']];
            //查询改线索本单位是否已选用
            $department_id = $param['current_department_id'];//当前登录部门id
            $newschoose = new Newschooserecord();
            $newschoose_info = $newschoose->where(['news_id' => $v['_source']['news_id'], 'department_id' => $department_id])->find();
            $news_data[$k]['choose_status'] = $newschoose_info?1 : 0;       //1为已选用
            $news_data[$k]['department_role_id'] = $param['current_department_role_id'];//当前登录部门角色id
        }
        return array("total"=>$es_query['hits']['total'],"data"=>$news_data);
    }
    /**
     * 获取重点线索列表
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer  		industry_id 行业id
     * @param integer  		province 省id
     * @param integer  		city 城市id
     * @param integer  		district 区县id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getNewsList($param);
     */
    public function getImportantList($param =array()){
        $where = " WHERE 1 AND n.status = 10 ";
        $page_size = 1;
        if($param['current_department_role_id'] !=""){
            if(in_array($param['current_department_role_id'],array(1,2,4,20,21))){
                $where.=" AND (n.is_recommend IN (1))";
            }elseif (in_array($param['current_department_role_id'],array(5,6,8))){
                $where.=" AND (n.is_recommend IN (1,2))";
            }elseif (in_array($param['current_department_role_id'],array(9,11,12,999))){
                $where.=" AND (n.is_recommend IN (1,2,3))";
            }
        }
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%') ";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (d.district ='{$param['district']}')";
        }
        if($param['industry_id'] !=""){
            $where.=" AND (n.industry_id ='{$param['industry_id']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['recommend_by_department_level'] !=""){
            if($param['recommend_by_department_level'] == 1){
                $where.=" AND (n.is_recommend ='2')";
            }elseif ($param['recommend_by_department_level'] == 2){
                $where.=" AND (n.is_recommend ='3')";
            }
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " n.publish_time DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM  {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT n.*,d.department_name,pr.region_name AS province,
			cr.region_name AS city,dr.region_name AS district
    	FROM  {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        foreach ($news_list as $k=>$v){
            $news_list[$k]['type_name']     = $this->news_type[$v['type']];
            $news_list[$k]['status_name']   = $this->news_status[$v['status']];
            //查询改线索本单位是否已选用
            $department_id = session("Department.department_id");
            $newschoose = new Newschooserecord();
            $newschoose_info = $newschoose->where(['news_id' => $v['news_id'], 'department_id' => $department_id])->find();
            $news_list[$k]['choose_status'] = $newschoose_info?1 : 0;       //1为已选用
            $news_list[$k]['current_recommend'] = $param['current_recommend'];//当前登录部门角色id
        }
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
    /**
     * 获取省直线索列表
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer  		industry_id 行业id
     * @param integer  		province 省id
     * @param integer  		city 城市id
     * @param integer  		district 区县id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getNewsList($param);
     */
    public function getProvinceList($param =array()){
        $where = " WHERE 1 AND n.status = 10 AND d.province = 3878 ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%') ";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (d.district ='{$param['district']}')";
        }
        if($param['industry_id'] !=""){
            $where.=" AND (n.industry_id ='{$param['industry_id']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " n.publish_time DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT n.*,d.department_name,pr.region_name AS province,
			cr.region_name AS city,dr.region_name AS district
    	FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        foreach ($news_list as $k=>$v){
            $news_list[$k]['type_name']     = $this->news_type[$v['type']];
            $news_list[$k]['status_name']   = $this->news_status[$v['status']];
            //查询改线索本单位是否已选用
            $department_id = session("Department.department_id");
            $newschoose = new Newschooserecord();
            $newschoose_info = $newschoose->where(['news_id' => $v['news_id'], 'department_id' => $department_id])->find();
            $news_list[$k]['choose_status'] = $newschoose_info?1 : 0;       //1为已选用
        }
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
    /**
     * 获取市县线索列表
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer  		industry_id 行业id
     * @param integer  		province 省id
     * @param integer  		city 城市id
     * @param integer  		district 区县id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getNewsList($param);
     */
    public function getCityDistrictList($param =array()){
        $where = " WHERE 1 AND n.status = 10 AND d.city NOT IN (0,3878) ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%') ";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (d.district ='{$param['district']}')";
        }
        if($param['industry_id'] !=""){
            $where.=" AND (n.industry_id ='{$param['industry_id']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " n.publish_time DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT n.*,d.department_name,pr.region_name AS province,
			cr.region_name AS city,dr.region_name AS district
    	FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        foreach ($news_list as $k=>$v){
            $news_list[$k]['type_name']     = $this->news_type[$v['type']];
            $news_list[$k]['status_name']   = $this->news_status[$v['status']];
            //查询改线索本单位是否已选用
            $department_id = session("Department.department_id");
            $newschoose = new Newschooserecord();
            $newschoose_info = $newschoose->where(['news_id' => $v['news_id'], 'department_id' => $department_id])->find();
            $news_list[$k]['choose_status'] = $newschoose_info?1 : 0;       //1为已选用
        }
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
    /**
     * 获取市直线索列表
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer  		industry_id 行业id
     * @param integer  		province 省id
     * @param integer  		city 城市id
     * @param integer  		district 区县id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getNewsList($param);
     */
    public function getCityList($param =array()){
        $where = " WHERE 1 AND n.status = 10 ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%') ";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (d.district ='{$param['district']}')";
        }
        if($param['industry_id'] !=""){
            $where.=" AND (n.industry_id ='{$param['industry_id']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " n.publish_time DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT n.*,d.department_name,pr.region_name AS province,
			cr.region_name AS city,dr.region_name AS district
    	FROM  {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        foreach ($news_list as $k=>$v){
            $news_list[$k]['type_name']     = $this->news_type[$v['type']];
            $news_list[$k]['status_name']   = $this->news_status[$v['status']];
            //查询改线索本单位是否已选用
            $department_id = session("Department.department_id");
            $newschoose = new Newschooserecord();
            $newschoose_info = $newschoose->where(['news_id' => $v['news_id'], 'department_id' => $department_id])->find();
            $news_list[$k]['choose_status'] = $newschoose_info?1 : 0;       //1为已选用
        }
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
    /**
     * 获取县乡线索列表
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer  		industry_id 行业id
     * @param integer  		province 省id
     * @param integer  		city 城市id
     * @param integer  		district 区县id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getNewsList($param);
     */
    public function getDistrictTownList($param =array()){
        $department = new Department();
        $where = " WHERE 1 AND n.status = 10 AND d.district NOT IN ({$department->city_level_region_id_str}) ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%') ";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (d.district ='{$param['district']}')";
        }
        if($param['industry_id'] !=""){
            $where.=" AND (n.industry_id ='{$param['industry_id']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " n.publish_time DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM  {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT n.*,d.department_name,pr.region_name AS province,
			cr.region_name AS city,dr.region_name AS district
    	FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        foreach ($news_list as $k=>$v){
            $news_list[$k]['type_name']     = $this->news_type[$v['type']];
            $news_list[$k]['status_name']   = $this->news_status[$v['status']];
            //查询改线索本单位是否已选用
            $department_id = session("Department.department_id");
            $newschoose = new Newschooserecord();
            $newschoose_info = $newschoose->where(['news_id' => $v['news_id'], 'department_id' => $department_id])->find();
            $news_list[$k]['choose_status'] = $newschoose_info?1 : 0;       //1为已选用
        }
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
    /**
     * 获取本地线索列表
     * @param string        keywords 关键词查询（模糊匹配标题）
     * @param dateTime  	publish_start_time 发布时间-开始
     * @param dateTime  	publish_end_time 发布时间-结束
     * @param integer  		department_id 单位id
     * @param integer  		industry_id 行业id
     * @param integer  		province 省id
     * @param integer  		city 城市id
     * @param integer  		district 区县id
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$news = news();$news->getNewsList($param);
     */
    public function getDistrictList($param =array()){
        $department = new Department();
        $where = " WHERE 1 AND n.status = 10 ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (n.title LIKE '%{$param['keywords']}%' OR d.department_name LIKE '%{$param['keywords']}%') ";
        }
        if($param['publish_start_time'] !=""){
            $where.=" AND n.publish_time >='{$param['publish_start_time']}'";
        }
        if($param['publish_end_time'] !=""){
            $where.=" AND n.publish_time <='{$param['publish_end_time']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND (n.department_id ='{$param['department_id']}')";
        }
        if($param['province'] !=""){
            $where.=" AND (n.province ='{$param['province']}')";
        }
        if($param['city'] !=""){
            $where.=" AND (n.city ='{$param['city']}')";
        }
        if($param['district'] !=""){
            $where.=" AND (d.district ='{$param['district']}')";
        }
        if($param['industry_id'] !=""){
            $where.=" AND (n.industry_id ='{$param['industry_id']}')";
        }
        if($param['type'] !="" && $param['type'] !=-1){
            $where.=" AND (n.type ='{$param['type']}')";
        }
        if($param['subject_id'] !=""){
            $where.=" AND (s.subject_id ='{$param['subject_id']}')";
        }
        if($param['subject_id_str'] !=""){
            $where.=" AND (s.subject_id IN ({$param['subject_id_str']}))";
        }
        if($param['label_id'] !=""){
            $where.=" AND (l.label_id ='{$param['label_id']}')";
        }
        if($param['label_id_str'] !=""){
            $where.=" AND (l.label_id in ({$param['label_id_str']}))";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page =  $param['page'];
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        $order_by_str = '';
        if($order_by_str==""){
            $order_by_str = " n.publish_time DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT n.*,d.department_name,pr.region_name AS province,
			cr.region_name AS city,dr.region_name AS district
    	FROM {$database_prefix}news AS n 
    	LEFT JOIN {$database_prefix}department AS d ON n.department_id = d.department_id
    	LEFT JOIN {$database_prefix}news_subject AS s ON n.news_id = s.news_id
    	LEFT JOIN {$database_prefix}news_label AS l ON n.news_id = l.news_id
    	LEFT JOIN {$database_prefix}region AS pr ON n.province 	= pr.region_id
    	LEFT JOIN {$database_prefix}region AS cr ON n.city 		= cr.region_id
    	LEFT JOIN {$database_prefix}region AS dr ON n.district 	= dr.region_id
    	{$where} GROUP BY n.news_id ORDER BY {$order_by_str} ";
        $sql.=" LIMIT $start_page,{$page_config['list_rows']}";
        $news_list = Db::query($sql);
        foreach ($news_list as $k=>$v){
            $news_list[$k]['type_name']     = $this->news_type[$v['type']];
            $news_list[$k]['status_name']   = $this->news_status[$v['status']];
            //查询改线索本单位是否已选用
            $department_id = session("Department.department_id");
            $newschoose = new Newschooserecord();
            $newschoose_info = $newschoose->where(['news_id' => $v['news_id'], 'department_id' => $department_id])->find();
            $news_list[$k]['choose_status'] = $newschoose_info?1 : 0;       //1为已选用
        }
        return array("total"=>$total[0]['t'],"data"=>$news_list);
    }
    //智能审核状态检查
    public function checkReview(){
        //获取待审核稿件
        $where = array();
        $where['status'] = array("IN","1");
        $where['text_status'] = array("NEQ","");
        $where['image_status'] = array("NEQ","");
        $where['video_status'] = array("NEQ","");
        $unreview_list = $this->where($where)->order("news_id ASC")->column('');
        //调用审核接口
        $result_data = array();
        $config_array = array("review","pass");
        $config_array_final = array("review","pass","block");
        foreach ($unreview_list as $k=>$v) {
            $news = new News();
            $news_info = array();
            $news_info = $news->where("news_id='{$v['news_id']}'")->find();
            if (in_array($news_info['text_status'],$config_array) && in_array($news_info['image_status'],$config_array) && in_array($news_info['video_status'],$config_array)) {
                $status = 10;
                $message = new Message();
                $message_data = array();
                $message_data['message_type'] = 1;
                $message_data['message_title'] = "《{$v['title']}》";
                $message_data['message_content'] = "您的线索《{$v['title']}》智能审核通过，发布成功！";
                $message_data['send_time'] = date("Y-m-d H:i:s");
                $message_data['department_id'] = $v['department_id'];
                $message_data['is_read'] = 0;
                $message->save($message_data);
            }elseif(in_array($news_info['text_status'],$config_array_final) && in_array($news_info['image_status'],$config_array_final) && in_array($news_info['video_status'],$config_array_final)){
                $status = 7;
                $message = new Message();
                $message_data = array();
                $message_data['message_type'] = 1;
                $message_data['message_title'] = "《{$v['title']}》";
                $message_data['message_content'] = "您的线索《{$v['title']}》智能审核未通过，发布失败！<a href='' data-title='{$v['news_id']}' id='viewNews'>请点击查看详情！</a>";
                $message_data['send_time'] = date("Y-m-d H:i:s");
                $message_data['department_id'] = $v['department_id'];
                $message_data['is_read'] = 0;
                $message->save($message_data);
            }else{
                $status = $news_info['status'];
            }
            $news->save(array("status" => $status), "news_id='{$v['news_id']}'");
            $result_data[$k]['news_id'] = $v['news_id'];
            $result_data[$k]['status']  = $status;
        }
        return $result_data;
    }
//$video_info = getVideoInfo(ROOT_PATH . 'public' . DS . 'uploads'. DS . 'news'. DS."20210202/31a723bdb47506e041da8e2cd661f947.mp4");
//if($video_info['bitrate']>1500 && pathinfo($file, PATHINFO_EXTENSION) == "mp4"){
//require ROOT_PATH.'/extend/vendor/autoload.php';
//require ROOT_PATH.'/extend/obs-autoloader.php';
//// 创建ObsClient实例
//$obsClient = new ObsClient([
//'key' => 'UWHPT1XGJ9NJFWYGA7FV',
//'secret' => 'W9sVfKcxRPkDfh7L5tP0wF28ezwQItB25shTBjTE',
//'endpoint' => 'https://obs.cn-north-1.myhuaweicloud.com',
//]);
//
//
//$resp = $obsClient->putObject([
//'Bucket' => 'jsl-video',
//'Key' => 'uploads/news/20210202/31a723bdb47506e041da8e2cd661f947.mp4',
//'SourceFile' => ROOT_PATH . 'public' . DS . 'uploads'. DS . 'news'. DS."20210202/31a723bdb47506e041da8e2cd661f947.mp4"  // localfile为待上传的本地文件路径，需要指定到具体的文件名
//]);
//// 使用访问OBS
//
//// 关闭obsClient
//$obsClient -> close();
//
//$a = new \app\common\model\Huaweiyun;
//$data = array();
//$ss = new \stdclass();
//$ss->bucket='jsl-video';
//$ss->location='cn-north-1';
//$ss->object='uploads/news/20210202/31a723bdb47506e041da8e2cd661f947.mp4';
//$dd = new \stdclass();
//$dd->bucket='jsl-video';
//$dd->location='cn-north-1';
//$dd->object='uploads/news/20210202';
//$dd->file_name='31a723bdb47506e041da8e2cd661f947_transcodings.mp4';
//$data['input']   = $ss;
//$data['output']  = $dd;
//$data['trans_template_id'] = '514756';
//$b = $a->add_news_transcodings($data);
//}
    public function transcodings(){
        //获取待转码稿件
        $where = array();
        $where['status'] = array("IN","1,7,10");
        $where['transcodings_status'] = "";
        $where['type'] = 3;
        $untranscodings_list = $this->where($where)->order("news_id ASC")->select();
        $result_data = array();
        foreach ($untranscodings_list as $k=>$v){

            $video_info = getVideoInfo(ROOT_PATH . 'public' . DS . 'uploads'. DS . 'news'. DS.$v['video_path']);
            $news = new News();
            $news_extend = new Newsextend();
            if($video_info['bitrate']>1500 || pathinfo($v['video_path'], PATHINFO_EXTENSION) != "mp4"){
                require ROOT_PATH.'/extend/vendor/autoload.php';
                require ROOT_PATH.'/extend/obs-autoloader.php';
// 创建ObsClient实例
                $obsClient = new ObsClient([
                    'key' => 'UWHPT1XGJ9NJFWYGA7FV',
                    'secret' => 'W9sVfKcxRPkDfh7L5tP0wF28ezwQItB25shTBjTE',
                    'endpoint' => 'https://obs.cn-north-1.myhuaweicloud.com',
                ]);
                $resp = $obsClient->putObject([
                    'Bucket' => 'jsl-video',
                    'Key' => "uploads/news/{$v['video_path']}",
                    'SourceFile' => ROOT_PATH . 'public' . DS . 'uploads'. DS . 'news'. DS.$v['video_path']  // localfile为待上传的本地文件路径，需要指定到具体的文件名
                ]);
// 使用访问OBS
// 关闭obsClient
                $obsClient -> close();

                $a = new \app\common\model\Huaweiyun;
                $data = array();
                $ss = new \stdclass();
                $ss->bucket='jsl-video';
                $ss->location='cn-north-1';
                $ss->object="uploads/news/{$v['video_path']}";
                $dd = new \stdclass();
                $dd->bucket='jsl-video';
                $dd->location='cn-north-1';
                $dd->object='uploads/news/'.dirname($v['video_path']);
                $data['input']   = $ss;
                $data['output']  = $dd;
                $data['output_filenames']  = array(basename($v['video_path']).'_transcodings.mp4');
                $data['trans_template_id'] = '514756';
                $b = $a->add_news_transcodings($data);
                if($b->task_id !=""){
                    $task_id             = $b->task_id;
                    $transcodings_status = "WAITING";
                }
                $news->save(array("transcodings_status"=>$transcodings_status),"news_id='{$v['news_id']}'");
                $news_extend->save(array("video_transcodings_task_id"=>$task_id),"news_id='{$v['news_id']}'");
            }else{
                $transcodings_status = "NOT_NEED_TRANSCODING";
                $news->save(array("transcodings_status"=>$transcodings_status),"news_id='{$v['news_id']}'");
            }
            $result_data[$k]['status']  = $transcodings_status;
            $result_data[$k]['news_id'] = $v['news_id'];
            $video_data = array();
            $video_data['video_bitrate']          = $video_info['bitrate'];
            $video_data['video_seconds']          = $video_info['seconds'];
            $video_data['video_width']            = $video_info['width'];
            $video_data['video_height']           = $video_info['height'];
            $news_extend->save($video_data,"news_id='{$v['news_id']}'");
        }
        return $result_data;
    }
    public function startTranscodings(){

    }
    //获取视频审核结果
    /*
     * //        $a = new \app\common\model\Huaweiyun;
//        $data = array();
//        $data['task_id'] = '22900494';
//        $b = $a->query_news_transcodings($data);
     *
     * */
    public function getVideoTranscodingsResult(){
        //获取待转码稿件
        $where = array();
        $where['status'] = array("IN","1,7,10");
        $where['transcodings_status'] = array("IN",array('WAITING','TRANSCODING','NEED_TO_BE_AUDIT'));
        $untranscodings_list = $this->where($where)->order("news_id ASC")->select();
        //调用审核接口
        $huaweiyun = new Huaweiyun();
        $news = new News();
        $news_review =  new Newsreview();
        $news_extend =  new Newsextend();
        $news_review_history =  new Newsreviewhistory();
        $result_data = array();
        foreach ($untranscodings_list as $k=>$v){
            $news = new News();
            $news_extend = new Newsextend();
            $str = "";
            $video_hwy_task_id = $news_extend->where("news_id='{$v['news_id']}'")->value("video_transcodings_task_id");
            $data = array();
            $data['task_id'] = $video_hwy_task_id;
            $result = $huaweiyun->query_news_transcodings($data);
            if($result->task_array[0]->status !=""){
                //$news->save(array("video_status"=>$suggestion,"status"=>$status),"news_id='{$v['news_id']}'");
                $news->save(array("transcodings_status"=>$result->task_array[0]->status), "news_id='{$v['news_id']}'");
                $review_info = $review_data = array();
                if($result->task_array[0]->status == "SUCCEEDED"){
                    $news_extend->save(array("transcodings_video_path"=>$result->task_array[0]->output->object."/".$result->task_array[0]->output_file_name[0]),
                        "news_id='{$v['news_id']}'");
                }
            }
            $result_data[$k]['status']  = $result->task_array[0]->status;
            $result_data[$k]['news_id'] = $v['news_id'];
        }
        return $result_data;
    }
}