<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/14
 * Time: 10:58
 */

namespace App\Console\Commands;


use App\Service\admin\Log\LogService;
use Illuminate\Console\Command;

class SyncApiRecordLogCommand extends Command
{
    protected $name = 'sync:api_record_log';

    protected $description = '同步相关API的日志记录';

    public function fire() {
        $logService = new LogService();
        $logService->get_api_record_log_file();
    }
}