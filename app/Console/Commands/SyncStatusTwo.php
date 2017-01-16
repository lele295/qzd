<?php
namespace App\Console\Commands;

use App\Http\Controllers\wx\ContractController;
use App\Log\Facades\Logger;
use Illuminate\Console\Command;


class SyncStatusTwo extends Command {


    protected $name = 'sync:statustwo';
    protected $description = '轮循订单状态020的合同';

    public function fire() {
        //1小时请求一次
        //Logger::info(date('Y-m-d H:i:s',time()),'statustwo');
        $contractModel = new ContractController();
        $contractModel->getContractInfo('020');
        $this->comment('running...');
    }

}
