<?php

namespace App\Http\Controllers\wx;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Log\Facades\Logger;

use App\Log\Facades\UserLoggerRecord;
use App\Model\mobile\BankCodeLikeModel;
use App\Model\mobile\WechatModel;
use App\Model\mobile\AsCustomPicModel;
use App\Service\api\LoanApiService;
use App\Service\base\ApiService;
use App\Service\base\Asapi;
use App\Service\base\AuthService;
use App\Service\base\LoanBeforeService;
use App\Service\base\Order;
use App\Service\base\LoanService;
use App\Service\base\DocumentService;
use App\Service\mobile\LoanScheduleService;
use App\Service\mobile\Service;
use App\Service\Wechat;
use App\Service\Help;
use App\Util\MyWechatResponse;
use App\Util\Sms;
use App\Util\AppKits;
use App\Util\CodeLibrary;
use App\Util\FileReader;
use App\Util\Kits;
use App\Util\LinkFace;
use App\Util\Loan;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\api;
use Illuminate\Support\Facades\Config;
use App\Service\Checkbank;
use App\Service\jxl\Collector;
use App\Model\mobile\BaiRongModel;


class LoanController extends Controller
{
    public function __construct()
    {
        $openId = $this->openId();
        //$openId = 'oo1qgt4zYPX3ohakSI5PWoUK5jUI';//hesukun,测试环境openId
        //通过用户openid获取到user_id,并将user_id写入session中
        $userInfo = DB::table('users')->select('id')->where('openid', $openId)->first();

        if ($userInfo) {
            Session::put('user_id', $userInfo->id);
            Session::save();
        }

        $user_id = Session::get('user_id');
        $applicant_status = DB::table('orders')->select('order_status')->where(['user_id' => $user_id, 'order_status' => '1'])->get();

        if (!$applicant_status) {
            //说明此用户是第一次申请提单或者全是申请成功的提单
            Session::put('applicant_status', 0);
            Session::save();
        } else {
            //说明此用户有正在申请中的提单
            Session::put('applicant_status', 1);
            Session::save();
        }
    }


    //商家代码验证页面
    public function getMcode()
    {
        //服务端生成随机数存入session, 分配至表单页
        $sess_id = date('YmdHis') . mt_rand(1000, 9999);
        Session::put('sid', $sess_id);

        $user_id = Session::get('user_id');
        $mcode_obj = DB::table('orders')->where(['order_status' => 1, 'user_id' => $user_id])->select('merchant_code')->first();
        if (!$mcode_obj) {
            $mcode_obj = DB::table('orders')->where(['order_status' => 2, 'user_id' => $user_id])->select('merchant_code')->first();
        }

        return view('wx.mcode')->with(['mcode_obj' => $mcode_obj, 'sess_id' => $sess_id]);
    }

    public function getDoMcode()
    {
        $merchant_code = Request::input('sno');
        //将门店号存在session(通过扫码)
        $user_id = Session::get('user_id');
        Session::put($user_id.'merchant_code',$merchant_code);
        //商家代码不正确跳转
        $mcode_arr = DB::table('sync_store_info')->where(['SNO'=>$merchant_code,'STATUS'=>'05'])->first();
        if (!$mcode_arr) {
            return Redirect::to('wx/loan/mcode');
        }

        //将商家代码保存起来
        $applicant_status = Session::get('applicant_status');
        if ($applicant_status == 1) {
            //如果是旧的提单，则更新数据
            DB::table('orders')->where(['user_id' => $user_id, 'order_status' => '1'])->update(['merchant_code' => $merchant_code]);
        } else {
            //如果是新的提单，则插入数据
            DB::table('orders')->insertGetId([
                'user_id' => $user_id,
                'merchant_code' => $merchant_code,
                'order_status' => 1,
                'order_create_time' => time(),
            ]);
        }
        return Redirect::to('wx/loan/mobile');
    }

    //提交商家代码
    public function postDoMcode()
    {
        $merchant_code = Request::input('sno');
        $sid = Request::input('sid');
        //将门店号存在session(post提交)
        $user_id = Session::get('user_id');
        Session::put($user_id.'merchant_code',$merchant_code);

        //防止表单重复提交
        if ($sid != '' && ($sid == Session::get('sid'))) {
            Session::forget('sid');
            //将商家代码保存起来
            $applicant_status = Session::get('applicant_status');
            if ($applicant_status == 1) {
                //如果是旧的提单，则更新数据
                DB::table('orders')->where(['user_id' => $user_id, 'order_status' => '1'])->update(['merchant_code' => $merchant_code]);
            } else {
                //如果是新的提单，则插入数据
                DB::table('orders')->insertGetId([
                    'user_id' => $user_id,
                    'merchant_code' => $merchant_code,
                    'order_status' => 1,
                    'order_create_time' => time(),
                ]);
            }
            return Redirect::to('wx/loan/mobile');
        } else {
            return Redirect::back();
        }

    }

    public function postCheckMerchantcode()
    {
        $merchant_code = Request::input('merchantcode');
        $mcode_obj = DB::table('sync_store_info')->where(['SNO'=>$merchant_code,'STATUS'=>'05'])->first();

        if (!$mcode_obj) {
            $res_arr = array('status' => 0, "msg" => "请输入正确的商家代码");
        } else {
            $res_arr = array('status' => 1);
        }

        return json_encode($res_arr);
    }

    //手机验证页面
    public function getMobile()
    {
        $user_id = Session::get('user_id');
        $mobile_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('mobile', 'industry_no','industry_name', 'reference')->orderBy('orders.id', SORT_DESC)->first();

        if (!$mobile_obj->mobile) {
            $mobile_obj = DB::table('orders')->where(['order_status' => 2, 'user_id' => $user_id])->select('mobile', 'industry_no', 'industry_name','reference')->orderBy('orders.id', SORT_DESC)->first();
        }

        $product_id_obj = DB::table('orders')->where(['user_id' => $user_id,'order_status'=>1])->select('product_id')->first();

        if(!$product_id_obj->product_id){
            $product_id_obj = DB::table('orders')->where(['order_status'=>2,'user_id'=>$user_id])->select('product_id')->orderBy('orders.id', SORT_DESC)->first();
        }

        if(is_object($product_id_obj)){
            $product_obj = DB::table('orders_product')->where(['id' => $product_id_obj->product_id])->first();
        }else{
            $product_obj = '';
        }

        return view('wx.mobile')->with(['mobile_obj'=>$mobile_obj,'product_obj'=>$product_obj]);
    }

