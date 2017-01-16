<?php

namespace App\Model\Backend;
use DB;
use Illuminate\Database\Eloquent\Model;



/**
 * Description of BackUser
 *
 * @author
 */
class Orders extends Model {

    protected $table = 'orders';

    /**
     * 获得订单
     * $scope 订单状态分为  已提交  未提交
     * $condition 查询条件
     */
    public static function getOrders($authority, $scope, $condition) {
        //门店号筛选
        $merchant_list = self::getMerchantNoList($scope);
        //销售筛选
        $sales_list = self::getSalesList($merchant_list);
        $list = self::leftJoin('contract_info', 'contract_info.order_id', '=', 'orders.id')
            ->leftJoin('orders_product', 'orders_product.id', '=', 'orders.product_id')
            //->leftJoin('sync_store_info', 'sync_store_info.SNO', '=', 'orders.merchant_code')
            ->leftJoin('sync_store_info', function($leftJoin) use($merchant_list) {
                $leftJoin->on('sync_store_info.SNO', '=', 'orders.merchant_code')
                    ->whereIn('sync_store_info.SNO', $merchant_list);
            })
            //->leftJoin('sync_storerelativesalesman', 'sync_storerelativesalesman.SNO', '=', 'orders.merchant_code')
            ->leftJoin('sync_storerelativesalesman', function($leftJoin) use($sales_list){
                $leftJoin->on('sync_storerelativesalesman.SNO', '=', 'orders.merchant_code')
                    ->whereIn('sync_storerelativesalesman.SALESMANNO', $sales_list);
            })
            ->select('orders.id', 'contract_info.contract_no', 'contract_info.status', 'orders_product.applicant_name', 'orders_product.loan_money', 'orders_product.periods', 'sync_store_info.SNAME', 'orders.order_remark', 'orders.merchant_code', 'sync_store_info.SALESMANAGER', 'sync_store_info.CITYMANAGER', 'SALESMANNAME as sales', 'orders.rand_code')
            ->groupBy('orders.id')
            ->whereIn('orders.order_status', $scope);

        if( !empty($authority) ) {
            $list = $list->where($authority[0], $authority[1]);
        }
        if( !empty($condition) ) {
            foreach( $condition as $key=>$val ) {
                if( !empty($val[0]) || $val === "0" ) {
                    $list = $list->where($key, $val[1], $val[0]);
                }
            }
        };
        $list = $list->paginate(10);
        //用户名称表
        $user_list = self::getUserNameById($list);
        foreach($list as $key => $val) {
            $list[$key]['sale_manage'] = $val->SALESMANAGER ? $user_list[$val->SALESMANAGER] : '';
            $list[$key]['city_manage'] = $val->CITYMANAGER ? $user_list[$val->CITYMANAGER] : '';
        }

        return $list;
    }

    /**
     * @param $scope
     * @return mixed
     * 获取门店范围
     */
    public static function getMerchantNoList($scope) {
        $list = self::whereIn('order_status', $scope)->groupBy('merchant_code')->lists('merchant_code')->toArray();

        return $list;
    }

    /**
     * @param $scope
     * @return mixed
     * 销售人员范围
     */
    public static function getSalesList($scope) {
        $list = DB::table('sync_storerelativesalesman')->whereIn('SNO', $scope)->lists('SALESMANNO');
        return $list;
    }


