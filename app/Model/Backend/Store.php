<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Service\Help;

class Store extends Model
{

    protected $table = 'sync_store_info';

    public $timestamps = true;

    protected $guarded = [];

    /**
     * 查询门店列表
     * 
     * @param unknown $cons            
     * @return string
     */
    public static function getStoreInfo($cons)
    {
        $list = self::join('sync_retail_info', 'sync_retail_info.SERIALNO', '=', 'sync_store_info.RSERIALNO')->select('sync_retail_info.SERIALNO as RID', // 商户ID
'sync_store_info.SERIALNO as SID', // 门店ID
'sync_retail_info.RNO', // 商户代码
'sync_retail_info.RNAME', // 商户名称
'sync_store_info.SNO', // 门店代码
'sync_store_info.SNAME', // 门店名称
'sync_store_info.STATUS', // 门店状态
'sync_store_info.SALESMANAGER', // 销售经理
'sync_store_info.CITYMANAGER', // 区域总监
'sync_retail_info.CITY')
            -> // 城市
where('sync_store_info.SNO', '<>', '')
            ->where('sync_retail_info.RNO', '<>', '');
        if ($cons != '') {
            $list = $cons['merchantName'] ? $list->where('sync_retail_info.RNAME', 'like', "%" . $cons['merchantName'] . "%") : $list;
            $list = $cons['merchantCode'] ? $list->where('sync_retail_info.RNO', 'like', "%" . $cons['merchantCode'] . "%") : $list;
            $list = $cons['storeName'] ? $list->where('sync_store_info.SNAME', 'like', "%" . $cons['storeName'] . "%") : $list;
            $list = $cons['storeCode'] ? $list->where('sync_store_info.SNO', 'like', "%" . $cons['storeCode'] . "%") : $list;
            $list = $cons['storeStatus'] ? $list->where('sync_store_info.STATUS', $cons['storeStatus']) : $list;
            $list = isset($cons['storeCity']) && $cons['storeCity'] != '' ? $list->where('sync_store_info.CITY', $cons['storeCity']) : $list;
            
            // 查询销售经理
            if ($cons['salesManager'] != '') {
                $res = UserInfo::getUserIDByName($cons['salesManager']);
                $user_id_list = [];
                foreach ($res as $v) {
                    $user_id_list[] = $v->USERID;
                }
                $list->whereIn('sync_store_info.SALESMANAGER', $user_id_list);
            }
            
            // 查询区域总监
            if ($cons['cityManager'] != '') {
                $res = UserInfo::getUserIDByName($cons['cityManager']);
                $user_id_list = [];
                foreach ($res as $v) {
                    $user_id_list[] = $v->USERID;
                }
                $list->whereIn('sync_store_info.CITYMANAGER', $user_id_list);
            }
        }
        $list = $list->orderBy('sync_store_info.SNO', 'DESC')->Paginate(10);
        
        if ($list) {
            $sno_array = [];
            $saleman_manager_id_array = [];
            // 获取门店id列表及销售经理id列表
            foreach ($list as $key => $val) {
                $sno_array[] = $val->SNO;
                $saleman_manager_id_array[] = $val->SALESMANAGER;
            }
            // 去除重复id
            $sno_array = array_unique($sno_array);
            $saleman_manager_id_array = array_unique($saleman_manager_id_array);
            
            $salesman_list = self::getSalesManList($sno_array); // 销售列表
            $regional_manager_list = UserInfo::getSuperiorListByIDList($saleman_manager_id_array)->toArray(); // 区域经理列表
            $regional_manager_list = Help::fixArray($regional_manager_list, 'USERID'); // 整理数组
            foreach ($list as $k => $v) {
                isset($salesman_list[$v->SNO]) ? $list[$k]['SALESMAN'] = $salesman_list[$v->SNO] : $list[$k]['SALESMAN'] = '';
                isset($regional_manager_list[$v->SALESMANAGER]['SUPERID']) ? $list[$k]['REGIONALMANAGER'] = $regional_manager_list[$v->SALESMANAGER]['SUPERID'] : $list[$k]['REGIONALMANAGER'] = '';
            }
        }
        return $list;
    }