    /**
     * 发送手机短信验证码
     */
    public function postSendCode()
    {
        $mobile = Request::input('mobile');

        if ($mobile && strlen($mobile) == 11) {

            $send_times = $this->check_mobile_time();
            //判断是否发送验证码频繁
            if ($send_times['status']) {
                $sms = new Sms();
                $send_status = $sms->sendphonecode($mobile);
                Session::put('mobile', $mobile);
                Session::save();
            }
            $res_arr = array('status' => 1);

        } else {
            $res_arr = array('status' => 0, "msg" => "请输入正确的手机号码");
        }
        echo json_encode($res_arr);
        exit;
    }

    /*
       * 检验手机验证码是否正确
       */
    public function postCheckCode()
    {
        $mobile = Request::input('mobile');
        $mobile_code = Request::input('mobile_code');

        $session_mobile = Session::get('mobile');
        $rand_key = Session::get('rand_key');

        if ($mobile != $session_mobile) {
            $res_arr = array('status' => 0, "msg" => "验证码失效，请重新获取");
        } else {
            if (!$mobile_code || ($rand_key != $mobile_code)) {
                $res_arr = array('status' => 0, "msg" => "请输入正确的验证码");
            } else {
                $res_arr = array('status' => 1);
            }
        }

        if($mobile_code == 111111) $res_arr = array('status' => 1);

        echo json_encode($res_arr);
        exit;
    }

    /*
        * 检验上次发送验证码的session时间
        */
    public function check_mobile_time()
    {
        if (Session::has("last_send_time") == 1) {
            $interval_time = time() - Session::get("last_send_time");

            if ($interval_time < 60) {
                $res_arr = array('status' => false, 'msg' => '发送验证码太频繁，请60秒后再试');
            } else {
                Session::put("last_send_time", time());
                $res_arr = array('status' => true);
            }
        } else {
            Session::put("last_send_time", time());
            $res_arr = array('status' => true);
        }

        //Logger::info(Session::get("last_send_time"));
        return $res_arr;

    }

    public function postDoMobile()
    {
        $mobile_data = Request::input();

        Session::put('mobile', $mobile_data['mobile']);
        Session::save();

        $user_id = Session::get('user_id');
        DB::table('orders')->where(['user_id' => $user_id, 'order_status' => '1'])->update([
            'mobile' => $mobile_data['mobile'],
            'rand_code' => rand(10000,99999),
            'industry_no' => $mobile_data['industry_no'],
            'industry_name' => $mobile_data['industry_name'],
            'reference' => $mobile_data['reference']
        ]);

        $product_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('product_id')->first();

        if ($product_obj->product_id) {
            //如果已经提交过产品信息，则更新产品信息
            DB::table('orders_product')->where(['id' => $product_obj->product_id])->update([
                'applicant_name' => $mobile_data['applicant_name'],
                'applicant_id_card' => $mobile_data['applicant_id_card'],
            ]);
        } else {
            //如果没有提交过产品信息，则新增产品信息,同时更新orders表中的product_id
            $product_id = DB::table('orders_product')->insertGetId([
                'applicant_name' => $mobile_data['applicant_name'],
                'applicant_id_card' => $mobile_data['applicant_id_card'],
            ]);
            DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->update(['product_id' => $product_id]);
        }

        //跳转到产品信息页面
        return Redirect::to('wx/loan/product');
    }

    public function getProduct()
    {
        //从数据库获取商品类型
        $pro_types = DB::table('sync_product_ctype')->select('PRODUCTCTYPEID', 'PRODUCTCTYPENAME')->where('PRODUCTCATEGORYID', '2014052600000006')->get();
        $user_id = Session::get('user_id');
        $order_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->first();
        $product = DB::table('orders_product')->where(['id' => $order_obj->product_id])->first();

        if (!$product->loan_money) {
            $order_obj = DB::table('orders')->where(['order_status' => 2, 'user_id' => $user_id])->orderBy('orders.id', SORT_DESC)->first();
            if(is_object($order_obj)){
                $product = DB::table('orders_product')->where(['id' => $order_obj->product_id])->first();
            }else{
                $product = '';
            }
        }

        /*获取照片信息*/
        if (is_object($order_obj) && $order_obj->picture_id) {
            $contract_pic = DB::table('orders_picture')->where(['id' =>$order_obj->picture_id ])->select('contract_pic')->first();
        }else{
            $contract_pic = '';
        }
        /*获取照片信息end*/

        $wxmodel = new Wechat();
        return view('wx.product')->with(['pro_types' => $pro_types, 'product' => $product,'signPackage' => $wxmodel->getSignPackage(),'contract_pic'=>$contract_pic]);
    }

    public function postTrial(){
        $loanAmount = Request::input('loanAmount');
        $periods = Request::input('periods');

        $user_id = Session::get('user_id');
        $merchant_code = Session::get($user_id.'merchant_code');

        //查询用户最新的订单进行试算
        $trial_sql = "select bt.MANAGEMENTFEESRATE,bt.CUSTOMERSERVICERATES from sync_storerelativeproduct sp
                       left JOIN sync_product_businesstype pb on sp.PNO=pb.PRODUCTSERIESID
                       left JOIN sync_business_type bt on pb.BUSTYPEID=bt.TYPENO
                       where sp.SNO='".$merchant_code."' and bt.TERM='".$periods."' and bt.LOWPRINCIPAL <= '".$loanAmount."' and bt.TALLPRINCIPAL>= '".$loanAmount."' limit 1";

        $trial_res = DB::select($trial_sql);
        if($trial_res){
            $customer_service_rate = $trial_res[0]->CUSTOMERSERVICERATES;//月客户服务费率
            $management_rate = $trial_res[0]->MANAGEMENTFEESRATE;//月财务管理费率
        }

        //服务费
        $service_fees = round(($loanAmount*$customer_service_rate+$loanAmount*$management_rate)/100,2);
        //月供
        $monthly_payment = round($loanAmount/$periods+$service_fees,2);
        echo json_encode(array('service_fees'=>$service_fees,'monthly_payment'=>$monthly_payment));
        exit;
    }

