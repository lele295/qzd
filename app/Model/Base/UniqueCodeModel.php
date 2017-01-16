<?php
namespace App\Model\Base;

use App\Log\Facades\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Util\Sms;

class UniqueCodeModel extends Model{

    protected $table = "unique_code";

    public function __construct(){

    }

    //随机码生成
    public function randcode($mobile, $user_id = -1){
        $pool='0123456789';
        $rand_key='';
        for($i = 0;$i < 6;$i++){
            $rand_key.=substr($pool, mt_rand(0,  strlen($pool)-1),1);
        }
        return $rand_key;
    }


    //检查短信验证码是否正确
    public function check_yzm($mobile, $code, $user_id = -1){
        $user = DB::table('unique_code')
            ->where('mobile',$mobile)
            ->where('code',$code)
            ->where('is_enabled',1)
            ->where('user_id', $user_id)
            ->first();
        if($user){
            $this->update_code($mobile, $user_id);
            return true;
        }  else {
            return false;
        }
    }

    //更新过期的验证码
    public function update_code($mobile, $user_id)
    {
        DB::table('unique_code')
            ->where('mobile', $mobile)
            ->where('user_id', $user_id)
            ->update(array('is_enabled' => 0));
    }


    /*
     * 选择发送供应商(短信没有入库，与借钱么不同)
     */
    public function select_send_supply($content, $mobile){

        $sms = new Sms();
        $res = $sms->send_tiaozhan_sms($content ,$mobile);

        if($res){
            return array('status'=>true,'data'=>array('message'=>'短信发送成功'));
        }else{
            return array('status'=>false,'data'=>array('message'=>'短信发送失败，请重试"'));
        }
    }

}