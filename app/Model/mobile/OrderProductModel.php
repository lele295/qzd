<?php

namespace App\Model\mobile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderProductModel extends Model
{
    protected $table = 'orders_product';

    //获取商户相关贷款信息
    public function get_user_pro_info($id)
    {
        $data = DB::table($this->table)->where('id',$id)->first();
        return $data;
    }
}
