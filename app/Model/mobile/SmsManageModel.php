<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/7/11
 * Time: 9:54
 */
namespace App\Model\mobile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SmsManageModel extends Model{

    protected $table = 'sms_manage';

    public function insert_sms_manage($data){
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $id = DB::table($this->table)->insertGetId($data);
        return $id;
    }

    public function get_sms_list($condition){
        if(empty($condition)){

        }else{

        }
    }
}