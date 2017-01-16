<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/2
 * Time: 14:31
 */

namespace App\Commands;


use App\Service\mobile\CenterService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCancelWechat extends Command implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $loan_id;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($loan_id)
    {
        $this->loan_id = $loan_id;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $centerService = new CenterService();
        $centerService->send_weixin_message($this->loan_id);
    }
}
