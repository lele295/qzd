<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/2/3
 * Time: 16:47
 */

namespace App\Console\Commands;


use App\Log\Facades\Logger;
use App\Service\admin\EmailService;
use Illuminate\Console\Command;

class SendStatusNullEmail extends Command
{
    protected $name = 'command:sendmail_status_null';

    protected $description = '查询合同状态结果为空的合同';

    public function fire(){
        Logger::info('查询合同状态结果为空的合同');
        $emailService = new EmailService();
        $emailService->send_status_null_email();
    }
}