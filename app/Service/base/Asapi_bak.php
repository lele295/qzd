<?php
/* 
 * 安硕api model
 */
namespace App\Service\base;
use App\Log\Facades\Logger;
use App\Model\Base\CommErrorModel;
use App\Util\Curl;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class Asapi{
    private $_http_server;
    public function __construct(){
        if(App::environment('product')) {
            $this->_http_server = Config::get('myconfig.sys_ip');
        }else{
            $this->_http_server = Config::get('myconfig.sys_ip');
        }
    }
    /**
     * 2016-02-18
     * @desc 下单接口
     * @param $formData 下单数据
     * @return mixed
     */
    public function submit_loan($formData){
        $formData = $this->urlEncodeArray($formData);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单请求数据' . http_build_query($formData));
        $response = Curl::curlGet($this->_http_server . 'JSubmitContract',$formData,false,'origin');
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单返回数据:');
        BLogger::getLogger(BLogger::LOG_ASAPI)->info($response);
        $response = json_decode(AECCBC::aes_decode($response),true);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单返回数据(解密):');
        BLogger::getLogger(BLogger::LOG_ASAPI)->info($response);
        if(isset($response['data'])){
            return $response['data'][0];
        }
        return $response;
    }

    /**
     * @desc 下单接口
     * @param $formData 下单数据
     * @return mixed
     */
    public function submit_order($formData,$orderId = '00'){
        Logger::info(array($formData));
        if($this->isUrlHasNullParam($formData)){
            //异常抛出
            $formData = $this->urlEncodeArray($formData);
            BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单异常数据'  . http_build_query($formData));
            Logger::error('下单异常数据'  . http_build_query($formData));
            Logger::error('有必填参数为空');
            CommErrorModel::exceptionReturn('有必填参数为空');
        }
        $formData = $this->urlEncodeArray($formData);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单请求数据' . http_build_query($formData));
        $response = Curl::curlGet($this->_http_server . 'JSubmitContract',$formData,false,'origin');
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单返回数据:');
        BLogger::getLogger(BLogger::LOG_ASAPI)->info($response);
        $response = json_decode(AECCBC::aes_decode($response),true);
        BLogger::getLogger(BLogger::LOG_ASAPI)->info('下单返回数据(解密):');
        BLogger::getLogger(BLogger::LOG_ASAPI)->info($response);
        if(isset($response['data'])){
            return $response['data'][0];
        }
        return $response;
    }

    /**
     * 上传资料图片
     */
    public function uploadImageFile($data){
        $url = $this->_http_server . "UploadImageFile";
        $response = Curl::curlPostForAs($url,$data);
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
                Logger::error($key."字段不能为空");
                return true;
            }
        }
        return false;
    }

    /**
     * @param $array
     * @return bool
     */
    private function trimData($array){
        foreach($array as $key=>$val){
            $val = trim($val);
            $val = str_replace("'","",$val);
            $array[$key] = str_replace('"',"",$val);
        }
        return $array;
    }

}

