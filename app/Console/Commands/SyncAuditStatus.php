<?php
namespace App\Console\Commands;


use App\Log\Facades\Logger;
use App\Model\Base\LoanModel;

use App\Service\base\Order;

use Illuminate\Console\Command;
use App\Model\Base\AuthModel;

class SyncAuditStatus extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sync:auditstatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步审核订单状态';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        $data = LoanModel::get_status_ascode(array('070','0701'));
        if ($data) {
            foreach ($data as $val) {
                Order::updateStatus($val->id);
            }
        }else{
            Logger::info("没有审核订单需要同步订单状态");
        }
        //AuthModel::update_loan_auth_step_status();
        Logger::info("同步审核订单更新完成");
        $this->error('同步审核订单状态');
    }

}
