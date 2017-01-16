<?php

namespace App\Commands;

use App\Commands\Command;
use App\Log\Facades\Logger;
use App\Service\admin\EmailService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendEmail extends Command implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $message;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $emailService = new EmailService();
        $emailService->send($this->message,'异常邮件');
    }
}
