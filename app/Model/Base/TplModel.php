<?php

namespace App\Model\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 推送模板消息信息获取
 * Class TplModel
 * @package App\Model\Base
 */
class TplModel extends Model
{

    /**
     * 获取openid
     * @param $contract_no
     * @return mixed
     */
    public static function get_openid_by_contract($contract_no){
        $data = DB::table('contract_info')->where('contract_no',$contract_no)
            ->select('openid','audit_time','order_id')
            ->first();

        return $data;
    }

    /**
     * 申请成功后的模板信息,时间，产品（获取最新的合同信息,发送最新的模板信息）
     * @param $openid
     * @return mixed
     */
    public static function get_send_success_tpl_info($openid)
    {
        $data = DB::table('users')->where('openid',$openid)
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->join('orders_product', 'orders_product.id', '=', 'orders.product_id')
            ->select('order_create_time', 'service_type')
            ->orderBy('orders_product.id','desc')
            ->first();
        return $data;
    }

    /**
     * 审批成功，审批拒绝后模板信息，金额，期限，审批时间
     * @param $contract_no
     * @return mixed
     */
    public static function get_send_exam_tpl_info($contract_no)
    {
        $res = self::get_openid_by_contract($contract_no);
        $data = DB::table('orders')->where('orders.id',$res->order_id)
            ->join('orders_product', 'orders_product.id', '=', 'orders.product_id')
            ->first();

        $data->audit_time = $res->audit_time;
        return $data;
    }

}
