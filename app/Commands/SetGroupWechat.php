<?php

namespace App\Commands;

use App\Commands\Command;
use App\Log\Facades\Logger;
use App\Model\mobile\WechatModel;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Cache;
use Mockery\Exception;

class SetGroupWechat extends Command implements SelfHandling
{
    private $message;
    /**
     * 设置用户分组
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($name, $openid)
    {
        $this->name = $name; //组名称
        $this->openid = $openid;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $group_list = Cache::get("group_list");
            $id = $group_list[$this->name]->id;
            $wechat_m = new WechatModel();
            $wechat_m->move_open_group($id, $this->openid);
        }catch(\Exception $e){
            return false;
        }
        //throw new Exception("微信发送失败");
    }
}
