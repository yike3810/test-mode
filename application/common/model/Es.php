<?php
namespace app\common\model;
use Elasticsearch\ClientBuilder;
class Es extends \think\Model {
    public $client;
	public function __construct($data = [])
    {
        $caBundle = \Composer\CaBundle\CaBundle::getBundledCaBundlePath();
        $this->client = ClientBuilder::create()->setHosts(["https://admin:Gsda1ly@123@192.168.1.101:9200"])->setSSLVerification($caBundle)->build();
        //$this->client = ClientBuilder::create()->setHosts(["127.0.0.1:9200"])->setSSLVerification($caBundle)->build();
    }
    public function qqq(){
        $params = [
            'body' => [
                'id' => $row['news_id'],
                'title' => $row['title'],
                'content' => $content_info
            ],
            'id' => 'article_' . $row['news_id'],
            'index' => 'articles_index',
            'type' => 'articles_type'
        ];
        $this->client->index($params);
    }
    /* author@zhou
     * 功能：查询条件
     * return
     */
    public function search($params_array){
        $params = [
            'index' => 'articles_index',
            'type' => 'articles_type',
            'from' => $params_array['start_page'],
            'size' => $params_array['list_rows'] ==""?15:$params_array['list_rows'],
        ];

        //多字段匹配
//        $params['body']['query']['multi_match']['query'] = '我的宝马发动机多少';
//        $params['body']['query']['multi_match']['fields'] = ["title","content"];
//        $params['body']['query']['multi_match']['type'] ="most_fields"; // most_fields 多字段匹配度更高   best_fields  完全匹配占比更高
//
//        //单个字段匹配
//        $params['body']['query']['match']['content'] =  '我的宝马多少马力';

        //完全匹配
//        $params['body']['query']['match_phrase']['content'] =  '我的宝马多少马力';


        //联合搜索  must,should,must_not
//        $params['body']['query']["bool"]['must']["match"]['content'] = "秦安县";
//        $params['body']['query']["bool"]['must']["match"]['title'] = "习近平";
        $params['body']['query']['bool']['must']  = array();
        if($params_array['keywords']!=""){
            array_push( $params['body']['query']['bool']['must'],
                array('match_phrase' => array('content_str' => $params_array['keywords']))
            );
        }
        if($params_array['subject_id']!=""){
            array_push( $params['body']['query']['bool']['must'],
                array('term' => array('subject_id' => $params_array['subject_id']))
            );
        }
        if($params_array['publish_start_time']!="" ||$params_array['publish_end_time']!=""  ){
            $params['body']['query']['bool']['filter']['range'] = array(
                'publish_time.keyword' => array(
                    'gte'           => $params_array['publish_start_time'],
                    'lte'           => $params_array['publish_end_time'],
                    'format'        => "yyyy-MM-dd HH:mm:ss",
                ),
            );
        }
        $params['body']['_source'] = array(
            "excludes"=>["content_str"]
        );
        $res = $this->client->search($params);
        return $res;
    }
}
?>