<?php
namespace App\Console\Commands;

use App\Http\Controllers\wx\ContractController;
use App\Log\Facades\Logger;
use Illuminate\Console\Command;


class SyncSendtpl extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sync:sendtpl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送模板消息';

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    //定时轮循合同信息，给合同成功者发送模板信息
    public function fire() {
        //每分钟请求一次
        //Logger::info(date('Y-m-d H:i:s',time()),'sendtpl');
        $contractModel = new ContractController();
        $contractModel->getContractInfo('070');
        $this->comment('running...');
    }

}