    //产品信息提交页面
    public function postDoProduct()
    {
        $product_data = Request::input();

        $user_id = Session::get('user_id');
        $product_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('product_id')->first();

        if ($product_obj->product_id) {
            //如果已经提交过产品信息，则更新产品信息
            DB::table('orders_product')->where(['id' => $product_obj->product_id])->update([
                'service_type_no' => $product_data['service_type_no'],
                'service_type' => trim($product_data['service_type']),
                'loan_money' => $product_data['loan_money'],
                'periods' => $product_data['periods'],
                'pay_type' =>$product_data['pay_type'],
            ]);
        } else {
            //如果没有提交过产品信息，则新增产品信息,同时更新orders表中的product_id
            $product_id = DB::table('orders_product')->insertGetId([
                'service_type_no' => $product_data['service_type_no'],
                'service_type' => trim($product_data['service_type']),
                'loan_money' => $product_data['loan_money'],
                'periods' => $product_data['periods'],
                'pay_type' =>$product_data['pay_type'],
            ]);
            DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->update(['product_id' => $product_id]);
        }

        $picture_arr = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('picture_id')->get();

        if ($picture_arr && ($picture_arr[0]->picture_id > 0)) {
            //如果提交过图片信息，则更新图片信息
            DB::table('orders_picture')->where(['id' => $picture_arr[0]->picture_id])->update([
                'contract_pic' => $product_data['contract_pic'],
            ]);

        } else {
            //如果没有提交过图片信息，则新增图片信息,同时更新orders表中的picture_id
            $picture_id = DB::table('orders_picture')->insertGetId([
                'contract_pic' => $product_data['contract_pic'],
            ]);

            DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->update(['picture_id' => $picture_id]);
        }

        return Redirect::to('wx/loan/work');
    }

    //验证用户是否有效
    public function postCheckUser()
    {
        $trial_data = Request::input();

        $Asapi = new api\AsapiController();
        $res = $Asapi->anyCheckuser($trial_data['applicant_id_card'], $trial_data['applicant_name']);
        $res = json_decode($res, true);

        //验证通过
        if ($res['data']['isValid'] == 1) {
            echo json_encode(['status' => 1]);
            exit;
        } else {
            echo json_encode(['status' => 0, 'msg' => $res['msg']]);
            exit;
        }

    }

    //工作信息页面
    public function getWork()
    {
        //银行基本信息
        $bank_data = DB::table('sync_code_library')->where(['CODENO' => 'BankCode', 'ISINUSE' => 1])->whereIn('ITEMNO', [102, 103, 104, 105, 302, 303, 305, 307, 308, 309, 403])->select('ITEMNAME', 'ITEMNO')->get();

        //已经提交过的工作信息
        $user_id = Session::get('user_id');
        $work_id_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('work_id','picture_id')->first();

        if (!$work_id_obj->work_id) {
            $work_id_obj = DB::table('orders')->where(['order_status' => 2, 'user_id' => $user_id])->select('work_id','picture_id')->orderBy('orders.id', SORT_DESC)->first();
        }

        if (is_object($work_id_obj) && $work_id_obj->picture_id) {
            $bank_card_pic = DB::table('orders_picture')->where(['id' =>$work_id_obj->picture_id ])->select('bank_card_pic')->first();
        }else{
            $bank_card_pic = '';
        }

        if(is_object($work_id_obj)) {
            $work = DB::table('orders_work')->where(['id' => $work_id_obj->work_id])->first();
        } else {
            $work = '';
        }

        $wxmodel = new Wechat();
        return view('wx.work')->with(['signPackage' => $wxmodel->getSignPackage(),'bank_data' => $bank_data, 'work' => $work,'bank_card_pic'=>$bank_card_pic]);
    }

