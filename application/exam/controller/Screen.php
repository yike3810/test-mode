<?php
//志愿服务队管理类
namespace app\exam\controller;

use think\Controller;
use think\Request;
use think\Url;

class Screen extends Controller
{
    public function analysis()
    {

        return $this->fetch();
    }

    public function countyvisual()
    {
        $member = new \app\common\model\Member;
        $service = new \app\common\model\Service;
        $item = new \app\common\model\Item;
        $activity = new \app\common\model\Activity;
        $service_team = new \app\common\model\Serviceteam;
        $member_service = new \app\common\model\Memberservice;
        $parameter = new \app\common\model\Parameter;

//        累计用户数
        $cumulative_users_num = $member->where(['type' => array('in', '0,1')])->count();

//        累计访问量
        $cumulative_visitor_num = 169534;

//        今日新增
        $today_new_num = $member->where(['type' => array('in', '0,1'), 'register_time' => array('egt', date("Y/m/d"))])->count();

//        志愿者数
        $volunteer_num = $member->where(['type' => '1'])->count();

//        服务队数
        $service_num = $service->where(['service_cate' => array('neq', '117')])->count();

//        实践所站数
        $service_burg_num = $service->where(['service_cate' => '117'])->count();

        /*顶部数据*/
        $this->assign('cumulative_users_num', $cumulative_users_num);
        $this->assign('cumulative_visitor_num', $cumulative_visitor_num);
        $this->assign('today_new_num', $today_new_num);
        $this->assign('volunteer_num', $volunteer_num);
        $this->assign('service_num', $service_num);
        $this->assign('service_burg_num', $service_burg_num);


//        服务/活动
        $interval = 3600 * 24 * 10;
        $time = strtotime(date("Y-m-d"));
//        x轴文字
        $opt1_xAxis_arr = array();
//        服务数量
        $opt1_service_arr = array();
//        活动数量
        $opt1_activity_arr = array();

        for ($i = 0; $i < 10; $i++) {
            $time_temp = $time - $interval * $i;
            $where = array(array('egt', date('Y-m-d H:i:s', $time_temp)), array('lt', date('Y-m-d H:i:s', $time_temp + $interval)));
            $service_temp = $item->where(['add_time' => $where])->count();
            $activity_temp = $activity->where(['publisher_time' => $where])->count();

            $xAxis_temp = date("m/d", $time_temp);

            array_unshift($opt1_xAxis_arr, $xAxis_temp);
            array_unshift($opt1_service_arr, $service_temp);
            array_unshift($opt1_activity_arr, $activity_temp);
        }

        /*表一数据*/
        $this->assign('xAxis_arr', json_encode(array_values($opt1_xAxis_arr)));
        $this->assign('service_arr', json_encode(array_values($opt1_service_arr)));
        $this->assign('activity_arr', json_encode(array_values($opt1_activity_arr)));


        $opt2_xAxis_arr = ['文化服务', '健康体育', '科技科普', '理论宣讲', '教育服务'];

        $opt2_whfw = 168;
        $opt2_jsty = 205;
        $opt2_kjkp = 105;
        $opt2_llxj = 98;
        $opt2_jyfw = 175;
        $opt2_sum = max([$opt2_whfw, $opt2_jsty, $opt2_kjkp, $opt2_llxj, $opt2_jyfw]);

        /*表二数据*/
        $this->assign('opt2_xAxis_arr', json_encode($opt2_xAxis_arr));
        $this->assign('opt2_whfw', $opt2_whfw);
        $this->assign('opt2_jsty', $opt2_jsty);
        $this->assign('opt2_kjkp', $opt2_kjkp);
        $this->assign('opt2_llxj', $opt2_llxj);
        $this->assign('opt2_jyfw', $opt2_jyfw);
        $this->assign('opt2_sum', $opt2_sum);


        //活动总数
        $activity_sum = $activity->where(['activity_status' => '10'])->count();
        $activity_sum = $activity_sum <= 1 ? 1 : $activity_sum;

        //服务总数
        $service_sum = $item->where(['service_status' => '10'])->count();
        $service_sum = $service_sum <= 1 ? 1 : $service_sum;

        //服务总时长
        $service_time_long_sum = $item->where(['service_status' => '10'])->sum('time_long');;
        $service_time_long_sum = $service_time_long_sum <= 1 ? 1 : $service_time_long_sum;

        //志愿者总数(查询志愿者与服务队对应表,保证数据准确性)
        $volunteer_sum = $member_service->count();
        $volunteer_sum = $volunteer_sum <= 1 ? 1 : $volunteer_sum;

        $where_team['service_cate'] = array('neq', 117);
        $service_team_list_temp = $service_team->where($where_team)->column('id,service_name,service_function,longitude,latitude');
        $service_team_list = array_values($service_team_list_temp);
        foreach ($service_team_list as $k => $v) {
            $service_team_list[$k]['service_name_cut'] = mb_substr($service_team_list[$k]['service_name'], 0, 4, 'utf-8') . '..';
            //活动数
            $service_team_list[$k]['activity_num'] = $activity->where(['publisher' => $v['id'], 'activity_status' => '10'])->count();
            $service_team_list[$k]['ratio_activity_num'] = round($service_team_list[$k]['activity_num'] / $activity_sum * 100, 2);
            //服务数
            $service_team_list[$k]['service_num'] = $item->where(['service_team_id' => $v['id'], 'service_status' => '10'])->count();
            $service_team_list[$k]['ratio_service_num'] = round($service_team_list[$k]['service_num'] / $service_sum * 100, 2);
            //服务时长
            $service_team_list[$k]['service_time_long_num'] = $item->where(['service_team_id' => $v['id'], 'service_status' => '10'])->sum('time_long');
            $service_team_list[$k]['ratio_service_time_long_num'] = round($service_team_list[$k]['service_time_long_num'] / $service_time_long_sum * 100, 2);
            //志愿者数
            $service_team_list[$k]['volunteer_num'] = $member_service->where(['service_id' => $v['id']])->count();
            $service_team_list[$k]['ratio_volunteer_num'] = round($service_team_list[$k]['volunteer_num'] / $volunteer_sum * 100, 2);
            //排名
            $service_team_list[$k]['sort'] = $service_team_list[$k]['ratio_activity_num'] + $service_team_list[$k]['ratio_service_num'] +
                $service_team_list[$k]['ratio_service_time_long_num'] + $service_team_list[$k]['ratio_volunteer_num'];
        }
        //二维数组排序
        array_multisort(array_column($service_team_list, 'sort'), SORT_DESC, $service_team_list);

        $opt3_yAxis = array();//y轴类目
        $opt3_ratio_activity = array();//活动占比
        $opt3_ratio_service = array();//服务占比
        $opt3_ratio_duration = array();//时长占比
        $opt3_ratio_volunteer = array();//志愿者占比

        foreach ($service_team_list as $k => $v) {
            if ($k >= 5) {
                break;
            }
            array_push($opt3_yAxis, $v['service_name_cut']);//y轴类目
            array_push($opt3_ratio_activity, $v['ratio_activity_num']);//活动占比
            array_push($opt3_ratio_service, $v['ratio_service_num']);//服务占比
            array_push($opt3_ratio_duration, $v['ratio_service_time_long_num']);//时长占比
            array_push($opt3_ratio_volunteer, $v['ratio_volunteer_num']);//志愿者占比
        }


        /*表三数据*/
        $this->assign('opt3_yAxis', json_encode($opt3_yAxis));
        $this->assign('opt3_ratio_activity', json_encode($opt3_ratio_activity));
        $this->assign('opt3_ratio_service', json_encode($opt3_ratio_service));
        $this->assign('opt3_ratio_duration', json_encode($opt3_ratio_duration));
        $this->assign('opt3_ratio_volunteer', json_encode($opt3_ratio_volunteer));


        $parameter_list = $parameter->column('param_value', 'param_name');


        $county_name = $parameter_list['system_name'];
        $county_center_point = [$parameter_list['center_longitude'], $parameter_list['center_latitude']];//区县中心点
        $county_center_zoom = $parameter_list['map_zoom'];

        $markers = array();
        $marker_temp = array();
        foreach ($service_team_list as $k => $v) {
            if (!empty($v['longitude']) && !empty($v['latitude'])) {

                $marker_temp['pointx'] = $v['longitude'];
                $marker_temp['pointy'] = $v['latitude'];
                $marker_temp['servivename'] = $v['service_name'];
                $marker_temp['servicefunction'] = $v['service_function'];
                array_push($markers, $marker_temp);
            }

        }
        /*地图数据*/
        $this->assign('county_name', $county_name);
        $this->assign('county_center_pointx', $parameter_list['center_longitude']);
        $this->assign('county_center_pointy', $parameter_list['center_latitude']);
        $this->assign('county_center_zoom', $county_center_zoom);
        $this->assign('markers', json_encode($markers));
        return $this->fetch();
    }

