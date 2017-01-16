<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/1/18
 * Time: 16:57
 */

namespace App\Commands;


use App\Http\Service\queue\WechatQueue;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WechatMessage extends Command implements SelfHandling, ShouldQueue{
    use InteractsWithQueue, SerializesModels;

    private $openid;
    private $access_token;
    public function __construct($access_token,$openid){
        $this->openid = $openid;
        $this->access_token = $access_token;
    }


    public function handle(){
        $wechatQueue = new WechatQueue();
        $wechatQueue->get_wechat_message($this->access_token,$this->openid);
    }
}