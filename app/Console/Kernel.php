<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\SyncAuditStatus::class,
        \App\Console\Commands\DataMigrate::class,
        \App\Console\Commands\SendMsgToUserWaitSubmit::class,
        \App\Console\Commands\SendEmailCommand::class,
        \App\Console\Commands\SendStatusNullEmail::class,
        \App\Console\Commands\RefundWarn::class,
        \App\Console\Commands\SetUserGroup::class,
        \App\Console\Commands\CountCalculate::class,
        \App\Console\Commands\SyncApiRecordLogCommand::class,
        \App\Console\Commands\CronPerHour::class,
	    \App\Console\Commands\CleanYituCache::class,
        \App\Console\Commands\SyncSendtpl::class,
        \App\Console\Commands\SyncUpdSignStatus::class,
        \App\Console\Commands\SyncStatusTwo::class,
        \App\Console\Commands\SyncStatusEight::class,
        \App\Console\Commands\SyncStatusFive::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
    }
}
