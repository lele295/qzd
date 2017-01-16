<?php


namespace App\Api\api;


use App\Model\Base\CommErrorModel;
use App\Service\base\AECCBC;
use App\Service\base\BLogger;
use App\Util\Curl;
use Illuminate\Support\Facades\Config;
/**
安硕接口API
 */
class SysApi
{
    private $ip ;
    public function __construct(){
        $this->ip = Config::get('myconfig.sys_ip');
    }

    /*
         * 存量客户验证接口
         * $name 用户姓名  $cardid身份证号
         */
    public function usercheck($name, $cardid)
    {
        $data = array("CustomerName"=>$name, "CertID"=>$cardid);
        $url = $this->ip."CheckCustomer";
        $response = Curl::curlGet($url ,$data,null,'origin');
        $response = json_decode(AECCBC::aes_decode($response));
        return $response;
    }


    /*
     * 获取参数接口
     * $loan_amount 贷款本金  $periods 期数  $eventname 活动名称
     * $product_type 产品类型 车主/现金
     * $customername 客户姓名  certid 身份证号   $city 城市
     * $iscycle是否投保
     */
    public function count_param_res($loan_amount, $periods, $eventname='', $product_type = 2, $customername = '池启仲', $certid = "350821199001163917", $city = '440300', $iscycle = '1')
    {
        $data = array("BusinessSum"=>$loan_amount, 'Periods'=>$periods, 'EventName'=>$eventname, 'ProductID'=>$product_type,
            'CustomerName'=>rawurlencode($customername), 'CertID'=>$certid, 'StoreCity'=>$city, 'CreditCycle'=>$iscycle);
        $url = $this->ip."GetLoanInfo";
//        $response = Curl::curlGet($url ,$data);
        $response = Curl::curlGet($url ,$data,null,'origin');
        $response = json_decode(AECCBC::aes_decode($response));
        return $response;
    }

    public function count_param_res_new($loan_amount, $periods, $eventid='', $product_type = 2, $customername, $certid, $city, $iscycle = '1'){
        $data = array("BusinessSum"=>$loan_amount, 'Periods'=>$periods, 'EventID'=>$eventid, 'ProductID'=>$product_type,
            'CustomerName'=>rawurlencode($customername), 'CertID'=>$certid, 'StoreCity'=>$city, 'CreditCycle'=>$iscycle);
        $url = $this->ip."GetLoanInfo";
        $response = Curl::curlGet($url ,$data,null,'origin');
        $response = json_decode(AECCBC::aes_decode($response));
        return $response;
    }

    /*
        * 合同提交注册接口（更新状态为已签署）
        * $serialNo 合同编号
        */
    public function loan_status_commit($serialNo)
    {
        $data = array("SerialNo"=>$serialNo);
        $url = $this->ip."UpdateContractStatus";
        $response = Curl::curlGet($url ,$data);
        return $response;
    }

    /*
     * 合同状态查询接口
     * $serialNo 合同编号
     */
    public function loan_status($serialNo)
    {
        $data = array("SerialNo"=>$serialNo);
        $url = $this->ip."GetContractStatus";
//        $response = Curl::curlGet($url ,$data,false,'json',true);
        $response = Curl::curlGet($url ,$data,false,'origin',true);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('合同查询('.$serialNo.'):'  . AECCBC::aes_decode($response));
        $response = json_decode(AECCBC::aes_decode($response));
        return $response;
    }

    /*
     * 合同当前审核流程状态查询(如取消会有原因)
     * $serialNo 合同编号
     */
    public function loan_audit_status($serialNo)
    {
        $data = array("SerialNo"=>$serialNo);
        $url = $this->ip."GetSignedTaskOpinion";
        $response = Curl::curlGet($url ,$data,false,'origin',true);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('合同当前审核流程状态('.$serialNo.'):'  . AECCBC::aes_decode($response));
        $response = json_decode(AECCBC::aes_decode($response));
        return $response;
    }

    /**
    * 撤销合同
    * @param $serialNo
    * @return mixed|null|string
    */
    public function loan_revoke($serialNo,$customerID){
        $data = array('SerialNo'=>$serialNo,'UserID'=>'jieqianmedsm');
        $url = $this->ip."RevokeContract?content=".json_encode($data,JSON_UNESCAPED_UNICODE);
        $response = Curl::curlGet($url, '',false,'origin',true);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('撤销合同('.$serialNo.'):' . AECCBC::aes_decode($response));
        $response = json_decode(AECCBC::aes_decode($response),true);
        return $response;
    }

    /*
         * 还款计划接口
         * $serialNo 合同编号
         */
    public function repayment_plan($serialNo)
    {
        $data = array("SerialNo"=>$serialNo);
        $url = $this->ip."SelPaymentSchedule";
//        $response = Curl::curlGet($url ,$data);
        $response = Curl::curlGet($url ,$data,null,'origin');
        $response = json_decode(AECCBC::aes_decode($response));
        if(isset($response->data)){
            return $response->data;
        }
        return false;
    }


    /**
     * @desc 下单接口
     * @param $formData 下单数据
     * @return mixed
     */
    public function submit_order($formData){
        if($this->isUrlHasNullParam($formData)){
            //异常抛出
            $formData = $this->urlEncodeArray($formData);
            BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单异常数据'  . http_build_query($formData));
            CommErrorModel::exceptionReturn('有必填参数为空');
        }
        $formData = $this->urlEncodeArray($formData);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单请求数据' . http_build_query($formData));
        $response = Curl::curlGet($this->ip . 'JSubmitContract',$formData,true);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单返回数据:');
        BLogger::getLogger(BLogger::LOG_ASAPI)->info($response);
        return $response;
    }


    public function uploadImageFile($data){
        $url = $this->ip . "UploadImageFile";
//        $url = 'http://dev.jieqianme.com/test/save-image';
        $response = Curl::curlPostForAs($url,$data);
//        $response = Curl::curlPost($url,$data);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单上传图片返回值:' . $response);
        return json_decode($response,true);
    }


    private function urlEncodeArray($array){
        foreach($array as $key=>$val){
            $array[$key] = urlencode($val);
        }
        return $array;
    }

    /**
     * 判断参数里面是否有空的值
     */
    private function isUrlHasNullParam($array){
        foreach($array as $key=>$val){
            if($val === '') {
                return true;
            }
        }
        return false;
    }

}