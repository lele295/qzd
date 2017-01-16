<?php
/**
 * Date: 2016/4/12
 * Time: 15:20
 */

namespace App\Http\Middleware;

use App\Log\Facades\Logger;
use App\Model\Base\AuthModel;
use App\Service\base\ApiService;
use App\Service\base\LoanService;
use App\Service\mobile\Service;
use App\Util\AppKits;
use App\Util\Kits;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class RegisterMobile
{
    public function handle($request, Closure $next){
        if(Auth::check()){
            $auth = new AuthModel();
            $res = $auth->get_auth_info_by_user_id(Auth::id());  //检测是否已通过身份（真实姓名和身份证号）验证
            if($res){
                if($res->SubProductType == 3){
                    return Redirect('/loan/car-message');
                }else {
                    ///TODO: 这个地方后期看看是否能够优化
                    $apiService = new ApiService();
                    $auth_message = $apiService->get_cust_auth_message($res->real_name,$res->id_card);
                    Logger::info($auth_message,'auth');
                    if(!$auth_message){
                        $loanService = new LoanService();
                        $loanService->cancel_order(Auth::id());
                        $source = Request::input('source', false);
                        if(Kits::isFqg()){
                            if(true !== ($brige = AppKits::bridgeCheck())){
                                return Redirect('/account/myloans');
                            }
                            return Redirect('/center/loan-stauts');
                        }else{
                            return Redirect('/m/user');
                        }
                    }
                    //2016-08-02：实名后的用户，更新auth表的CreditLimit、TopMonthPayment、ProductFeatures、EventID
                    $data = array();
                    $data = array_add($data, 'CreditLimit', $auth_message['CreditLimit']);
                    $data = array_add($data, 'ProductFeatures', $auth_message['ProductFeatures']);
                    $data = array_add($data, 'TopMonthPayment', $auth_message['TopMonthPayment']);
                    $data = array_add($data, 'EventID' , $auth_message['EventID']);
                    $auth->update_auth_info_by_user_id($data,Auth::id());

                    //2016-07-19添加：检测用户状态step_status，若为201更改为200。目的：解决已否决的单三个月之后可以办单的问题
                    $authModel = new AuthModel();
                    $authInfo = $authModel->get_auth_info_by_user_id(Auth::id());
                    if($authInfo->step_status == Service::LOAN_TO_SYS){
                        $authModel->update_auth_info_by_user_id(array('step_status'=>Service::RE_LOAN),Auth::id());
                    }
                    /*
                     * 101:填写贷款        102:个人资料      103:单位资料    104:上传图片        105:银行卡待审核    106:银行卡已打款
                     * 107:银行卡不通过     108:银行卡通过    109:Ca认证      200:重新填写贷款    201:已提单
                     *
                     */
                    $select["101"] = "/m/user/fillout-loan";
                    $select["102"] = "/loan/person-info";
                    $select["103"] = "/loan/firm-info";
                    $select["104"] = "/loan/file-pic";
                    $select["108"] = "/loan/protocol-info";
                    $select["109"] = "/loan/protocol-info";
                    $select["200"] = "/m/user/fillout-loan";
                    $select["201"] = "/m/user";
                    return Redirect($select[$res->step_status]);
                }
            }
        }
        return $next($request);
    }
}