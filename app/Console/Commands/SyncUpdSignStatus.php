<?php
namespace App\Console\Commands;

use App\Log\Facades\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


//测试状态更新
class SyncUpdSignStatus extends Command {


    protected $name = 'sync:updsignstatus';
    protected $description = '更新签署状态';

    //签署状态如果，2天未签署，更改状态以取消(080->100)
    public function fire() {
        //1小时请求一次
        //Logger::info(date('Y-m-d H:i:s',time()),'upd');
        $data = DB::table('contract_info')->select('create_time','status','contract_no')
            ->where('status','080')
            ->get();

        foreach($data as $v){
            if(time()-$v->create_time > 2*24*3600){
                $rs = DB::table('contract_info')->where('contract_no',$v->contract_no)
                    ->update(['status' => '100']);
            }
        }

        $this->comment('running...');

    }

}