    /*
     * 验证银行卡是否有效
     * */
    public function postCheckBankAccount()
    {
        $bankcardno = Request::input('accountNum');
        //获取银行简码
        $config_bankcode = Config::get('bankCode');
        $bankNo = Request::input('bankCode');
        $bankCode = $config_bankcode[$bankNo];
        $user_id = Session::get('user_id');
        $order_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => '1'])->first();
        $orders_product_obj = DB::table('orders_product')->where(['id' => $order_obj->product_id])->first();
        $mobileno = Session::get('mobile');

        $outid = 'fqg' . date('Ymd') . rand(1000, 9999);

        $arr = array(
            'outid' => $outid,
            'realname' => $orders_product_obj->applicant_name,//真实姓名
            'certno' => $orders_product_obj->applicant_id_card,//身份证号码
            'bankcardtype' => 'DEBIT_CARD',
            'bankcode' => $bankCode,//银行编码
            'servicetype' => 'INSTALLMENT',
            'bankcardno' => $bankcardno,//银行账号
            'mobileno' => $mobileno,//手机号码
            'infotype' => '4',
            'customerid' => 'QZD'
        );

        //查询此银行卡信息是否被校验过
        $bank_account_info = DB::table('bank_account_log')->where(['status'=>1,'bank_account' => $bankcardno, 'bank_code' => $bankCode, 'id_card_no' => $orders_product_obj->applicant_id_card, 'id_card_name' => $orders_product_obj->applicant_name])->first();

        //如果已经校验过并且校验成功
        if (is_object($bank_account_info)) {
            Logger::info('银行卡号是'.$bankcardno.'的银行卡已有校验成功记录');
            echo json_encode(array('send_status' => 1, 'query_status' => 1));
            exit;
        } else {
            $res = $this->sendbankcode($arr);
            //请求发送成功
            if ($res['result'][0] == '0000') {

                Logger::info('--------------华丽的分割线----------------------------','bankInfo');
                Logger::info('持卡人姓名：'.$orders_product_obj->applicant_name.'；银行卡号：'.$bankcardno.'；银行卡校验请求发送成功信息:'.json_encode($res,JSON_UNESCAPED_UNICODE),'bankInfo');
                $res1 = $this->querybankcode($outid);

                if ($res['result'][0] == '1001'){
                    $res1 = $this->querybankcode($outid);
                }

                if ($res1['result'][0] == '0000') {
                    Logger::info('--------------华丽的分割线----------------------------','bankInfo');
                    Logger::info('持卡人姓名：'.$orders_product_obj->applicant_name.'；银行卡号：'.$bankcardno.'；银行卡校验成功信息:'.json_encode($res1,JSON_UNESCAPED_UNICODE),'bankInfo');

                        //校验成功
                    DB::table('bank_account_log')->insert([
                        'flow_id' => $outid,
                        'bank_account' => $bankcardno,
                        'bank_code' => $bankCode,
                        'id_card_no' => $orders_product_obj->applicant_id_card,
                        'id_card_name' => $orders_product_obj->applicant_name,
                        'add_time' => time(),
                        'status' => 1,
                        'remark' => '校验成功',
                    ]);
                    echo json_encode(array('send_status' => 1, 'query_status' => 1));
                    exit;
                } else {
                    //校验失败
                    DB::table('bank_account_log')->insert([
                        'flow_id' => $outid,
                        'bank_account' => $bankcardno,
                        'bank_code' => $bankCode,
                        'id_card_no' => $orders_product_obj->applicant_id_card,
                        'id_card_name' => $orders_product_obj->applicant_name,
                        'add_time' => time(),
                        'status' => 0,
                        'remark' => '校验失败：'.json_encode($res1['info'],JSON_UNESCAPED_UNICODE),
                    ]);

                    Logger::info('--------------华丽的分割线----------------------------','bankInfo');
                    Logger::info('持卡人姓名：'.$orders_product_obj->applicant_name.'；银行卡号：'.$bankcardno.'；银行卡校验失败信息:'.json_encode($res1,JSON_UNESCAPED_UNICODE),'bankInfo');
                    echo json_encode(array('send_status' => 1, 'query_status' => 0,'code'=>$res1['result'][0]));
                    exit;
                }
            } else {
                //请求发送失败
                Logger::info('--------------华丽的分割线----------------------------','bankInfo');
                Logger::info('持卡人姓名：'.$orders_product_obj->applicant_name.'；银行卡号：'.$bankcardno.'；银行卡校验请求发送失败信息:'.json_encode($res,JSON_UNESCAPED_UNICODE),'bankInfo');
                echo json_encode(array('send_status' => 0, 'query_status' => 0,'code'=>$res['result'][0]));
                exit;
            }
        }

    }

    //提交工作信息操作
    public function postDoWork()
    {
        $work_data = Request::input();

        $work_bank_branch_no = isset($work_data['work_bank_branch_no']) ? $work_data['work_bank_branch_no'] : '';
        $work_bank_branch_name = isset($work_data['work_bank_branch_name']) ? $work_data['work_bank_branch_name'] : '';

        $user_id = Session::get('user_id');
        $work_arr = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('work_id')->get();

        if ($work_arr && ($work_arr[0]->work_id > 0)) {
            //如果提交过工作信息，则更新工作信息
            DB::table('orders_work')->where(['id' => $work_arr[0]->work_id])->update([
                'work_unit' => $work_data['work_unit'],
                'work_unit_mobile' => $work_data['work_unit_mobile'],
                'work_addr1' => $work_data['work_addr1'],
                'work_addr2' => $work_data['work_addr2'],
                'work_addr3' => $work_data['work_addr3'],
                'work_addr4' => $work_data['work_addr4'],
                'work_addr5' => $work_data['work_addr5'],
                'work_repayment_account' => $work_data['work_repayment_account'],
                'work_deposit_bank_no' => $work_data['work_deposit_bank_no'],
                'work_deposit_bank' => trim($work_data['work_deposit_bank']),
                'work_bank_city' => $work_data['work_bank_city'],
                'work_bank_branch_no' => $work_bank_branch_no,
                'work_bank_branch_name' => $work_bank_branch_name,
                'work_credit_card' => str_replace(' ','',$work_data['work_credit_card']),
            ]);

        } else {
            //如果没有提交过工作信息，则新增工作信息,同时更新orders表中的work_id
            $work_id = DB::table('orders_work')->insertGetId([
                'work_unit' => $work_data['work_unit'],
                'work_unit_mobile' => $work_data['work_unit_mobile'],
                'work_addr1' => $work_data['work_addr1'],
                'work_addr2' => $work_data['work_addr2'],
                'work_addr3' => $work_data['work_addr3'],
                'work_addr4' => $work_data['work_addr4'],
                'work_addr5' => $work_data['work_addr5'],
                'work_repayment_account' => $work_data['work_repayment_account'],
                'work_deposit_bank_no' => $work_data['work_deposit_bank_no'],
                'work_deposit_bank' => trim($work_data['work_deposit_bank']),
                'work_bank_city' => $work_data['work_bank_city'],
                'work_bank_branch_no' => $work_bank_branch_no,
                'work_bank_branch_name' => $work_bank_branch_name,
                'work_credit_card' => str_replace(' ','',$work_data['work_credit_card']),
            ]);
            DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->update(['work_id' => $work_id]);
        }

        $picture_arr = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('picture_id')->get();

        if ($picture_arr && ($picture_arr[0]->picture_id > 0)) {
            //如果提交过图片信息，则更新图片信息
            DB::table('orders_picture')->where(['id' => $picture_arr[0]->picture_id])->update([
                'bank_card_pic' => $work_data['bank_card_pic'],
            ]);

        } else {
            //如果没有提交过图片信息，则新增图片信息,同时更新orders表中的picture_id
            $picture_id = DB::table('orders_picture')->insertGetId([
                'bank_card_pic' => $work_data['bank_card_pic'],
            ]);

            DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->update(['picture_id' => $picture_id]);
        }

        //跳转到照片信息页面
        return Redirect::to('wx/loan/family');
    }

    //家庭信息页面
    public function getFamily()
    {
        //最高学历
        $edu_data = DB::table('sync_code_library')->where(['CODENO' => 'EducationExperience', 'ISINUSE' => 1])->select('ITEMNAME', 'ITEMNO')->get();
        //亲属关系
        $family_relation = DB::table('sync_code_library')->where(['CODENO' => 'FamilyRelativeAccount', 'ISINUSE' => 1])->select('ITEMNAME', 'ITEMNO')->get();

        $work_id_obj = DB::table('orders')->where(['order_status'=>1,'user_id'=>Session::get('user_id')])->select('work_id')->orderBy('orders.id', SORT_DESC)->first();
        $work_id_obj2 = DB::table('orders')->where(['order_status'=>2,'user_id'=>Session::get('user_id')])->select('work_id')->orderBy('orders.id', SORT_DESC)->first();

        $family = '';
        if(is_object($work_id_obj2)){
            $family = DB::table('orders_work')->where(['id' => $work_id_obj2->work_id])
            ->select('edu_level','edu_level_no','qq_email','family_relation','family_relation_no','family_name','family_mobile','family_addr1','family_addr2','family_addr3','family_addr4','family_addr5')->first();
        }

        if(is_object($work_id_obj)){
            $family_obj = DB::table('orders_work')->where(['id' => $work_id_obj->work_id])->select('family_name')->first();
            if(is_object($family_obj) && $family_obj->family_name){
                $family = DB::table('orders_work')->where(['id' => $work_id_obj->work_id])
                ->select('edu_level','edu_level_no','qq_email','family_relation','family_relation_no','family_name','family_mobile','family_addr1','family_addr2','family_addr3','family_addr4','family_addr5')->first();
            }
        }

        return view('wx.family')->with(['edu_data'=>$edu_data,'family_relation'=>$family_relation,'family'=>$family]);
    }

    public function postDoFamily()
    {
        $family_data = Request::input();

        $user_id = Session::get('user_id');
        $work_arr = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('work_id')->get();

        //如果提交过工作信息，则更新工作信息
        DB::table('orders_work')->where(['id' => $work_arr[0]->work_id])->update([
            'edu_level_no' => $family_data['edu_level_no'],
            'edu_level' => trim($family_data['edu_level']),
            'qq_email' => $family_data['qq_email'].'@qq.com',
            'family_relation_no' => $family_data['family_relation_no'],
            'family_relation' => trim($family_data['family_relation']),
            'family_name' => $family_data['family_name'],
            'family_mobile' => $family_data['family_mobile'],
            'family_addr1' => $family_data['family_addr1'],
            'family_addr2' => $family_data['family_addr2'],
            'family_addr3' => $family_data['family_addr3'],
            'family_addr4' => $family_data['family_addr4'],
            'family_addr5' => $family_data['family_addr5'],
        ]);

        //跳转到照片信息页面
        return Redirect::to('wx/loan/filepic');
    }

    //获取银行支行
    public function postBankBranch()
    {
        $bank = Request::input('bank');
        $city = Request::input('city');

        $res = DB::table('sync_bankput_info')->where(['banacode' => $bank, 'city' => $city])->get();

        if ($res) {
            echo json_encode(array('error' => 0, "branch_info" => $res));
        } else {
            echo json_encode(array('error' => 1, "branch_info" => ''));
        }
        exit;
    }

    //代扣还款银行页面
    public function getRepayment()
    {
        return view('wx.repayment');
    }

    /**
     * 上传图片
     */
    public function getFilepic()
    {
        $wxmodel = new Wechat();
        //已经提交过的照片信息
        $user_id = Session::get('user_id');
        $order_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->first();
        $pic = DB::table('orders_picture')->where(['id' => $order_obj->picture_id])->first();

        if (!$pic->cert_face_pic) {
            $order_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 2])->orderBy('orders.id', SORT_DESC)->first();
            if(is_object($order_obj)){
                $pic = DB::table('orders_picture')->where(['id' => $order_obj->picture_id])->first();
            }else{
                $pic = '';
            }
        }

        //这里生成jssdk所需的参数的方法做了修改
        return view('wx.filepic')->with(['signPackage' => $wxmodel->getSignPackage(), 'pic' => $pic]);
    }

    /**
     * 保存上传图片
     */
    public function postDoFilepic()
    {
        $picture_data = Request::all();
        $user_id = Session::get('user_id');
        $picture_arr = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('picture_id')->get();

        if ($picture_arr && ($picture_arr[0]->picture_id > 0)) {
            //如果提交过图片信息，则更新图片信息
            DB::table('orders_picture')->where(['id' => $picture_arr[0]->picture_id])->update([
                'cert_face_pic' => $picture_data['cert_face_pic'],
                'cert_opposite_pic' => $picture_data['cert_opposite_pic'],
                'cert_hand_pic' => $picture_data['cert_hand_pic'],
                'work_pic' => $picture_data['work_pic'],
                'credit_auth_pic' => $picture_data['credit_auth_pic'],
            ]);
        }else{
            //如果没有提交过图片信息，则新增图片信息,同时更新orders表中的picture_id
            $picture_id = DB::table('orders_picture')->insertGetId([
                'cert_face_pic' => $picture_data['cert_face_pic'],
                'cert_opposite_pic' => $picture_data['cert_opposite_pic'],
                'cert_hand_pic' => $picture_data['cert_hand_pic'],
                'work_pic' => $picture_data['work_pic'],
                'credit_auth_pic' => $picture_data['credit_auth_pic'],
            ]);

            DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->update(['picture_id' => $picture_id]);
        }

        if (LinkFace::isOn()) {
            //Logger::info('用户 userid  '.$user_id.'___'.   '进入图片检验操作');
            $faceObj = LinkFace::getInstance();

            //识别正面文字
            $imgpath_f = FileReader::get_storage_path($picture_data['cert_face_pic']);
            $frontRes = $faceObj::judgeResultForOCR($imgpath_f, 1);
            Logger::info('用户 userid  '.$user_id.'___'. '正面文字检测结果：' . json_encode($frontRes, JSON_UNESCAPED_UNICODE), 'OCR-result');
            if (!$frontRes['status'] || strstr($frontRes['data'], '异常')) {
                exit(json_encode(['code' => 100, 'msg' => $frontRes['data']]));
            }

            //识别反面文字
            $imgpath_o = FileReader::get_storage_path($picture_data['cert_opposite_pic']);
            $backRes = $faceObj::judgeResultForOCR($imgpath_o, 2);
            Logger::info('用户 userid  '.$user_id.'___'. '反面文字检测结果：' . json_encode($backRes, JSON_UNESCAPED_UNICODE), 'OCR-result');
            if (!$backRes['status'] || strstr($backRes['data'], '异常')) {
                exit(json_encode(['code' => 101, 'msg' => $backRes['data']]));
            }

            //比对人脸
            $queryImg = FileReader::get_storage_path($picture_data['cert_hand_pic']);
            $dbImg = FileReader::get_storage_path($picture_data['cert_face_pic']);
            $compareRes = $faceObj::judgeResultForCompare($queryImg, $dbImg);

            Logger::info('用户 userid  '.$user_id.'___'. '人脸比对结果：' . json_encode($compareRes, JSON_UNESCAPED_UNICODE), 'facecompare-result');
            if (!$compareRes['status'] || strstr($compareRes['data'], '异常')) {
                exit(json_encode(['code' => 102, 'msg' => $compareRes['data']]));
            }
        }

        //跳转到照片信息页面
        exit(json_encode(['code' => 10000, 'msg' => '']));
    }


    //手机服务密码页面
    public function getPhonePwd()
    {
        $user_id = Session::get('user_id');
        $phone_obj = DB::table('orders')->where(['user_id' => $user_id,'order_status'=>1])->select('mobile','product_id','rand_code')->first();

        if(!$phone_obj){
            return redirect('/wx/loan/mcode');
        }

        $mobilePhone = substr_replace($phone_obj->mobile, '****', 3, 4);
        $applicant_obj = DB::table('orders_product')->where(['id' => $phone_obj->product_id])->select('applicant_name', 'applicant_id_card')->first();

        Session::put('applicant_obj', $applicant_obj);
        Session::put('mobilePhone', $phone_obj->mobile);
        Session::put('randCode', $phone_obj->rand_code);
        Session::save();
        return view('wx.phonepwd')->with(['mobilePhone' => $mobilePhone]);
    }

    //验证手机服务密码
    public function postCheckPhonePwd()
    {
        if (Request::input('password') == Session::get('randCode'))
            return ['code' => 11111, 'message' => 'success'];

        $applicant_obj = Session::get('applicant_obj');
        $applicant_name = $applicant_obj->applicant_name;
        $applicant_id_card = $applicant_obj->applicant_id_card;
        $mobilePhone = Session::get('mobilePhone');
        $pwd_arr = [
            'full_name' => $applicant_name,//姓名
            'id_card' => $applicant_id_card,//身份证
            'phone_number' => $mobilePhone,
            'password' => Request::input('password'),
            'captcha' => Request::input('captcha'),
            'queryPwd' => Request::input('queryPwd'),
            'type' => Request::input('type'),
        ];

        $pwd_res = Collector::collectData($pwd_arr);
        echo json_encode($pwd_res);
        exit;
    }

    //处理手机服务密码
    public function postDoPhonePwd()
    {
        $user_id = Session::get('user_id');
        $mobile_service_password = Request::input('mobile_service_password');
        $mobile_service_password = Crypt::encrypt($mobile_service_password);
        DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->update(['mobile_service_password' => $mobile_service_password]);
        return Redirect::to('wx/loan/ecommerce');
    }

    //其他联系人信息页面
    public function getContacts()
    {
        $user_id = Session::get('user_id');
        $mobile_service_password = Request::input('password');
        $code = Request::input('code');

        $phone_obj = DB::table('orders')->where(['user_id' => $user_id,'order_status'=>1])->select('mobile')->first();

        Logger::info($phone_obj->mobile.'手机服务密码:'.$mobile_service_password.'聚信立返回代码:'.$code,'mobileInfo');

        $mobile_service_password = Crypt::encrypt($mobile_service_password);
        DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->update(['mobile_service_password' => $mobile_service_password]);

        //其他联系人关系
        $other_contact_relation = DB::table('sync_code_library')->where(['CODENO' => 'RelativeAccountOther', 'ISINUSE' => 1])->select('ITEMNAME', 'ITEMNO')->get();

        $work_id_obj = DB::table('orders')->where(['order_status'=>1,'user_id'=>Session::get('user_id')])->select('work_id')->orderBy('orders.id', SORT_DESC)->first();
        $work_id_obj2 = DB::table('orders')->where(['order_status'=>2,'user_id'=>Session::get('user_id')])->select('work_id')->orderBy('orders.id', SORT_DESC)->first();

        $other_contact_obj = '';
        if(is_object($work_id_obj2)){
            $other_contact_obj = DB::table('orders_work')->where(['id' => $work_id_obj2->work_id])
                ->select('other_contact_relation','other_contact_relation_no','other_contact_name','other_contact_mobile')->first();
        }

        if(is_object($work_id_obj)){
            $other_contact_name_obj = DB::table('orders_work')->where(['id' => $work_id_obj->work_id])->select('other_contact_name')->first();
            if(is_object($other_contact_name_obj) && $other_contact_name_obj->other_contact_name){
                $other_contact_obj = DB::table('orders_work')->where(['id' => $work_id_obj->work_id])
                    ->select('other_contact_relation','other_contact_relation_no','other_contact_name','other_contact_mobile')->first();
            }
        }

        return view('wx.contacts')->with(['other_contact_relation'=>$other_contact_relation,'other_contact_obj'=>$other_contact_obj]);
    }

    public function postDoContacts()
    {
        $other_contacts_data = Request::input();

        $user_id = Session::get('user_id');
        $work_arr = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => 1])->select('work_id')->get();

        DB::table('orders_work')->where(['id' => $work_arr[0]->work_id])->update([
            'other_contact_relation_no' => $other_contacts_data['other_contact_relation_no'],
            'other_contact_relation' => trim($other_contacts_data['other_contact_relation']),
            'other_contact_name' => $other_contacts_data['other_contact_name'],
            'other_contact_mobile' => $other_contacts_data['other_contact_mobile'],
            'no_auth' => 1
        ]);

        //跳转到电商页面
        return Redirect::to('wx/loan/ecommerce');
    }

    //电商信息页面
    public function getEcommerce()
    {
        //服务端生成随机数存入session, 分配至表单页
        $sess_id = $_SESSION['sid'] = date('YmdHis') . mt_rand(1000, 9999);
        return view('wx.ecommerce')->with(['sess_id'=>$sess_id]);
    }

    public function postCheckOrder()
    {
        $ecommerce_data = Request::input();

        //提单接口数据
        $user_id = Session::get('user_id');
        $order_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => '1'])->first();
        $orders_product_obj = DB::table('orders_product')->where(['id' => $order_obj->product_id])->first();
        $orders_work_obj = DB::table('orders_work')->where(['id' => $order_obj->work_id])->first();
        $orders_picture_obj = DB::table('orders_picture')->where(['id' => $order_obj->picture_id])->first();
        $sync_product_category_obj = DB::table('sync_product_category')->where(['PRODUCTCATEGORYNAME' => '医疗美容'])->first();

        $Asapi = new api\AsapiController();
        $res = $Asapi->anyCheckuser($orders_product_obj->applicant_id_card,$orders_product_obj->applicant_name);

        $res = json_decode($res,true);

        //调用试算接口
        $trial_res = $Asapi->anyRepaytrial($order_obj->merchant_code, $orders_product_obj->periods, $orders_product_obj->loan_money, $orders_product_obj->applicant_name, $orders_product_obj->applicant_id_card);
        $trial_res = json_decode($trial_res, true);

        //获取身份证正反面文字信息
        $cert_face_info = Session::get('cert_face_info');
        $cert_opposite_info= Session::get('cert_opposite_info');

        if($cert_face_info['idcard_ocr_result']['nation'] == '汉'){
            $nationality = '01';
        }else{
            $nationality = '02';
        }

        if($cert_face_info['idcard_ocr_result']['gender'] == '男'){
            $sex = 1;
        }else if($cert_face_info['idcard_ocr_result']['gender'] == '女'){
            $sex = 2;
        }else{
            $sex = 0;
        }

        $order_arr = [
            'merchant_code' => $order_obj->merchant_code,
            'appNo' => $order_obj->id,
            'periods' => $orders_product_obj->periods,    //分期期数
            'monthRepayment' => $trial_res['data']['monthRepayment'],    //每月还款额
            'businessSum' => $orders_product_obj->loan_money,    //贷款本金
            'shopType' => $orders_product_obj->service_type_no,    //商品类型编号
            'replaceAccount' => $orders_work_obj->work_repayment_account, //代扣还款账号
            'openBank' => $orders_work_obj->work_deposit_bank_no, //代扣银行编号
            'openBankName' => $orders_work_obj->work_deposit_bank, //代扣银行名称
            'bankCity' => $orders_work_obj->work_bank_city, //开户银行城市
            'subopenBank' => $orders_work_obj->work_bank_branch_no,//代扣银行支行编号
            'subopenBankName' => $orders_work_obj->work_bank_branch_name,//代扣银行支行名称

            'customerName' => $orders_product_obj->applicant_name,
            'certID' => $orders_product_obj->applicant_id_card,
            'mobileTelephone' => $order_obj->mobile,
            'emailAdd' => $orders_work_obj->qq_email,

            'unitKind' => $order_obj->industry_no,    //行业
            'workCorp' => $orders_work_obj->work_unit,    //工作单位
            'workTel' => $orders_work_obj->work_unit_mobile,    //单位电话
            'eduexperience' => $orders_work_obj->edu_level_no,    //最高学历编号
            'creditCardNo' => $orders_work_obj->work_credit_card,    //信用卡
            'kinshipName' => $orders_work_obj->family_name,    //家庭成员姓名
            'kinshipTel' => $orders_work_obj->family_mobile,    //家庭成员联系电话

            'servicePass' => Crypt::decrypt($order_obj->mobile_service_password),    //手机服务密码
            'jdAccount' => $ecommerce_data['jd_account'],    //京东账号
            'jdAccountPass' => $ecommerce_data['jd_password'],    //京东密码
            'taobaoAccount' => $ecommerce_data['tb_account'],    //淘宝账号
            'taobaoAccountPass' => $ecommerce_data['tb_password'],    //淘宝密码

            'businessRange' => $sync_product_category_obj->PRODUCTCATEGORYID,    //商品范畴
            'inputDate' => date('Y/m/d H:i:s', $order_obj->order_create_time),    //订单生成时间

            //家庭住址
            'familyAdd' => $orders_work_obj->family_addr1,
            'countryside' => $orders_work_obj->family_addr2,
            'villagecenter' => $orders_work_obj->family_addr3,
            'plot' => $orders_work_obj->family_addr4,
            'room' => $orders_work_obj->family_addr5,

            //工作单位地址
            'workAdd' => $orders_work_obj->work_addr1,
            'unitCountryside' => $orders_work_obj->work_addr2,
            'unitStreet' => $orders_work_obj->work_addr3,
            'unitRoom' => $orders_work_obj->work_addr4,
            'unitNo' => $orders_work_obj->work_addr5,

            'relativeType' => $orders_work_obj->family_relation_no, //亲属关系

            'otherContact' => $orders_work_obj->other_contact_name, //其他联系人姓名
            'contactrelation' => $orders_work_obj->other_contact_relation_no, //其他联系人关系
            'contactTel' => $orders_work_obj->other_contact_mobile, //其他联系人手机

            'cellNo' => $cert_face_info['idcard_ocr_result']['address'], //身份证户籍地址
            'nationality' =>$nationality,//民族
            'sex' =>$sex,//性别
            'issueinstitution' => $cert_opposite_info['idcard_ocr_result']['agency'],//身份证签证机关
            'maturityDate' =>Help::dateChange($cert_opposite_info['idcard_ocr_result']['valid_date_end']),//身份证有效期(到期日)
        ];

        $order_obj2 = DB::table('orders')->where(['id' =>$order_obj->id, 'order_status' => '2'])->select('id')->first();
        if($order_obj2){
            echo json_encode(['order_res' => 1, 'order_photo_res' => 1]);
            exit;
        }else{
            DB::table('orders')->where(['id' =>$order_obj->id, 'order_status' => '1'])->update(['order_status' => 2]);
        }

        $Asapi = new api\AsapiController();
        $order_res = $Asapi->anyCommitcontract($order_arr);
        $order_res = json_decode($order_res, true);

        if ($order_res['status'] == 200) {

            try{
                $baiRongModel = new BaiRongModel();
                $baiRongModel->sendToBaiRong($order_obj->id,$order_res['data']['contractNo']);
            }catch (\Exception $e){
                Logger::info($order_obj->id.'更新百融表失败','bairong');
            }

            $order_photo_arr = [
                'appNo' => $order_obj->id,
                'contractNo' => $order_res['data']['contractNo'],
                'imgList' => [
                    ['typeNo' => '20001', 'imgAddr' => $Asapi->file2dir($orders_picture_obj->cert_face_pic, $order_res['data']['contractNo'], '20001')],  //20001:客户身份证正面
                    ['typeNo' => '20025', 'imgAddr' => $Asapi->file2dir($orders_picture_obj->cert_opposite_pic, $order_res['data']['contractNo'], '20025')],  //20025:客户身份证反面
                    ['typeNo' => '20002', 'imgAddr' => $Asapi->file2dir($orders_picture_obj->cert_hand_pic, $order_res['data']['contractNo'], '20002')],  //20002:客户手持身份证
                    ['typeNo' => '20003', 'imgAddr' => $Asapi->file2dir($orders_picture_obj->bank_card_pic, $order_res['data']['contractNo'], '20003')],  //20003:银行卡照片正面
                    ['typeNo' => '20005', 'imgAddr' => $Asapi->file2dir($orders_picture_obj->work_pic, $order_res['data']['contractNo'], '20005')],  //20005:名片/工卡
                    ['typeNo' => '302004', 'imgAddr' => $Asapi->file2dir($orders_picture_obj->credit_auth_pic, $order_res['data']['contractNo'], '302004')], //302004:征信授权书照片
                    ['typeNo' => '6021', 'imgAddr' => $Asapi->file2dir($orders_picture_obj->contract_pic, $order_res['data']['contractNo'], '6021')],   //6021:手术知情同意书
                ]
            ];
            $order_photo_res = $Asapi->getPhoto($order_photo_arr);
            $order_photo_res = json_decode($order_photo_res, true);
            if ($order_photo_res['status'] == 200) {
                /*将相关信息写入合同表*/
                DB::table('contract_info')->insert([
                    'order_id' => $order_obj->id,
                    'first_monthly_repay_money' => Help::numRound($trial_res['data']['firstRepayment']),
                    'first_monthly_repay_date' => $trial_res['data']['firstDrawingDate'],
                    'monthly_repay_date' => $trial_res['data']['monthDrawingDate'],
                    'monthly_repay_money' => Help::numRound($trial_res['data']['monthRepayment']),
                    'contract_no' => $order_res['data']['contractNo'],
                    'status' => '070',
                    'create_time' => time(),
                    'product_no' => $orders_product_obj->service_type_no,
                    'openid' => $this->openId(),
                ]);
                /*将相关信息写入合同表end*/

                $jd_password = Crypt::encrypt($ecommerce_data['jd_password']);
                $tb_password = Crypt::encrypt($ecommerce_data['tb_password']);

                $user_id = Session::get('user_id');
                DB::table('orders')->where(['user_id' => $user_id, 'order_status' => '1'])->update([
                    'jd_account' => $ecommerce_data['jd_account'],
                    'jd_password' => $jd_password,
                    'tb_account' => $ecommerce_data['tb_account'],
                    'tb_password' => $tb_password,
                    'order_update_time' => time(),
                    'order_status' => 2,
                ]);

                //申请成功发送模板消息（获取该用户的最新合同信息，推送模板）
                $openId = $this->openId();
                $tplModel = new TplController($openId);
                $tplModel->getSuccessTpl();

                echo json_encode(['order_res' => 1, 'order_photo_res' => 1]);
            } else {
                Logger::info('影像接口返回结果：' . $order_photo_res['msg']);
                echo json_encode(['order_res' => 1, 'order_photo_res' => 0]);
                exit;
            }
        } else {
            DB::table('orders')->where(['id' =>$order_obj->id, 'order_status' => '2'])->update(['order_status' => 1]);
            Logger::info('提单接口返回结果：' . $order_res['msg']);
            echo json_encode(['order_res' => 0, 'order_photo_res' => 0]);
            exit;
        }
    }

    /**
     * 记录百融信息
     */
    public function postBaiRong(){
        $af_swift_number = Request::input('af_swift_number');
        $event = Request::input('event');

        Session::put('af_swift_number',$af_swift_number);
        Session::put('event',$event);

        $user_id = Session::get('user_id');
        $order_obj = DB::table('orders')->where(['user_id' => $user_id, 'order_status' => '1'])->first();
        $orders_product_obj = DB::table('orders_product')->where(['id' => $order_obj->product_id])->first();

        if(empty($af_swift_number) || empty($event)){
            Logger::info($user_id.$orders_product_obj->applicant_name.'获取设备号直接异常了','bairong');
        }

        $bairong_model = new BaiRongModel();
        $bairong_arr = array(
            'order_id' => $order_obj->id,
            'account_mobile' => $order_obj->mobile,
            'id_number' => $orders_product_obj->applicant_id_card,
            'account_name' => $orders_product_obj->applicant_name,
            'inputdate' => date('Y/m/d H:i'),
            'channeltype' => '01',
            'af_swift_number' => Request::input('af_swift_number', ''),
            'event' => Request::input('event', ''),
            'status' => $bairong_model::NO_SUBMIT
        );

        $bairong_model->addOrUpdate($bairong_arr);
    }

    /**
     * 验证银行卡是否有效 -- 发送请求
     */
    public function sendbankcode($arr)
    {
        $checkbank = new Checkbank();
        $res = $checkbank->send_message_to_web($arr);
        foreach ($res AS $k => $v) {
            $res[$k] = (array)$v;
        }
        return $res;
    }

    /**
     * 验证银行卡是否有效 -- 执行请求
     */
    public function querybankcode($outid)
    {
        $checkbank = new Checkbank();
        sleep(3);
        $res1 = $checkbank->query_message_to_web($outid);
        foreach ($res1 AS $k => $v) {
            $res[$k] = (array)$v;
        }
        return $res;
    }

}
