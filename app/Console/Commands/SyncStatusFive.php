<?php
namespace App\Console\Commands;

use App\Http\Controllers\wx\ContractController;
use App\Log\Facades\Logger;
use Illuminate\Console\Command;


class SyncStatusFive extends Command {


    protected $name = 'sync:statusfive';
    protected $description = '轮循订单状态050合同';

    public function fire() {
        //每天晚上十二点执行
        Logger::info(date('Y-m-d H:i:s',time()),'statusfive');
        $contractModel = new ContractController();
        $contractModel->getStatusInfo('050');
        $this->comment('running...');
    }

}
