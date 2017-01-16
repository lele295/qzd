<?php
namespace App\Model\mobile;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class WechatModel extends Model{

    protected $table = 'wechat';

    public function __construct()
    {
    }

    //获取access_token信息
    public function getInfo(){

        $data = DB::table($this->table)->first();
        return $data;
    }

    //更新access_token信息
    public function update_access_token($token,$time){

        $data = DB::table($this->table)->where('id',1)
            ->update(['access_token' => $token,'access_time'=>$time]);

        //dd($data);
        return $data;
    }

    //根据access_token更新ticket信息
    public function update_ticket($access_token,$ticket,$tiket_time){
        $data = DB::table($this->table)->where('access_token',$access_token)
            ->update(['ticket' => $ticket,'ticket_time'=>$tiket_time]);

        //dd($data);
        return $data;
    }


    public static function getWxversion(){

        if(!isset($_SERVER['HTTP_USER_AGENT'])){
            return false;
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            $wwstr = $_SERVER['HTTP_USER_AGENT'];
            $rule = "/MicroMessenger\/([0-9]\.[0-9])/";
            preg_match($rule,$wwstr, $result);
            if($result[1] > 5.0){
                return true;
            }
        }
        return false;
    }

}
