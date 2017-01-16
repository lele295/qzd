<?php
namespace App\Console\Commands;


use App\Http\Controllers\admin\SetWechatController;
use App\Log\Facades\Logger;
use App\Service\admin\LoanService;
use App\Service\base\OnceService;
use Illuminate\Console\Command;


class SetUserGroup extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'set:user_group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置以前用户分组（公用）';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
     //   $setwechat = new SetWechatController();
     //   $setwechat->getBatchReplaceDeal();
     //   $this->error('同步完成');
    //    $loanService = new LoanService();
   //     $loanService->set_user_group();
        Logger::info('进行用户分组');
        $onceService = new OnceService();
        $onceService->set_user_group();
        $this->error('用户分组同步完成');
    }

}
