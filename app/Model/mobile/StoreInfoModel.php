<?php

namespace App\Model\mobile;

use App\Http\Controllers\wx\SignController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StoreInfoModel extends Model
{
    protected $table = 'sync_store_info';

    //获取门店，销售点相关信息
    public function get_store_info_by_order_id($order_id)
    {
        $data = DB::table('orders')->where('orders.id', $order_id)
            //->whereNull('b.STYPE')
            ->leftjoin('sync_store_info as a', 'orders.merchant_code', '=', 'a.SNO')
            ->leftjoin('sync_storerelativesalesman as b', 'a.SNO', '=', 'b.SNO')
            ->leftjoin('sync_code_library as c', 'a.CITY', '=', 'c.ITEMNO')
            ->select('b.SALESMANNO', 'a.SERIALNO', 'a.SNO', 'a.SNAME', 'a.ADDRESS', 'orders.merchant_code', 'c.ITEMNAME')
            //->tosql();
            ->first();

        //dd($data);
        return $data;
    }


    //通过order_id号查询费率
    public function get_rate_info_by_order_id($order_id,$loan_money,$periods)
    {
        $data = DB::table('orders')->where('orders.id', '=', $order_id)
            ->where('c.LOWPRINCIPAL','<=',$loan_money)
            ->where('c.TALLPRINCIPAL','>=',$loan_money)
            ->where('c.TERM','=',$periods)
            ->leftjoin('sync_storerelativeproduct as a','a.SNO','=','orders.merchant_code')
            ->leftjoin('sync_product_businesstype as b','a.PNO','=','b.PRODUCTSERIESID')
            ->leftjoin('sync_business_type as c','b.BUSTYPEID','=','c.TYPENO')
            ->select('c.MONTHLYINTERESTRATE','c.MANAGEMENTFEESRATE','c.CUSTOMERSERVICERATES','b.BUSTYPEID')
            //->tosql();
            ->first();
        //dd($data);
        return $data;
    }

}
