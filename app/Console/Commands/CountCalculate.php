<?php


namespace App\Console\Commands;


use App\Service\admin\OtherCountService;
use Illuminate\Console\Command;

class CountCalculate extends Command{

    protected $name = 'sync:count_calculate';

    protected $description = '对日志中的数据进行同步操作';

    public function fire(){
        $otherCountService = new OtherCountService();
        $otherCountService->count_calculate();
    }

}