<?php
namespace App\Http\Controllers\wx;



use App\Http\Controllers\api\AsapiController;
use App\Http\Controllers\Controller;
use App\Model\mobile\ContractModel;
use App\Model\mobile\UserModel;
use App\Service\api\CaApiService;
use App\Service\base\DocumentService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class SignController extends Controller
{
    private $_openId;
    public function __construct(){
        $this->_openId = $this->openId();
        //通过openid获取用户信息
        $model = new UserModel();
        $user_info = $model->get_user_info_by_openid($this->_openId);

        if($user_info){
            $user_id = $user_info->id;
            Session::put('user_id',$user_id);
            Session::save();
        }
    }


    /**
     * 签署协议
     * @return $this
     */
    public function getProtocolInfo(){
        //获取合同号存在session
        $contractNo = Request::input('contractNo');
        Session::put('contractNo',$contractNo);
        $user_id = Session::get('user_id');

        $contractModel = new ContractModel();
        $rs = $contractModel->get_contract_info_by_id($contractNo);

        //如果合同号为080，审批通过的则可以签署
        if($rs){
            if($rs->status != '080'){
                return Redirect::to('wx/order/list');
            }
        }

        $documentService = new DocumentService();
        $res = $documentService->get_new_order_deal($user_id,$contractNo);
        $order_id = $res['order_id'];


        $info = $documentService->get_data_to_application($order_id,$contractNo);
        if($info['contract_types'] == '2015011700000003'){
            //中信信托有限责任公司
            $flag = 'zx';
        }else if($info['contract_types']  == '2014060300000001'){
            //中泰信托有限责任公司
            $flag = 'zt';
        }

        return view("mobile/loan/protocolInfo",$info)->with('flag',$flag);
    }


    /**
     * 发送确认码
     * @return string
     */
    public function postProtocolInfo()
    {
        $documentService = new DocumentService();
        $contractNo = Session::get('contractNo');
        $user_id = Session::get('user_id');

        //根据用户id获取用户相关信息
        $res = $documentService->get_new_order_deal($user_id,$contractNo);
        $mobile = $res['order_data']->mobile;
        $order_id = $res['order_id'];

        //挑战码获取
        $caApiService = new CaApiService();
        $result = $caApiService->send_challenge_code_to_ca_by_order_id($order_id,$contractNo);

        if($result['status']){
            return json_encode(array("status" => true, "msg" => "成功",'mobile' => substr_replace($mobile,'****',3,4)));
        }else{
            return json_encode(array("status" => false, "msg" => $result['data']['message']));
        }
    }


    /**
     * 确认签署协议
     * @return string
     */
    public function postCheckCa()
    {
        $contractNo = Session::get('contractNo');
        $mobile_code = Request::input('mobile_code', false);

        //根据合同号获取用户order_id
        $contractModel = new ContractModel();
        $info = $contractModel->get_contract_info_by_id($contractNo);
        $order_id = $info->order_id;

        //防止用户返回，反复点击确认按钮
        if($info->status=='020' || $info->status=='050'){
            return json_encode(array("status" => false, "msg" => "您已签署"));
        }

        $caApiService = new CaApiService();
        $result = $caApiService->check_ca_challenge_code_from_user($order_id,$mobile_code);
        
        if($result['status']){
            //更新合同状态为已签署020
            $contractModel->update_contract_info_by_order_id($order_id,'020');
            //提交注册已签署合同
            $asModel = new AsapiController();
            $data = $asModel->anyCommit($contractNo);

            return json_encode(array("status" => true, "msg" => $result['data']['message']));
        }else{
            return json_encode(array("status" => false, "msg" => $result['data']['message']));
        }
    }

}
