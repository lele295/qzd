<?php
namespace App\Console\Commands;

use App\Service\statistical\TelesaleStatistical;
use App\Service\statistical\TelStatistical;
use Illuminate\Console\Command;

class CronPerHour extends  Command{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sync:cronperhour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '系统每小时的定时任务';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
       TelStatistical::telbookCalc();//紧急性不高
       TelesaleStatistical::calc();//紧急性也不高
    }

}