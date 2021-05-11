<?php

namespace app\command;

use think\console\Input;
use think\console\Output;
use think\console\Command;
use think\console\input\Option;
use think\console\input\Argument;

class Test extends Command
{
    protected function configure()
    {
        $this->setName('test')
            ->addArgument('name', Argument::REQUIRED, "your name")
            ->addArgument('age', Argument::OPTIONAL, "your age")
            ->addOption('imagefield', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, 'automatically generate image component with suffix', null)
            // ->addOption('imagefield', null, Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY, 'automatically generate image component with suffix', null)
            ->setDescription('Here is the remark ');
    }

    protected function execute(Input $input, Output $output)
    {
        // dd($input->hasOption('imagefield'));
        $name = trim($input->getArgument('name'));
        $age = trim($input->getArgument('age'));
        $imagefield = $input->getOption('imagefield');  // VALUE_IS_ARRAY：php think test --imagefield=1 --imagefield=22
        $output->writeln("你的名字为:".$name.'你的年龄为：'.$age.'imagefield为'.json_encode($imagefield));
    }
}
