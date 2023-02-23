<?php
namespace app\command\controller;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class AutoShipping extends Command
{
    protected function configure()
    {
        $this->setName('autoshipping')->setDescription('Command Test');
    }
    
    protected function execute(Input $input, Output $output)
    {
        $order = new \app\common\model\Order;
        $result = $order->AutoShipping();
        $output->writeln("AutoShippingStart:".date("Y-m-d H:i:s"));
        $output->writeln("AutoShippingEnd:".date("Y-m-d H:i:s"));
    }
}
