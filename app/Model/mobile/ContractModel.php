<?php

namespace App\Model\mobile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContractModel extends Model
{

    protected $table = 'contract_info';

    /**
     * @param $order_id
     * @return mixed
     */
    public function get_contract_info_by_order($order_id)
    {
        $data = DB::table($this->table)->where('order_id',$order_id)->first();
        return $data;
    }

    /**
     * 指定合同状态的合同信息，并用于轮循进行发模板消息
     * @param $status
     * @return mixed
     */
    public function get_contract_info($status)
    {
        $data = DB::table($this->table)->where('status','=',$status)->get();
        return $data;
    }


    /**
     * 更新合同为指定状态
     * @param $contract_no
     * @param $status
     */
    public function update_contract_info_by_id($contract_no,$status)
    {
        $data = DB::table($this->table)->where('contract_no',$contract_no)->where('contract_no','<>','070')
            ->update(['status'=>$status]);
        //dd($data);
    }

    /**
     * 根据合同号获取合同信息
     * @param $contract_no
     * @return mixed
     */
    public function get_contract_info_by_id($contract_no)
    {
        $data = DB::table($this->table)->where('contract_no',$contract_no)
            ->first();
        return $data;
    }

    /**
     * 根据order_id更新合同状态
     * @param $order_id
     * @param $status
     */
    public function update_contract_info_by_order_id($order_id,$status)
    {
        $data = DB::table($this->table)->where(['order_id'=>$order_id, 'status'=>'080'])
            ->update(['status'=>$status]);
        //dd($data);
    }

    /**
     * 指定时间的合同信息
     */
    public function get_contract_info_by_date($startTime,$endTime){
        $data = DB::table('contract_info')
            ->orderBy('id','ASC')->where('create_time','>=',$startTime)->where('create_time','<=',$endTime)
            ->select('openid')
            ->distinct('openid')
            ->get();
        return $data;
    }


    /**
     * 循环查询数据，每次返回100条数据
     * @param $i
     * @return mixed
     */
    public function get_onehundred_contract_info($i,$status){

        $data = DB::table('contract_info')->skip(100*($i-1)+100)->take(100)
            ->where('status','=',$status)
            ->get();

        return $data;
    }

}
