<?php

namespace App\Http\Controllers\wx;

use App\Log\Facades\Logger;
use App\Model\mobile\AttributeModel;
use App\Model\mobile\BankModel;
use App\Model\mobile\BankPutModel;
use App\Model\mobile\ContractModel;
use App\Model\mobile\LoanPicModel;
use App\Model\mobile\LoanWorkModel;
use App\Model\mobile\OrderModel;
use App\Http\Controllers\Controller;
use App\Model\mobile\OrderPicModel;
use App\Model\mobile\OrderProductModel;
use App\Model\mobile\OrderSchModel;
use App\Model\mobile\OrderWorkModel;
use App\Model\mobile\UserInfoModel;
use App\Model\mobile\UserModel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use PhpSpec\Exception\Example\ErrorException;
use Symfony\Component\VarDumper\VarDumper;
use App\Util\FileReader;
use App\Service\base\Asapi;
use App\Http\Controllers\api;

class OrderController extends Controller
{
    public $user;

    public function __construct()
    {
        $openId = $this->openId();
        //$openId = 'oo1qgt4zYPX3ohakSI5PWoUK5jUI';
        $this->user = DB::table('users')->select('id')->where('openid', $openId)->first();
    }

    /**
     * 订单列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $status_list = DB::table('sync_code_library')->where('CODENO', 'ContractStatus')->select('ITEMNO','ITEMNAME')->get();

        $orderList = DB::table('orders')
                    ->select([
                        'orders.*',
                        'orders.id as oid',
                        'orders_product.*',
                        'sync_store_info.SNAME',
                        'contract_info.status',
                        'contract_info.monthly_repay_money'
                    ])
                    ->leftJoin('orders_product', 'orders.product_id', '=', 'orders_product.id')
                    ->leftJoin('sync_store_info', 'orders.merchant_code', '=', 'sync_store_info.SNO')
                    ->rightJoin('contract_info', 'orders.id', '=', 'contract_info.order_id')
                    ->where(['orders.user_id' => $this->user->id])
                    ->where('contract_info.status','<>','100')
                    ->groupBy('orders.id')
                    ->orderBy('orders.id', SORT_DESC)
                    //->tosql();
                    ->get();

        return view('wx.order_list', [
                    'orderList' => $orderList,
                    'statusList' => $this->_indexStatus($status_list)
        ]);
    }

    public static function _indexStatus($status_list)
    {
        $result = [];
        foreach ($status_list as $item) {
            if ($item->ITEMNO == '050')
                $result[$item->ITEMNO] = '还款中';
            else
                $result[$item->ITEMNO] = $item->ITEMNAME;
        }
        return $result;
    }

    /**
     * 订单详情
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDetail(Request $request)
    {
        $order_id = Input::get('order_id');
        $orderInfo = DB::table('orders as od')
            ->leftJoin('orders_picture as pic', 'od.picture_id', '=', 'pic.id')
            ->leftJoin('orders_product as pro', 'od.product_id', '=', 'pro.id')
            ->leftJoin('orders_work as work', 'od.work_id', '=', 'work.id')
            ->leftJoin('contract_info as ci','ci.order_id','=','od.id')
            ->where(['od.user_id' => $this->user->id, 'od.id' => $order_id])
            ->first();

        //dd($orderInfo);

        if ($orderInfo == null || empty($orderInfo)) {
            return redirect('wx/order/list');
        }

        /*计算服务费（手续费）和月供*/
        $trial_sql = "select bt.MANAGEMENTFEESRATE,bt.CUSTOMERSERVICERATES from sync_storerelativeproduct sp
                       left JOIN sync_product_businesstype pb on sp.PNO=pb.PRODUCTSERIESID
                       left JOIN sync_business_type bt on pb.BUSTYPEID=bt.TYPENO
                       where sp.SNO='".$orderInfo->merchant_code."' and bt.TERM='".$orderInfo->periods."' and
                       bt.LOWPRINCIPAL <= '".$orderInfo->loan_money."' and bt.TALLPRINCIPAL>= '".$orderInfo->loan_money."' limit 1";

        $trial_res = DB::select($trial_sql);
        if($trial_res){
            $customer_service_rate = $trial_res[0]->CUSTOMERSERVICERATES;//月客户服务费率
            $management_rate = $trial_res[0]->MANAGEMENTFEESRATE;//月财务管理费率
        }

        //服务费
        $service_fees = round(($orderInfo->loan_money*$customer_service_rate+$orderInfo->loan_money*$management_rate)/100,2);
        //月供
        $monthly_payment = round($orderInfo->loan_money/$orderInfo->periods+$service_fees,2);
        /*计算服务费（手续费）和月供end*/

        $contract_cache = Cache::get('contract_cache_key');
        if($orderInfo->contract_no == $contract_cache){
            $is_cache = true;
        }else{
            $is_cache = false;
        }

        return view('wx.order_detail', [
            'monthly_payment' => $monthly_payment,
            'service_fees' => $service_fees,
            'orderInfo' => $orderInfo,
            'is_cache' => $is_cache,
        ]);
    }


    /**
     * 用户主动取消合同
     */
    public function postCancelContract(){

        $contractNo = \Illuminate\Support\Facades\Request::get('contractNo');
        //进来缓存一下合同，用于显示页面上的取消按钮
        $contract_cache_key = 'contract_cache_key';
        $res = Cache::get($contract_cache_key);
        if(empty($res)){
            Cache::put($contract_cache_key,$contractNo,5);//缓存5分钟
        }

        //取消合同
        $model = new api\AsapiController();
        $rs = $model->anyCancelcontract($contractNo);
        $info = json_decode($rs);
        Logger::info('取消合同接口返回信息：'.json_encode($rs,JSON_UNESCAPED_UNICODE),'cancel-contract');
        if($info->status==200){
            return ['status'=>1,'msg'=>'已为您申请取消该订单！请您在一分钟后查看该订单状态！'];
        }else{
            return ['status'=>0,'msg'=>'请不要重复取消！'];
        }
    }


}
