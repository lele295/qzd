<?php
namespace App\Model\Backend;

use DB;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @author yue.huang01
 */
class ContractInfo extends Model
{

    protected $table = 'contract_info';

    /**
     * 审核通过的合同状态
     */
    public static function getPassContractStatus()
    {
        return [
            '050',
            '020',
            '080'
        ];
    }

    /**
     * 审核已拒绝的合同状态
     */
    public static function getFailContractStatus()
    {
        return [
            '010'
        ];
    }

    /**
     * 审核已取消的合同状态
     */
    public static function getCancelContractStatus()
    {
        return [
            '100'   
        ];
    }

    /**
     * 获取订单通过、拒绝、取消状态的数量
     */
    public static function getRiskControlData($cons)
    {
        $pass_status=self::getPassContractStatus();
        $fail_status=self::getFailContractStatus();
        $cancel_status=self::getCancelContractStatus();
        $res=self::select(DB::raw('count(case when contract_info.status IN(' . implode(",", $pass_status) . ') then contract_info.status else null end) as "pass",
            count(case when contract_info.status IN(' . implode(",", $fail_status) . ') then contract_info.status else null end) as "fail",
            count(case when contract_info.status IN(' . implode(",", $cancel_status) . ') then contract_info.status else null end) as "cancel"'
            ))
        ->join('orders','orders.id','=','contract_info.order_id')
        ->join('sync_store_info','orders.merchant_code','=','sync_store_info.SNO')
        ;
        if (! empty($cons)) {
            $res = $cons['city'] ? $res->where('sync_store_info.CITY',  $cons['city']) : $res;
            $res = $cons['s_date'] ? $res->where('contract_info.create_time', '>', strtotime($cons['s_date'])) : $res;
            $res = $cons['e_date'] ? $res->where('contract_info.create_time', '<', strtotime($cons['e_date'])) : $res;
        }
        return $res->first();
    }
    
    /**
     * 风控数据导出的数据列
     * @param unknown $cons
     */
    public static function getRiskControlDataList($cons)
    {
        $pass_status=self::getPassContractStatus();
        $fail_status=self::getFailContractStatus();
        $cancel_status=self::getCancelContractStatus();
        $res=self::select(DB::raw('count(case when contract_info.status IN(' . implode(",", $pass_status) . ') then contract_info.status else null end) as "pass",
            count(case when contract_info.status IN(' . implode(",", $fail_status) . ') then contract_info.status else null end) as "fail",
            count(case when contract_info.status IN(' . implode(",", $cancel_status) . ') then contract_info.status else null end) as "cancel"'
            ))
        ->join('orders','orders.id','=','contract_info.order_id')
        ;
        
    }
}