    public function coordinatespick()
    {

        $parameter = new \app\common\model\Parameter;

        $parameter_list = $parameter->column('param_value', 'param_name');
//        a($parameter_list);

        $county_name = $parameter_list['system_name'];
        $county_center_zoom = $parameter_list['map_zoom'];


        /*地图数据*/
        $this->assign('county_name', $county_name);
//        $this->assign('county_name', '凉州区');
        $this->assign('county_center_pointx', $parameter_list['center_longitude']);
        $this->assign('county_center_pointy', $parameter_list['center_latitude']);
        $this->assign('county_center_zoom', $county_center_zoom);
        return $this->fetch();
    }

    public function countyvisualmode()
    {

        $cfg = config('jsl.app_name');

        if ($cfg == 'lzq-wenming') {
            $name = "凉州区";
            $point = [102.750198, 37.680445];
            $zoom = 11.55;
            $markers = [
                [
                    'point' => [102.669843, 37.932528],
                    'servivename' => '送温暖志愿服务分队',
                    'servicefunction' => '维护职工群众的经济效益和民主权益，吸引和组织职工群众参加经济建设和改革，努力完成经济和社会发展任务，帮助职工不断提高思想政治觉悟和文化素质，教育职工不断提高思想道德素质和科学文化素养，动员和组织职工积极参加社区建设和管理，为会员活动提供方便、创造条件。',
                ],
                [
                    'point' => [102.597404, 37.914311],
                    'servivename' => '牵手关爱志愿服务分队',
                    'servicefunction' => '引导广大青少年树立正确的世界观、人生观、价值观，树立科学文明健康的生活理念，使广大青年成为推动乡村文化振兴的先锋和重要力量。',
                ],
                [
                    'point' => [102.749181, 37.737366],
                    'servivename' => '家风传习志愿服务分队',
                    'servicefunction' => '注重发挥妇女在社会生活、家庭生活中的独特作用和在弘扬家庭传统美德、树立良好家风方面的独特作用，引导广大妇女成为移风易俗理念的践行者、代言人，树文明新风、扬乡村正气。',
                ],
                [
                    'point' => [102.711237, 37.871026],
                    'servivename' => '教育志愿服务分队',
                    'servicefunction' => '加强师德师风建设，深入实施乡村教师支持计划，大力开展教师培训，提升教师队伍整体水平。统筹用好普通中小学校、职业学校、青少年活动中心、乡村学校少年宫等教育资源，建好教育服务平台，服务未成年人成长成才。做好中小学体育设施对外开放的落实工作。',
                ],
                [
                    'point' => [102.669268, 37.906111],
                    'servivename' => '体育健身志愿服务分队',
                    'servicefunction' => '建好用好基层体育阵地和资源，推动中小学体育设施对外开放，建好新时代文明实践中心体育健身服务平台；推动全民健身活动在农村广泛开展。推进体育社团组织、社会体育指导员向基层延伸，为群众提供科学健身指导服务，有效提高科学健身意识。',
                ],
                [
                    'point' => [102.58533, 37.998528],
                    'servivename' => '民俗文化基地',
                    'servicefunction' => '平岘民俗馆是皋兰县第一座集民俗传统、农耕文化、廉政教育、精神文明展示和党建宣传为一体的民俗展览馆，坐落于皋兰县忠和镇平岘村新农村，建筑面积260平米，拥有2个展厅，其中：一楼展厅主要陈列民国期间、解放初期和改革开放初期皋兰县百姓使用的农具、生活用品等实物及图片；二楼展厅主要展示甘肃近代名人书画。展出著名画家裴广铎大师的百余幅“廉政漫画”，以此反映社会现实问题，针砭时弊警醒世人。',
                ],
                [
                    'point' => [102.573832, 37.924786],
                    'servivename' => '农业科技示范基地',
                    'servicefunction' => '注重实效功能：紧密结合农业生产时节和农民生产需求，开展灵活多样、不同形式的技术培训，使农民一看就懂，一学就会，学了能用，用能致富。典型案例与实践教学相结合，利用各种形式传播农业科技知识。',
                ],
            ];
        }
        if ($cfg == 'jtx-wenming') {
            $name = "景泰县";
            $point = [104.041521, 37.016926];
            $zoom = 11.5;
            $markers = [
                [
                    'point' => [104.086121, 37.196835],
                    'servivename' => '送温暖志愿服务分队',
                    'servicefunction' => '维护职工群众的经济效益和民主权益，吸引和组织职工群众参加经济建设和改革，努力完成经济和社会发展任务，帮助职工不断提高思想政治觉悟和文化素质，教育职工不断提高思想道德素质和科学文化素养，动员和组织职工积极参加社区建设和管理，为会员活动提供方便、创造条件。',
                ],
                [
                    'point' => [103.942967, 37.15681],
                    'servivename' => '牵手关爱志愿服务分队',
                    'servicefunction' => '引导广大青少年树立正确的世界观、人生观、价值观，树立科学文明健康的生活理念，使广大青年成为推动乡村文化振兴的先锋和重要力量。',
                ],
                [
                    'point' => [104.178107, 37.141623],
                    'servivename' => '家风传习志愿服务分队',
                    'servicefunction' => '注重发挥妇女在社会生活、家庭生活中的独特作用和在弘扬家庭传统美德、树立良好家风方面的独特作用，引导广大妇女成为移风易俗理念的践行者、代言人，树文明新风、扬乡村正气。',
                ],
                [
                    'point' => [104.01943, 37.266254],
                    'servivename' => '教育志愿服务分队',
                    'servicefunction' => '加强师德师风建设，深入实施乡村教师支持计划，大力开展教师培训，提升教师队伍整体水平。统筹用好普通中小学校、职业学校、青少年活动中心、乡村学校少年宫等教育资源，建好教育服务平台，服务未成年人成长成才。做好中小学体育设施对外开放的落实工作。',
                ],
                [
                    'point' => [104.061399, 37.188556],
                    'servivename' => '体育健身志愿服务分队',
                    'servicefunction' => '建好用好基层体育阵地和资源，推动中小学体育设施对外开放，建好新时代文明实践中心体育健身服务平台；推动全民健身活动在农村广泛开展。推进体育社团组织、社会体育指导员向基层延伸，为群众提供科学健身指导服务，有效提高科学健身意识。',
                ],
                [
                    'point' => [104.12234, 37.084527],
                    'servivename' => '民俗文化基地',
                    'servicefunction' => '平岘民俗馆是皋兰县第一座集民俗传统、农耕文化、廉政教育、精神文明展示和党建宣传为一体的民俗展览馆，坐落于皋兰县忠和镇平岘村新农村，建筑面积260平米，拥有2个展厅，其中：一楼展厅主要陈列民国期间、解放初期和改革开放初期皋兰县百姓使用的农具、生活用品等实物及图片；二楼展厅主要展示甘肃近代名人书画。展出著名画家裴广铎大师的百余幅“廉政漫画”，以此反映社会现实问题，针砭时弊警醒世人。',
                ],
                [
                    'point' => [103.917095, 37.098344],
                    'servivename' => '农业科技示范基地',
                    'servicefunction' => '注重实效功能：紧密结合农业生产时节和农民生产需求，开展灵活多样、不同形式的技术培训，使农民一看就懂，一学就会，学了能用，用能致富。典型案例与实践教学相结合，利用各种形式传播农业科技知识。',
                ],
            ];
        }
        if ($cfg == 'lintx-wenming') {
            $name = "临潭县";
            $point = [103.520998, 34.700716];
            $zoom = 12;
            $markers = [
                [
                    'point' => [103.360532, 34.707423],
                    'servivename' => '送温暖志愿服务分队',
                    'servicefunction' => '维护职工群众的经济效益和民主权益，吸引和组织职工群众参加经济建设和改革，努力完成经济和社会发展任务，帮助职工不断提高思想政治觉悟和文化素质，教育职工不断提高思想道德素质和科学文化素养，动员和组织职工积极参加社区建设和管理，为会员活动提供方便、创造条件。',
                ],
                [
                    'point' => [103.34271, 34.707423],
                    'servivename' => '牵手关爱志愿服务分队',
                    'servicefunction' => '引导广大青少年树立正确的世界观、人生观、价值观，树立科学文明健康的生活理念，使广大青年成为推动乡村文化振兴的先锋和重要力量。',
                ],
                [
                    'point' => [103.60493, 34.70629],
                    'servivename' => '家风传习志愿服务分队',
                    'servicefunction' => '注重发挥妇女在社会生活、家庭生活中的独特作用和在弘扬家庭传统美德、树立良好家风方面的独特作用，引导广大妇女成为移风易俗理念的践行者、代言人，树文明新风、扬乡村正气。',
                ],
                [
                    'point' => [103.376055, 34.689379],
                    'servivename' => '教育志愿服务分队',
                    'servicefunction' => '加强师德师风建设，深入实施乡村教师支持计划，大力开展教师培训，提升教师队伍整体水平。统筹用好普通中小学校、职业学校、青少年活动中心、乡村学校少年宫等教育资源，建好教育服务平台，服务未成年人成长成才。做好中小学体育设施对外开放的落实工作。',
                ],
                [
                    'point' => [103.683561, 34.632736],
                    'servivename' => '体育健身志愿服务分队',
                    'servicefunction' => '建好用好基层体育阵地和资源，推动中小学体育设施对外开放，建好新时代文明实践中心体育健身服务平台；推动全民健身活动在农村广泛开展。推进体育社团组织、社会体育指导员向基层延伸，为群众提供科学健身指导服务，有效提高科学健身意识。',
                ],
                [
                    'point' => [103.326083, 34.669686],
                    'servivename' => '民俗文化基地',
                    'servicefunction' => '平岘民俗馆是皋兰县第一座集民俗传统、农耕文化、廉政教育、精神文明展示和党建宣传为一体的民俗展览馆，坐落于皋兰县忠和镇平岘村新农村，建筑面积260平米，拥有2个展厅，其中：一楼展厅主要陈列民国期间、解放初期和改革开放初期皋兰县百姓使用的农具、生活用品等实物及图片；二楼展厅主要展示甘肃近代名人书画。展出著名画家裴广铎大师的百余幅“廉政漫画”，以此反映社会现实问题，针砭时弊警醒世人。',
                ],
                [
                    'point' => [103.74975, 34.6433],
                    'servivename' => '农业科技示范基地',
                    'servicefunction' => '注重实效功能：紧密结合农业生产时节和农民生产需求，开展灵活多样、不同形式的技术培训，使农民一看就懂，一学就会，学了能用，用能致富。典型案例与实践教学相结合，利用各种形式传播农业科技知识。',
                ],
            ];
        }
        if ($cfg == 'szq-wenming') {
            $name = "肃州区";
            $point = [98.880132, 39.454048];
            $zoom = 11.62;
            $markers = [
                [
                    'point' => [98.56773, 39.73669],
                    'servivename' => '送温暖志愿服务分队',
                    'servicefunction' => '维护职工群众的经济效益和民主权益，吸引和组织职工群众参加经济建设和改革，努力完成经济和社会发展任务，帮助职工不断提高思想政治觉悟和文化素质，教育职工不断提高思想道德素质和科学文化素养，动员和组织职工积极参加社区建设和管理，为会员活动提供方便、创造条件。',
                ],
                [
                    'point' => [98.511525, 39.683028],
                    'servivename' => '牵手关爱志愿服务分队',
                    'servicefunction' => '引导广大青少年树立正确的世界观、人生观、价值观，树立科学文明健康的生活理念，使广大青年成为推动乡村文化振兴的先锋和重要力量。',
                ],
                [
                    'point' => [98.568233, 39.657759],
                    'servivename' => '家风传习志愿服务分队',
                    'servicefunction' => '注重发挥妇女在社会生活、家庭生活中的独特作用和在弘扬家庭传统美德、树立良好家风方面的独特作用，引导广大妇女成为移风易俗理念的践行者、代言人，树文明新风、扬乡村正气。',
                ],
                [
                    'point' => [98.471113, 39.708955],
                    'servivename' => '教育志愿服务分队',
                    'servicefunction' => '加强师德师风建设，深入实施乡村教师支持计划，大力开展教师培训，提升教师队伍整体水平。统筹用好普通中小学校、职业学校、青少年活动中心、乡村学校少年宫等教育资源，建好教育服务平台，服务未成年人成长成才。做好中小学体育设施对外开放的落实工作。',
                ],
                [
                    'point' => [98.528185, 39.686233],
                    'servivename' => '体育健身志愿服务分队',
                    'servicefunction' => '建好用好基层体育阵地和资源，推动中小学体育设施对外开放，建好新时代文明实践中心体育健身服务平台；推动全民健身活动在农村广泛开展。推进体育社团组织、社会体育指导员向基层延伸，为群众提供科学健身指导服务，有效提高科学健身意识。',
                ],
                [
                    'point' => [98.90131, 39.496492],
                    'servivename' => '民俗文化基地',
                    'servicefunction' => '平岘民俗馆是皋兰县第一座集民俗传统、农耕文化、廉政教育、精神文明展示和党建宣传为一体的民俗展览馆，坐落于皋兰县忠和镇平岘村新农村，建筑面积260平米，拥有2个展厅，其中：一楼展厅主要陈列民国期间、解放初期和改革开放初期皋兰县百姓使用的农具、生活用品等实物及图片；二楼展厅主要展示甘肃近代名人书画。展出著名画家裴广铎大师的百余幅“廉政漫画”，以此反映社会现实问题，针砭时弊警醒世人。',
                ],
                [
                    'point' => [98.991281,39.422565],
                    'servivename' => '农业科技示范基地',
                    'servicefunction' => '注重实效功能：紧密结合农业生产时节和农民生产需求，开展灵活多样、不同形式的技术培训，使农民一看就懂，一学就会，学了能用，用能致富。典型案例与实践教学相结合，利用各种形式传播农业科技知识。',
                ],
            ];
        }
        if ($cfg == 'znx-wenming') {
            $name = "卓尼县";
            $point = [103.293524, 34.545722];
            $zoom = 11.3;
            $markers = [
                [
                    'point' => [103.479117, 34.617498],
                    'servivename' => '送温暖志愿服务分队',
                    'servicefunction' => '维护职工群众的经济效益和民主权益，吸引和组织职工群众参加经济建设和改革，努力完成经济和社会发展任务，帮助职工不断提高思想政治觉悟和文化素质，教育职工不断提高思想道德素质和科学文化素养，动员和组织职工积极参加社区建设和管理，为会员活动提供方便、创造条件。',
                ],
                [
                    'point' => [103.532009, 34.573757],
                    'servivename' => '牵手关爱志愿服务分队',
                    'servicefunction' => '引导广大青少年树立正确的世界观、人生观、价值观，树立科学文明健康的生活理念，使广大青年成为推动乡村文化振兴的先锋和重要力量。',
                ],
                [
                    'point' => [103.634344, 34.557586],
                    'servivename' => '家风传习志愿服务分队',
                    'servicefunction' => '注重发挥妇女在社会生活、家庭生活中的独特作用和在弘扬家庭传统美德、树立良好家风方面的独特作用，引导广大妇女成为移风易俗理念的践行者、代言人，树文明新风、扬乡村正气。',
                ],
                [
                    'point' => [103.488316, 34.552829],
                    'servivename' => '教育志愿服务分队',
                    'servicefunction' => '加强师德师风建设，深入实施乡村教师支持计划，大力开展教师培训，提升教师队伍整体水平。统筹用好普通中小学校、职业学校、青少年活动中心、乡村学校少年宫等教育资源，建好教育服务平台，服务未成年人成长成才。做好中小学体育设施对外开放的落实工作。',
                ],
                [
                    'point' => [103.644693, 34.512861],
                    'servivename' => '体育健身志愿服务分队',
                    'servicefunction' => '建好用好基层体育阵地和资源，推动中小学体育设施对外开放，建好新时代文明实践中心体育健身服务平台；推动全民健身活动在农村广泛开展。推进体育社团组织、社会体育指导员向基层延伸，为群众提供科学健身指导服务，有效提高科学健身意识。',
                ],
                [
                    'point' => [103.45957, 34.529993],
                    'servivename' => '民俗文化基地',
                    'servicefunction' => '平岘民俗馆是皋兰县第一座集民俗传统、农耕文化、廉政教育、精神文明展示和党建宣传为一体的民俗展览馆，坐落于皋兰县忠和镇平岘村新农村，建筑面积260平米，拥有2个展厅，其中：一楼展厅主要陈列民国期间、解放初期和改革开放初期皋兰县百姓使用的农具、生活用品等实物及图片；二楼展厅主要展示甘肃近代名人书画。展出著名画家裴广铎大师的百余幅“廉政漫画”，以此反映社会现实问题，针砭时弊警醒世人。',
                ],
                [
                    'point' => [103.237653, 34.436679],
                    'servivename' => '农业科技示范基地',
                    'servicefunction' => '注重实效功能：紧密结合农业生产时节和农民生产需求，开展灵活多样、不同形式的技术培训，使农民一看就懂，一学就会，学了能用，用能致富。典型案例与实践教学相结合，利用各种形式传播农业科技知识。',
                ],
            ];
        }
        if ($cfg == 'zqx-wenming') {
            $name = "舟曲县";
            $point = [104.430953, 33.489058];
            $zoom = 11.7;
            $markers = [
                [
                    'point' => [104.403932, 33.719018],
                    'servivename' => '送温暖志愿服务分队',
                    'servicefunction' => '维护职工群众的经济效益和民主权益，吸引和组织职工群众参加经济建设和改革，努力完成经济和社会发展任务，帮助职工不断提高思想政治觉悟和文化素质，教育职工不断提高思想道德素质和科学文化素养，动员和组织职工积极参加社区建设和管理，为会员活动提供方便、创造条件。',
                ],
                [
                    'point' => [104.360813, 33.777613],
                    'servivename' => '牵手关爱志愿服务分队',
                    'servicefunction' => '引导广大青少年树立正确的世界观、人生观、价值观，树立科学文明健康的生活理念，使广大青年成为推动乡村文化振兴的先锋和重要力量。',
                ],
                [
                    'point' => [104.305622, 33.72094],
                    'servivename' => '家风传习志愿服务分队',
                    'servicefunction' => '注重发挥妇女在社会生活、家庭生活中的独特作用和在弘扬家庭传统美德、树立良好家风方面的独特作用，引导广大妇女成为移风易俗理念的践行者、代言人，树文明新风、扬乡村正气。',
                ],
                [
                    'point' => [104.367138, 33.742076],
                    'servivename' => '教育志愿服务分队',
                    'servicefunction' => '加强师德师风建设，深入实施乡村教师支持计划，大力开展教师培训，提升教师队伍整体水平。统筹用好普通中小学校、职业学校、青少年活动中心、乡村学校少年宫等教育资源，建好教育服务平台，服务未成年人成长成才。做好中小学体育设施对外开放的落实工作。',
                ],
                [
                    'point' => [104.471772, 33.635859],
                    'servivename' => '体育健身志愿服务分队',
                    'servicefunction' => '建好用好基层体育阵地和资源，推动中小学体育设施对外开放，建好新时代文明实践中心体育健身服务平台；推动全民健身活动在农村广泛开展。推进体育社团组织、社会体育指导员向基层延伸，为群众提供科学健身指导服务，有效提高科学健身意识。',
                ],
                [
                    'point' => [104.368862, 33.738234],
                    'servivename' => '民俗文化基地',
                    'servicefunction' => '平岘民俗馆是皋兰县第一座集民俗传统、农耕文化、廉政教育、精神文明展示和党建宣传为一体的民俗展览馆，坐落于皋兰县忠和镇平岘村新农村，建筑面积260平米，拥有2个展厅，其中：一楼展厅主要陈列民国期间、解放初期和改革开放初期皋兰县百姓使用的农具、生活用品等实物及图片；二楼展厅主要展示甘肃近代名人书画。展出著名画家裴广铎大师的百余幅“廉政漫画”，以此反映社会现实问题，针砭时弊警醒世人。',
                ],
                [
                    'point' => [104.413706, 33.757926],
                    'servivename' => '农业科技示范基地',
                    'servicefunction' => '注重实效功能：紧密结合农业生产时节和农民生产需求，开展灵活多样、不同形式的技术培训，使农民一看就懂，一学就会，学了能用，用能致富。典型案例与实践教学相结合，利用各种形式传播农业科技知识。',
                ],
            ];
        }
        $this->assign('name', $name);
        $this->assign('pointx', $point[0]);
        $this->assign('pointy', $point[1]);
        $this->assign('zoom', $zoom);
        $this->assign('markers', json_encode($markers));
        return $this->fetch();
    }
}

?>