    /**
     * 获得订单导出列表
     * $scope 订单状态分为  已提交  未提交
     * $condition 查询条件
     */
    public static function getPushOrders($authority, $scope, $condition) {
        //门店号筛选
        $merchant_list = self::getMerchantNoList($scope);
        //销售筛选
        $sales_list = self::getSalesList($merchant_list);
        $list = self::leftJoin('contract_info', 'contract_info.order_id', '=', 'orders.id')
            ->leftJoin('orders_product', 'orders_product.id', '=', 'orders.product_id')
            ->leftJoin('orders_work', 'orders_work.id', '=', 'orders.work_id')
            //->leftJoin('sync_store_info', 'sync_store_info.SNO', '=', 'orders.merchant_code')
            ->leftJoin('sync_store_info', function($leftJoin) use($merchant_list) {
                $leftJoin->on('sync_store_info.SNO', '=', 'orders.merchant_code')
                    ->whereIn('sync_store_info.SNO', $merchant_list);
            })
            //->leftJoin('sync_storerelativesalesman', 'sync_storerelativesalesman.SNO', '=', 'orders.merchant_code')
            ->leftJoin('sync_storerelativesalesman', function($leftJoin) use($sales_list){
                $leftJoin->on('sync_storerelativesalesman.SNO', '=', 'orders.merchant_code')
                    ->whereIn('sync_storerelativesalesman.SALESMANNO', $sales_list);
            })
            ->select('orders.order_create_time', 'sync_store_info.SNAME', 'orders_product.applicant_name', 'orders_product.applicant_id_card', 'orders.mobile', 'orders.reference', 'orders_product.service_type', 'orders.product_type', 'orders_product.loan_money', 'orders_product.periods', 'contract_info.monthly_repay_date', 'contract_info.monthly_repay_money', 'orders.industry_name', 'orders_work.work_unit', 'orders_work.work_unit_mobile', 'orders_work.work_addr1', 'orders_work.work_addr2', 'orders_work.work_addr3', 'orders_work.work_addr4', 'orders_work.work_addr5', 'orders_work.work_repayment_account', 'orders_work.work_deposit_bank', 'orders_work.work_bank_city', 'orders_work.edu_level', 'orders_work.qq_email', 'orders_work.family_relation', 'orders_work.family_name', 'orders_work.family_mobile', 'orders_work.family_addr1', 'orders_work.family_addr2', 'orders_work.family_addr3', 'orders_work.family_addr4', 'orders_work.family_addr5', 'contract_info.contract_no', 'contract_info.status', 'orders.order_remark', 'sync_store_info.SALESMANAGER', 'sync_store_info.CITYMANAGER', 'SALESMANNAME as sales')
            ->groupBy('orders.id')
            ->whereIn('orders.order_status', $scope);

        if( !empty($authority) ) {
            $list = $list->where($authority[0], $authority[1]);
        }
        if( !empty($condition) ) {
            foreach( $condition as $key=>$val ) {
                if( !empty($val['values'][0]) || $val['values'] === "0" ) {
                    if( $val['values'][1] == 'in') {
                        $list = $list->whereIn($val['key'], $val['values'][0]);
                    } else {
                        $list = $list->where($val['key'], $val['values'][1], $val['values'][0]);
                    }
                }
            }
        };

        $list = $list->get();

        //用户名称表
        $user_list = self::getUserNameById($list);
        foreach($list as $key => $val) {
            $list[$key]['sale_manage'] = $user_list[$val->SALESMANAGER];
            $list[$key]['city_manage'] = $user_list[$val->CITYMANAGER];
        }

        return $list;
    }

    /**
     * 获得店铺对应销售人员
     */
    public static function getSalesManList($data) {
        $store_id_array = [];
        $sales_list = [];
        foreach($data as $key=>$val) {
            $store_id_array[] = $val->merchant_code;
        }

        $store_id_array = array_unique($store_id_array);

        $list = DB::table('sync_storerelativesalesman')
            ->select(DB::raw('group_concat(SALESMANNAME) as sales'), 'SNO')
            ->whereIn('SNO', $store_id_array)
            ->groupBy('SNO')
            ->get();

        foreach($list as $key=>$val) {
            $sales_list[$val->SNO] = $val->sales;
        }
        return $sales_list;
    }

    /**
     * 获得用户名
     */
    public static function getUserNameById($data) {
        $result = [];
        $user_id_list = [];
        foreach ($data as $key=>$val) {
            $user_id_list[] = $val->SALESMANAGER;
            $user_id_list[] = $val->CITYMANAGER;
        }
        $user_id_list = array_unique($user_id_list);
        $list = DB::table('sync_user_info')
            ->select('USERID', 'USERNAME');
        if( !empty($user_id_list) ) {
            $list = $list->whereIn('USERID', $user_id_list);
        }
        $list = $list->get();
        foreach($list as $key=>$val) {
            $result[$val->USERID] = $val->USERNAME;
        }
        return $result;


    }

    /**
     * 订单状态文本
     */
    public static function getOrdersStatus() {
        return [
            '0' => '取消',
            '2' => '审核中',
            '3' => '审核通过',
            '4' => '审核拒绝',
            '5' => '签署中',
            '6' => '还款中',
            '7' => '已结清',
        ];
    }

    /**
     * 已提交订单
     */
    public static function getSubmitOrdersStatus() {
        return [2];
    }

    /**
     * 未提交订单状态
     */
    public static function getNotSubmitOrdersStatus() {
        return [1];
    }
    
    /**
     * 订单根据权限设立初始条件
     */
    public static function getAuthorityCondition($role_id, $user_id) {
        $list = [
            1 => 'sync_storerelativesalesman.SALESMANNO',  //销售人员
            2 => 'sync_store_info.SALESMANAGER',
            3 => 'sync_store_info.CITYMANAGER'
        ];
        $condition = [];

        if( isset($list[$role_id]) ) {
            $condition[] = $list[$role_id];
            $condition[] = $user_id;
        }
        return $condition;
    }

