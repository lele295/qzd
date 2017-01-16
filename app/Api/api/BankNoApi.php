<?php
namespace App\Api\api;


use App\Log\Facades\Logger;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PhpSpec\Exception\Exception;
use Psy\Exception\FatalErrorException;

class BankNoApi{
    private $bank_no_webService;
    private $bank_no_user_name;
    private $bank_no_user_pwd;

    public function __construct(){
        $this->bank_no_webService = Config::get('myconfig.bank_no_web_service');
        $this->bank_no_user_name = Config::get('myconfig.bank_no_user_name');
        $this->bank_no_user_pwd = Config::get('myconfig.bank_no_user_pwd');
    }

    private function create_xml_str($array){
        $str = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>"
            ."<validate>"
	        ."<outid>".$array['outid']."</outid>"
	        ."<realname>".$array['realname']."</realname>"
	        ."<certno>".$array['certno']."</certno>"
	        ."<bankcardtype>".$array['bankcardtype']."</bankcardtype>"
	        ."<bankcode>".$array['bankcode']."</bankcode>"
	        ."<servicetype>".$array['servicetype']."</servicetype>"
	        ."<bankcardno>".$array['bankcardno']."</bankcardno>"
	        ."<mobileno>".$array['mobileno']."</mobileno>"
	        ."<infotype>".$array['infotype']."</infotype>"
	        ."<customerid>".$array['customerid']."</customerid>"
            ."</validate>";
        Logger::info('发送报文内容为：');
        Logger::info($str);
        return $str;
    }
    //解析XML文件
    private function exceptXml($str){
        Logger::info('返回银行卡帐户认证信息为：','bank');
        Logger::info($str,'bank');
        $xml = simplexml_load_string($str);
        $info = $xml->info;
        $resultcode = isset($xml->resultcode)?$xml->resultcode:'';
        $result = $xml->result;
        Logger::info('resultcode为:'.$resultcode,'bank');
        return array('result'=>$result,'info'=>$info,'resultcode'=>$resultcode);
    }

    public function send_message_to_web($array){
        try {
            Logger::info($this->bank_no_webService,'bank');
            $client = new \SoapClient($this->bank_no_webService, array('trace' => true, 'exceptions' => true));
            $xml = $this->create_xml_str($array);
            $data = array('in0' => $this->bank_no_user_pwd, 'in1' => $this->bank_no_user_name, 'in2' => $xml);
            $scs = $client->__soapCall('validate', array('parameters' => $data));
            Logger::info('银行卡认证结果为：','bank');
            Logger::info((array)$scs,'bank');
            if($scs){
                $response = $this->exceptXml($scs->out);
                return $response;
            }else{
                return false;
            }
        }catch (\SoapFault $e){
            Logger::error('银行卡认证webservice出现错误SoapFault'.$this->bank_no_webService,'bank');
            Logger::error('银行卡认证webservice出现错误SoapFault'.$this->bank_no_webService,'yunwei');
            Log::info($e);
            return '9999';
        }
    }

    public function query_message_to_web($out_id){
        try {
            Logger::info('流水号为：' . $out_id,'bank');
            $client = new \SoapClient($this->bank_no_webService, array('trace' => true, 'exceptions' => true));
            $data = array('in0' => $this->bank_no_user_pwd, 'in1' => $this->bank_no_user_name, 'in2' => $out_id);
            $scs = $client->__soapCall('query', array('parameters' => $data));
            if ($scs) {
                $response = $this->exceptXml($scs->out);
                return $response;
            } else {
                return false;
            }
        }catch (\SoapFault $e){
            Logger::error('银行卡查询结果出现异常，流水号为：'.$out_id,'bank');
            Logger::error('银行卡查询结果出现异常，流水号为：'.$out_id,'yunwei');
            Log::info($e);
            return false;
        }
    }
}