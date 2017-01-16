<?php
namespace App\Model\mobile;
use App\Log\Facades\Logger;
use App\Model\Base\SyncCodeLibraryModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankCodeLikeModel extends Model{
    const table = "bank_code_like";
    protected $talbe = "bank_code_like";
    /*
     * 检验银行卡前缀信息
     * $ReplaceAccount 银行卡号
     */
    static function like_bank_prefix($ReplaceAccount, $bank_code){
        $number = substr($ReplaceAccount,0,3);
        $number1 = substr($ReplaceAccount,0,4);
        $number2 = substr($ReplaceAccount,0,5);
        $number3 = substr($ReplaceAccount,0,6);
        $number4 = substr($ReplaceAccount,0,7);
        $number5 = substr($ReplaceAccount,0,8);
        $number6 = substr($ReplaceAccount,0,9);

        $res = DB::table("bank_code_like")
        ->where("bank_mark", "like", $number."%")
        ->select("type", "bank_mark", "bank_code")->get();
        if(!$res){
            $info_txt= "卡号:".$ReplaceAccount."未查找到，但通过";
            Logger::info($info_txt);
            return array("status"=>true, "data"=>"未查找到，但通过");
        }else{
            $res_arr = array();
            foreach($res as $val){
                if($number == $val->bank_mark || $number1 == $val->bank_mark || $number2 == $val->bank_mark || $number3 == $val->bank_mark || $number4 == $val->bank_mark || $number5 == $val->bank_mark || $number6 == $val->bank_mark){
                    $res_arr[] = array("status"=>true, "type"=>$val->type, "bank_code"=>$val->bank_code, "number"=>$val->bank_mark);
                }
            }
            if($res_arr){
                $pass_arr = array("status"=>false, "data"=>"银行账号与开户行不匹配\n请更改账号或开户行");


                foreach($res_arr as $val){
                    if($val["bank_code"] == $bank_code){
                        if($val["type"]==2){
                            $pass_arr = array("status" => false, "data" => "请填写储蓄卡账号");
                        }else {
                            $pass_arr = array("status" => true, "data" => "通过");
                        }
                    }
                }
                $info_txt= "卡号:".$ReplaceAccount."初步检验结果";
                Logger::info($info_txt);
                Logger::info($pass_arr);
                return $pass_arr;
            }else{
                $info_txt= "卡号:".$ReplaceAccount."无该卡号对应的开户行";
                Logger::info($info_txt);
                return array("status"=>true, "data"=>"无该卡号对应的开户行");
            }
        }
    }

    /*
     * 添加银行卡校验
     */
    static public function add_bank_code($data){
        $affect = DB::table(self::table)->insert($data);
        return $affect;
    }

    /*
     * 检验是否存在
     * $bank_code 银行代码
     * $bank_mark 号码前缀
     */
    static public function verify_bank_like($bank_code, $bank_mark){
        $res = DB::table(self::table)
            ->where("bank_mark", $bank_mark)
            ->where("bank_code", $bank_code)
            ->first();
        return $res;
    }

    /*
     * 获取所有的bank_code_like表并分页
     */
    static public function get_all_page(){
        $res = DB::table(self::table)->orderBy("id", "desc")->paginate(15);
        return $res;
    }

    /*
     * 根据id删除信息
     */
    static public function del_id($id){
        $affect = DB::table(self::table)->where("id", $id)->delete();
        return $affect;
    }
}
