<?php

namespace App\Model\mobile;

use App\Http\Controllers\api\AsapiController;
use App\Log\Facades\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderModel extends Model
{
    protected $table = 'orders';

    public static $extraInfo = [
        'jd_account' => '京东账号',
        'jd_password' => '京东密码',
        'tb_account' => '淘宝账号',
        'tb_password' => '淘宝密码',
    ];

    /**
     * 获取用户协议模板中的相关信息
     * @param $order_id
     * @return mixed
     */
    public function get_order_data_by_order_id($order_id){
        $data = DB::table($this->table)->where('orders.id',$order_id)
            ->leftjoin('orders_product', 'orders_product.id', '=', 'orders.product_id')
            ->leftjoin('orders_picture' ,'orders_picture.id', '=', 'orders.picture_id')
            ->select('user_id','service_type_no','order_create_time','merchant_code','applicant_id_card','product_type','school_id','product_id','work_id','order_status','mobile','orders.id as order_id','applicant_name','protocol_url','cert_face_pic','cert_opposite_pic','cert_hand_pic')
            ->first();

        return $data;
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function get_order_info($order_id){
        $data = DB::table($this->table)->where('id',$order_id)->first();
        return $data;
    }

    /**
     * 更新order中的协议文件地址
     * @param $array
     * @param $id
     * @return mixed
     */
    public function update_order_by_id($array,$id){
        $data = DB::table($this->table)->where('id',$id)->update($array);
        return $data;
    }


    /**
     * 中信，中泰判断
     * @param $order_id
     * @return mixed
     */
    public function get_contract_types($order_id){
        $order_obj = $this->get_order_data_by_order_id($order_id);

        $data = DB::table('sync_service_providers as sp')->where([
            'sp.customertype1'=>'06',
            'pc.ProductType'=>0,
            'sp.loaner'=>'010',
            'si.SNO'=>$order_obj->merchant_code
        ])
            ->leftjoin('sync_providerscity as pc','pc.serialno','=','sp.serialno')
            ->leftjoin('sync_store_info as si','si.CITY','=','pc.AREACODE')
            ->select('sp.serialno as SerialNo', 'sp.serviceprovidersname as ServiceProvidersName')
            ->first();

        return $data->SerialNo;
    }


    /**
     * 总的订单合同数
     * @return mixed
     */
    public function get_count_nums(){

        $data = DB::table('contract_info as ci')
            ->distinct('ci.contract_no')
            ->count('ci.contract_no');
        return $data;
    }


    /**
     * 总订单额
     * @return mixed
     */
    public function get_contract_sum_money()
    {
        $data = DB::table('contract_info as ci')->where('ci.status', '=', '050')
            ->leftjoin('orders as o','ci.order_id','=','o.id')
            ->leftjoin('orders_product as op', 'op.id', '=', 'o.product_id')
            ->sum('op.loan_money');

        return $data;
    }

    /**
     * 总的有效订单
     * @return mixed
     */
    public function get_valid_contract_nums(){
        $data = DB::table('contract_info')
            ->where('status','=','050')
            ->distinct('contract_no')
            ->count('contract_no');
        return $data;
    }

    public function get_rand_count_nums(){
        $data = DB::table('orders')->orderBy('id','DESC')->count();
        return $data;
    }

    public function get_rand_data($offset,$rev){
        $data = DB::table('orders')->orderBy('id','DESC')->take($rev)->skip($offset)->get();
        return $data;
    }

    /**
     * 当天有效合同数
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public function get_today_valid_contract($startTime,$endTime)
    {
        $data = DB::table('contract_info')
            ->whereIn('status',['050','020','080'])
            ->where('create_time','>=',$startTime)
            ->where('create_time','<=',$endTime)
            ->distinct('contract_no')
            ->count('contract_no');
        return $data;
    }

    /**
     * 当天被否订单数
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public function get_today_reject_contract($startTime,$endTime)
    {
        $data = DB::table('contract_info')
            ->where('status','=','010')
            ->where('create_time','>=',$startTime)
            ->where('create_time','<=',$endTime)
            ->distinct('contract_no')
            ->count('contract_no');
        return $data;
    }

    /**
     * 当天取消订单数
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public function get_today_cancel_contract($startTime,$endTime)
    {
        $data = DB::table('contract_info')
            ->where('status','=','100')
            ->where('create_time','>=',$startTime)
            ->where('create_time','<=',$endTime)
            ->distinct('contract_no')
            ->count('contract_no');
        return $data;
    }

    /**
     * 当天有效订单金额
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public function get_today_valid_money($startTime,$endTime)
    {
        $data = DB::table('contract_info as ci')
            ->whereIn('ci.status',['050','020','080'])
            ->where('create_time','>=',$startTime)
            ->where('create_time','<=',$endTime)
            ->leftjoin('orders as o','ci.order_id','=','o.id')
            ->leftjoin('orders_product as op', 'op.id', '=', 'o.product_id')
            ->sum('op.loan_money');
        return $data;
    }


    /**
     * 合同列表分页数据
     * @param $offset
     * @param $rev
     * @return mixed
     */
    public function get_page_data($offset,$rev){
        $data = DB::table('contract_info as ci')
            ->select(
                DB::raw('
                    FROM_UNIXTIME(ci.create_time,"%Y-%m-%d %H:%i:%S") as order_commit_time,
                    FROM_UNIXTIME(ci.create_time,"%Y-%m-%d %H:%i:%S") as order_time
                '),
                'ci.contract_no as contract_no',
                'ci.status as status',
                'ci.order_id as order_id',
                'ci.monthly_repay_date as monthly_repay_date',
                'ci.monthly_repay_money as monthly_repay_money'
            )
            ->orderBy('id','DESC')->take($rev)->skip($offset)
            //->tosql();
            ->get();

        foreach($data as $v){
            $orderIds[$v->order_id] = $v->order_id;
            $statuIds[$v->order_id] = $v->status;
            $contracts[$v->order_id] = $v->contract_no;
            $return[$v->order_id] = $v;
        }

        //查询合同状态，记录取消原因
        $contract_str = implode(',',$contracts);
        $model = new AsapiController();
        $rs = $model->anyContractstatus($contract_str);
        $rs = json_decode($rs);
        foreach($rs->data as $v){
            $contractReason[$v->contractNo] = is_null($v->reMarks) ? $v->cancelReason : $v->reMarks;
        }
        //order_id 对应取消的原因
        foreach($contracts as $k=>$v){
            foreach($contractReason as $kk=>$vv){
                if($v==$kk){
                    $reason[$k] = $vv;
                }
            }
        }

        $orders = $this->getOrders($orderIds);

        foreach($orders as $k=>$v){
            $productIds[$v->id] = $v->product_id;
            $workIds[$v->id] = $v->work_id;
            $codeIds[$v->id] = $v->merchant_code;
            $snoIds[$v->id] = $v->merchant_code;
        }
        $products = $this->getProducts($productIds);
        $works = $this->getWorks($workIds);
        $codes = $this->getCodes($codeIds);
        $status = $this->getStatus($statuIds);


        $salesmenInfo = $this->getUname($snoIds);
        $salesManagerInfo = $this->getManagerUname($snoIds);
        $salesCityInfo = $this->getCityUname($snoIds);
        $salesProductInfo = $this->getProductName($snoIds);

        $order_number = 0;
        foreach($return as $k=>$v){
            $order_number++;
            $v->order_number = $order_number;//分页序号
            $v->mobile = $orders[$orderIds[$k]]->mobile;//手机号
            $v->order_start_time = $orders[$orderIds[$k]]->order_start_time;
            $v->service_type = $products[$productIds[$k]]->service_type;//服务类型
            $v->applicant_id_card = $products[$productIds[$k]]->applicant_id_card;//身份证
            $v->applicant_name = $products[$productIds[$k]]->applicant_name;//姓名
            $v->loan_money = $products[$productIds[$k]]->loan_money;//贷款金额
            $v->periods = $products[$productIds[$k]]->periods;//期数
            $v->work_unit = $works[$workIds[$k]]->work_unit;//工作单位
            $v->edu_level = $works[$workIds[$k]]->edu_level;//最高学历
            $v->family_mobile = $works[$workIds[$k]]->family_mobile;//家属联系电话
            $v->family_name = $works[$workIds[$k]]->family_name;//家属姓名
            $v->SNAME = $codes[$codeIds[$k]]->SNAME;//门店
            $v->ITEMNAME = $status[$statuIds[$k]];//合同状态
            $v->USERNAME = isset($salesmenInfo[$snoIds[$k]]) ? $salesmenInfo[$snoIds[$k]]->USERNAME:'';//销售代表
            $v->SALESMANAGERNAME = isset($salesManagerInfo[$snoIds[$k]]) ? $salesManagerInfo[$snoIds[$k]]->USERNAME : '';//销售经理
            $v->CITYMANAGERNAME = isset($salesCityInfo[$snoIds[$k]]) ? $salesCityInfo[$snoIds[$k]]->USERNAME : '';//城市经理
            $v->PNAME = isset($salesProductInfo[$snoIds[$k]]) ? $salesProductInfo[$snoIds[$k]]->PNAME : '';//产品名称
            $v->reason = isset($reason[$k]) ? $reason[$k] : '';//合同取消原因
        }
        return $return;
    }

    /**
     * 合同信息下载
     * @param $offset
     * @param $rev
     * @return mixed
     */
    public function get_excel_data($startTime,$endTime){
        $data = DB::table('contract_info as ci')
            ->select(
                DB::raw('
                    FROM_UNIXTIME(ci.create_time,"%Y-%m-%d %H:%i:%S") as order_commit_time,
                    FROM_UNIXTIME(ci.create_time,"%Y-%m-%d %H:%i:%S") as order_time
                '),
                'ci.contract_no as contract_no',
                'ci.status as status',
                'ci.order_id as order_id',
                'ci.monthly_repay_date as monthly_repay_date',
                'ci.monthly_repay_money as monthly_repay_money'
            )
            ->orderBy('id','DESC')->where('create_time','>=',$startTime)->where('create_time','<=',$endTime)
            ->get();

        foreach($data as $v){
            $orderIds[$v->order_id] = $v->order_id;
            $statuIds[$v->order_id] = $v->status;
            $return[$v->order_id] = $v;
        }
        $orders = $this->getOrders($orderIds);

        foreach($orders as $k=>$v){
            $productIds[$v->id] = $v->product_id;
            $workIds[$v->id] = $v->work_id;
            $codeIds[$v->id] = $v->merchant_code;
            $snoIds[$v->id] = $v->merchant_code;
        }

        $products = $this->getProducts($productIds);
        $works = $this->getWorks($workIds);
        $codes = $this->getCodes($codeIds);
        $status = $this->getStatus($statuIds);


        $salesmenInfo = $this->getUname($snoIds);
        $salesManagerInfo = $this->getManagerUname($snoIds);
        $salesCityInfo = $this->getCityUname($snoIds);
        $salesProductInfo = $this->getProductName($snoIds);

        $order_number = 0;
        foreach($return as $k=>$v){
            $order_number++;
            $v->order_number = $order_number;//分页序号
            $v->mobile = $orders[$orderIds[$k]]->mobile;//手机号
            $v->order_start_time = $orders[$orderIds[$k]]->order_start_time;
            $v->service_type = $products[$productIds[$k]]->service_type;//服务类型
            $v->applicant_id_card = $products[$productIds[$k]]->applicant_id_card;//身份证
            $v->applicant_name = $products[$productIds[$k]]->applicant_name;//姓名
            $v->loan_money = $products[$productIds[$k]]->loan_money;//贷款金额
            $v->periods = $products[$productIds[$k]]->periods;//期数
            $v->work_unit = $works[$workIds[$k]]->work_unit;//工作单位
            $v->edu_level = $works[$workIds[$k]]->edu_level;//最高学历
            $v->family_mobile = $works[$workIds[$k]]->family_mobile;//家属联系电话
            $v->family_name = $works[$workIds[$k]]->family_name;//家属姓名
            $v->SNAME = $codes[$codeIds[$k]]->SNAME;//门店
            $v->ITEMNAME = $status[$statuIds[$k]];//合同状态
            $v->USERNAME = isset($salesmenInfo[$snoIds[$k]]) ? $salesmenInfo[$snoIds[$k]]->USERNAME:'';//销售代表
            $v->SALESMANAGERNAME = isset($salesManagerInfo[$snoIds[$k]]) ? $salesManagerInfo[$snoIds[$k]]->USERNAME : '';//销售经理
            $v->CITYMANAGERNAME = isset($salesCityInfo[$snoIds[$k]]) ? $salesCityInfo[$snoIds[$k]]->USERNAME : '';//城市经理
            $v->PNAME = isset($salesProductInfo[$snoIds[$k]]) ? $salesProductInfo[$snoIds[$k]]->PNAME : '';//产品名称
        }

        return $return;
    }

    /**
     * 销售经理
     * @param $snoIds
     * @return mixed
     */
    public function getManagerUname($snoIds)
    {
        $data = DB::table('sync_store_info as st')->whereIn('st.SNO',$snoIds)
            ->leftjoin('sync_user_info as ui','st.SALESMANAGER','=','ui.USERID')
            ->get();

        foreach($data as $v){
            if($v->SNO){
                $res[$v->SNO] = $v;
            }

        }
        return $res;
    }

    /**
     * 销售代表
     * @param $snoIds
     * @return mixed
     */
    public function getUname($snoIds){
        $data = DB::table('sync_storerelativesalesman as sm')->whereIn('sm.SNO',$snoIds)
            //->whereNull('sm.STYPE')
            ->leftjoin('sync_user_info as ui','sm.SALESMANNO','=','ui.USERID')
            ->get();

        foreach($data as $v){
            $res[$v->SNO] = $v;
        }
        return $res;
    }

    /**
     * 城市经理
     * @param $snoIds
     * @return mixed
     */
    public function getCityUname($snoIds){
        $data = DB::table('sync_store_info as st')->whereIn('st.SNO',$snoIds)
            ->leftjoin('sync_user_info as ui','st.SALESMANAGER','=','ui.USERID')
            ->leftjoin('sync_user_info as ui3','ui.SUPERID','=','ui3.USERID')
            ->get();

        foreach($data as $v){
            $res[$v->SNO] = $v;
        }
        return $res;
    }

    /**
     * 产品名称
     * @param $snoIds
     * @return mixed
     */
    public function getProductName($snoIds){
        $data = DB::table('sync_storerelativeproduct as sp')->whereIn('sp.SNO',$snoIds)
            ->get();

        foreach($data as $v){
            $res[$v->SNO] = $v;
        }
        return $res;
    }


    public function getOrders($orderIds){
        $data = DB::table('orders')->whereIn('id',$orderIds)
            ->select(
                DB::raw('
                    FROM_UNIXTIME(order_create_time,"%Y-%m-%d %H:%i:%S") as order_start_time
                '),'mobile','product_id','work_id','merchant_code','id'
            )
            ->get();

        foreach($data as $v){
            $res[$v->id] = $v;
        }
        return $res;
    }

    public function getProducts($productIds){
        $data = DB::table('orders_product')->whereIn('id',$productIds)
            ->get();

        foreach($data as $v){
            $res[$v->id] = $v;
        }
        return $res;
    }

    public function getWorks($workIds){
        $data = DB::table('orders_work')->whereIn('id',$workIds)->get();
        foreach($data as $v){
            $res[$v->id] = $v;
        }
        return $res;
    }

    public function getCodes($codes){
        $data = DB::table('sync_store_info')->whereIn('SNO',$codes)->get();
        foreach($data as $v){
            $res[$v->SNO] = $v;
        }
        return $res;
    }


    /**
     * 订单的几种状态让其放入缓存
     * @return mixed
     */
    public function getStatus(){

        $order_cache_key = '_order_status_cache_key_';
        $res = Cache::get($order_cache_key);
        if(empty($res)){
            //缓存状态编号的对应关系
            $res = DB::table('sync_code_library')->where('CODENO','=','ContractStatus')->lists('ITEMNAME','ITEMNO');
            Cache::put($order_cache_key,$res,1440*7);//缓存七天
        }
        return $res;
    }

}
