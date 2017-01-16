<?php
namespace App\Console\Commands;


use App\Log\Facades\Logger;
use App\Service\admin\SendService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMsgToUserWaitSubmit extends Command
{
    protected $name = 'sendMsgToUserWailSubmit';

    protected $description = '提醒未提交订单的客户';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function fire(){
        Logger::info('提醒未提交订单的客户');
        $sendService = new SendService();
        $sendService->get_loan_send_msg();
    }

}