    /**
     * 订单详情
     */
    public static function getOrderDetailInfo($order_id) {
        $info = self::leftJoin('contract_info', 'contract_info.order_id', '=', 'orders.id')
            ->leftJoin('orders_product', 'orders_product.id', '=', 'orders.product_id')
            ->leftJoin('orders_work', 'orders_work.id', '=', 'orders.work_id')
            ->leftJoin('sync_product_ctype', 'sync_product_ctype.PRODUCTCTYPEID', '=', 'contract_info.product_no')
            ->leftJoin('sync_product_category', 'sync_product_category.PRODUCTCATEGORYID', '=', 'sync_product_ctype.PRODUCTCATEGORYID')
            ->where('orders.id', $order_id)
            ->select('orders.id', 'orders.merchant_code', 'orders.mobile', 'orders_product.applicant_name', 'orders_product.applicant_id_card', 'orders.industry_name', 'sync_product_category.PRODUCTCATEGORYID', 'sync_product_category.PRODUCTCATEGORYNAME', 'orders_product.service_type', 'orders_product.pay_type', 'orders_product.loan_money', 'contract_info.monthly_repay_date', 'orders_product.periods', 'orders_work.work_unit', 'orders_work.work_unit_mobile', 'orders_work.edu_level', 'orders_work.qq_email', 'orders_work.family_name', 'orders_work.family_mobile', 'orders_work.work_repayment_account', 'orders_work.work_deposit_bank', 'orders_work.work_bank_city', 'orders_work.work_bank_branch_name', 'orders.mobile_service_password', 'orders.jd_account', 'orders.jd_password', 'orders.tb_account', 'orders.tb_password')
            ->first();
        $info = self::objToArray($info);
        $merchantSaleInfo = self::getMerchantSalesInfo($info['merchant_code']);
        $service_cost = self::getServiceCost($info['merchant_code'], $info['periods'], $info['loan_money']);
        $info['service_cost'] = $service_cost;
        $info = array_merge($info, $merchantSaleInfo);
        return $info;
    }

    /**
     * 获得商户及销售相关信息
     */
    public static function getMerchantSalesInfo($merchant_code) {
        $info = DB::table('sync_store_info')
            ->leftJoin('sync_storerelativesalesman', 'sync_storerelativesalesman.SNO', '=', 'sync_store_info.SNO')
            ->leftJoin('sync_user_info as manager_info', 'manager_info.USERID', '=', 'sync_store_info.SALESMANAGER')
            ->leftJoin('sync_user_info as city_manager_info', 'city_manager_info.USERID', '=', 'sync_store_info.CITYMANAGER')
            ->leftJoin('sync_retail_info', 'sync_retail_info.SERIALNO', '=', 'sync_store_info.RSERIALNO')
            ->select(DB::raw('group_concat(SALESMANNAME) as sales'), DB::raw('group_concat(SALESMANNO) as sales_id'), 'SALESMANAGER', 'CITYMANAGER', 'manager_info.USERNAME as manager_name', 'city_manager_info.USERNAME as city_manager_name', 'sync_store_info.SNAME', 'sync_retail_info.RNO', 'sync_retail_info.RNAME', 'sync_store_info.CITY')
            ->groupBy('sync_store_info.SNO')
            ->where('sync_store_info.SNO', $merchant_code)
            ->first();
        $info = self::objToArray($info);
        return $info;
    }

    /**
     * @param $data
     * @return mixed
     * 对象转数组
     */
    public static function objToArray($data) {
        return json_decode(json_encode($data), true);
    }

    public static function getOrderImage($order_id) {
        $info =self::leftJoin('orders_picture', 'orders.picture_id', '=', 'orders_picture.id')
            ->select('orders.id', 'cert_face_pic', 'cert_opposite_pic', 'cert_hand_pic', 'work_pic', 'contract_pic', 'bank_card_pic', 'credit_auth_pic')
            ->where('orders.id', $order_id)
            ->first();
        return $info;
    }

    /**
     * 订单服务费计算
     */
    public static function getServiceCost($merchant_code, $periods, $loanAmount) {
        //查询用户最新的订单进行试算
        $trial_sql = "select bt.MANAGEMENTFEESRATE,bt.CUSTOMERSERVICERATES from sync_storerelativeproduct sp
                       left JOIN sync_product_businesstype pb on sp.PNO=pb.PRODUCTSERIESID
                       left JOIN sync_business_type bt on pb.BUSTYPEID=bt.TYPENO
                       where sp.SNO='".$merchant_code."' and bt.TERM='".$periods."' and bt.LOWPRINCIPAL <= '".$loanAmount."' and bt.TALLPRINCIPAL>= '".$loanAmount."' limit 1";

        $trial_res = DB::select($trial_sql);
        if($trial_res){
            $customer_service_rate = $trial_res[0]->CUSTOMERSERVICERATES;//月客户服务费率
            $management_rate = $trial_res[0]->MANAGEMENTFEESRATE;//月财务管理费率

            //服务费
            $service_fees = round(($loanAmount*$customer_service_rate+$loanAmount*$management_rate)/100,2);
        }else {
            $service_fees = "数据异常未知";
        }

        return $service_fees;
    }

    /**
     * 获得区域经理用户名
     */
    public static function getCityManager() {
        $list = self::leftJoin('sync_store_info', 'sync_store_info.SNO', '=', 'orders.merchant_code')
            ->leftJoin('sync_user_info', 'sync_store_info.CITYMANAGER', '=', 'sync_user_info.USERID')
            ->select('sync_user_info.USERID', 'sync_user_info.USERNAME')
            ->groupBy('sync_user_info.USERID')
            ->get();
        return $list;
    }

}