<?php
namespace App\Console\Commands;

use App\Http\Controllers\wx\ContractController;
use App\Log\Facades\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefundWarn extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'refund:warn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '提前三天还款提醒';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        //每小时执行一次定时任务
        //Logger::info(date('Y-m-d H:i:s',time()),'refundWarn');
        $warn_date = date('d');  //提醒日期 例如返回15(号)
        $contract_res = DB::table('contract_info')->where(['status'=>'050','monthly_repay_date'=>$warn_date+3])->get();
        if($contract_res){
            foreach($contract_res as $k=>$v){

            }
        }
        //$this->comment('running...');
    }

}