    /**
     * 查询门店对应销售人员
     */
    public static function getSalesManList($sno_array)
    {
        $sales_list = [];
        $list = DB::table('sync_storerelativesalesman')->select(DB::raw('group_concat(SALESMANNAME) as sales'), 'SNO')
            ->whereIn('SNO', $sno_array)
            ->groupBy('SNO')
            ->get();
        
        foreach ($list as $key => $val) {
            $sales_list[$val->SNO] = $val->sales;
        }
        return $sales_list;
    }

    /**
     * 查询某个门店所绑定的产品列表
     * 
     * @param unknown $serial_no            
     */
    public static function getProduct($sno)
    {
        $res = DB::table('sync_storerelativeproduct')->select(DB::raw('group_concat(PNAME) as PNAME'), 'SNO')
            ->where('SNO', $sno)
            ->groupBy('SNO')
            ->get();
        return $res;
    }

    /**
     * 批量查询门店对应绑定的产品列表
     */
    public static function getProductBySnoList($sno_array)
    {
        $product_list = [];
        $list = DB::table('sync_storerelativeproduct')->select(DB::raw('group_concat(PNAME) as PNAME'), 'SNO')
            ->whereIn('SNO', $sno_array)
            ->groupBy('SNO')
            ->get();
        if ($list) {
            foreach ($list as $key => $val) {
                $product_list[$val->SNO] = $val->PNAME;
            }
        }
        return $product_list;
    }

    /**
     * 查询门店基本信息
     */
    public static function getStoreBaseInfo($serial_no)
    {
        $res = self::select('SNO', 'SNAME')->where('SERIALNO', $serial_no)->first();
        return $res;
    }

    /**
     * 获取城市排行
     */
    public static function getCityRank($cons, $limit = '')
    {
        $res = self::join('orders', 'sync_store_info.SNO', '=', 'orders.merchant_code')->join('orders_product', 'orders.product_id', '=', 'orders_product.id')->select('sync_store_info.CITY as city', DB::raw('IFNULL(SUM(`orders_product`.`loan_money`),0) as sum'));
        if (! empty($cons)) {
            $res = $cons['s_date'] ? $res->where('orders.order_create_time', '>', strtotime($cons['s_date'])) : $res;
            $res = $cons['e_date'] ? $res->where('orders.order_create_time', '<', strtotime($cons['e_date'])) : $res;
        }
        if ($limit != '') {
            $res->limit($limit);
        }
        return $res->groupBy('sync_store_info.CITY')
            ->orderBy('sum', 'DESC')
            ->get();
    }

    /**
     * 获取区域排行（返回区域总监姓名及销售额）
     */
    public static function getAreaRank($cons, $limit = '')
    {
        $res = self::join('orders', 'sync_store_info.SNO', '=', 'orders.merchant_code')->join('orders_product', 'orders.product_id', '=', 'orders_product.id')
            ->
        // ->join('contract_info','contract_info.order_id','=','orders.id')
        // ->select('contract_info.city_manager_no as manager_no',DB::raw('IFNULL(SUM(`orders_product`.`loan_money`),0) as sum'));
        join('sync_user_info', 'sync_store_info.CITYMANAGER', '=', 'USERID')
            ->select('sync_user_info.USERNAME as manager_name', DB::raw('IFNULL(SUM(`orders_product`.`loan_money`),0) as sum'));
        if (! empty($cons)) {
            $res = $cons['s_date'] ? $res->where('orders.order_create_time', '>', strtotime($cons['s_date'])) : $res;
            $res = $cons['e_date'] ? $res->where('orders.order_create_time', '<', strtotime($cons['e_date'])) : $res;
        }
        if ($limit != '') {
            $res->limit($limit);
        }
        // return $res->groupBy('contract_info.city_manager_no')->limit(5)->get();
        return $res->groupBy('sync_user_info.USERNAME')
            ->orderBy('sum', 'DESC')
            ->limit(5)
            ->get();
    }
}
    
