<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/16
 * Time: 11:26
 */

namespace App\Console\Commands;


use App\Log\Facades\Logger;
use App\Service\admin\EmailService;
use Illuminate\Console\Command;

class SendEmailCommand extends Command
{
    protected $name = 'command:sendmail';

    protected $description = '发送同步数据结果邮件';

    public function fire(){
        Logger::info('发送同步数据结果邮件开始');
        $emailService = new EmailService();
        $emailService->send_every_day_sync_data_to_admin();
        $this->info('发送同步数据结果邮件结束');
    }
}