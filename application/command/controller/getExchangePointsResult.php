<?php
namespace app\command\controller;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class getExchangePointsResult extends Command
{
    protected function configure()
    {
        $this->setName('getexchangepointsresult')->setDescription('Command Test');
    }
    
    protected function execute(Input $input, Output $output)
    {
        $order = new \app\common\model\Order;
        $result = $order->getExchangePointsResult();
        $output->writeln("getExchangePointsResultStart:".date("Y-m-d H:i:s"));
        $output->writeln("getExchangePointsResultEnd:".date("Y-m-d H:i:s"));
    }
}
