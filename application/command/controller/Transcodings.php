<?php
namespace app\command\controller;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Transcodings extends Command
{
    protected function configure()
    {
        $this->setName('transcodings')->setDescription('Command Test');
    }
    
    protected function execute(Input $input, Output $output)
    {
    	$news = new \app\common\model\News;
    	$result = $news->transcodings();
        $output->writeln("TranscodingsCommandStart:".date("Y-m-d H:i:s"));
        foreach ($result as $k=>$v){
        	$output->writeln("news_id:{$v['news_id']}...{$v['status']}");
        }
        $output->writeln("TranscodingsCommandEnd:".date("Y-m-d H:i:s"));
    }
}
