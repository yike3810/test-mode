<?php
namespace app\command\controller;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class VideoReview extends Command
{
    protected function configure()
    {
        $this->setName('videoreview')->setDescription('Command Test');
    }
    
    protected function execute(Input $input, Output $output)
    {
    	$news = new \app\common\model\News;
    	$result = $news->moderationVideo();
        $output->writeln("VideoReviewCommandStart:".date("Y-m-d H:i:s"));
        foreach ($result as $k=>$v){
        	$output->writeln("news_id:{$v['news_id']}...{$v['status']}");
        }
        $output->writeln("VideoReviewCommandEnd:".date("Y-m-d H:i:s"));
    }
}
