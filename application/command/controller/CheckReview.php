<?php
namespace app\command\controller;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class CheckReview extends Command
{
    protected function configure()
    {
        $this->setName('checkreview')->setDescription('Command Test');
    }
    
    protected function execute(Input $input, Output $output)
    {
    	$news = new \app\common\model\News;
    	$result = $news->checkReview();
        $output->writeln("checkReviewCommandStart:".date("Y-m-d H:i:s"));
        foreach ($result as $k=>$v){
        	$output->writeln("news_id:{$v['news_id']}...{$v['status']}");
        }
        $output->writeln("checkReviewCommandEnd:".date("Y-m-d H:i:s"));
    }
}
