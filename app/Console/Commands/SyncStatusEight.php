<?php
namespace App\Console\Commands;

use App\Http\Controllers\wx\ContractController;
use App\Log\Facades\Logger;
use Illuminate\Console\Command;


class SyncStatusEight extends Command {


    protected $name = 'sync:statuseight';
    protected $description = '轮循订单状态080合同';


    public function fire() {
        //10分钟请求一次
        //Logger::info(date('Y-m-d H:i:s',time()),'statuseight');
        $contractModel = new ContractController();
        $contractModel->getContractInfo('080');
        $this->comment('running...');
    }

}
