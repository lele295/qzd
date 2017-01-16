<?php
namespace App\Model\Backend;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Model\Base\SyncCodeLibrary;

/**
 * Description of BackUser
 *
 * @author
 *
 */
class OrdersProduct extends Model
{

    protected $table = 'orders_product';

    /**
     * 销售业绩统计
     * 根据条件查询总销售额及订单数量
     * *@param unknown $cons
     */
    public static function getMarketPerformance($cons)
    {
        $contract_status=ContractInfo::getPassContractStatus();
        
        $res = self::select( DB::raw('IFNULL(SUM(`orders_product`.`loan_money`),0) as sum'),DB::raw('COUNT(`orders_product`.`id`) as count'))
        ->join('orders','orders_product.id','=','orders.product_id')
        ->join('contract_info','contract_info.order_id','=','orders.id')
        ->whereIn('contract_info.status',$contract_status);
        if (! empty($cons)) {
            $res = $cons['s_date'] ? $res->where('contract_info.create_time', '>', strtotime($cons['s_date'])) : $res;
            $res = $cons['e_date'] ? $res->where('contract_info.create_time', '<', strtotime($cons['e_date'])) : $res;
        }
        return $res->first();
    }
    
    /**
     * 销售业绩统计导出的信息
     * @param unknown $cons
     */
    public static function getMarketPerformanceList($cons)
    {
        $contract_status=ContractInfo::getPassContractStatus();
        
        $res = self::select(DB::raw('IFNULL(SUM(`orders_product`.`loan_money`),0) as sum'),DB::raw('COUNT(`orders_product`.`id`) as count'))
        ->join('orders','orders_product.id','=','orders.product_id')
        ->join('contract_info','contract_info.order_id','=','orders.id');
//         ->whereIn('contract_info.status',$contract_status);
            $res = $res->where('contract_info.create_time', '>', strtotime($cons['s_date'])) ;
            $res = $res->where('contract_info.create_time', '<', strtotime($cons['e_date']));
        return $res->get();
        
    }
}