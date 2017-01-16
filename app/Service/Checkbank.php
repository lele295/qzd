<?php
namespace App\Service;

use Illuminate\Support\Facades\Log;

class Checkbank{
    private $bank_no_webService;
    private $bank_no_user_name;
    private $bank_no_user_pwd;

    public function __construct(){
        //$this->bank_no_webService = "http://10.28.1.20:7001/payment/service/ValidateAccount?wsdl";
        $this->bank_no_webService = "http://10.25.1.21:8003/bqdk/service/ValidateAccount?wsdl";
        $this->bank_no_user_name = "fqg";
        $this->bank_no_user_pwd = "1F143C6A1F69B68F";
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
        //Logger::info('发送报文内容为：');
        //Logger::info($str);
        return $str;
    }
    
    //解析XML文件
    private function exceptXml($str){
        //Logger::info('返回银行卡帐户认证信息为：');
        //Logger::info($str);
        $xml = simplexml_load_string($str);
        $info = $xml->info;
        $resultcode = isset($xml->resultcode)?$xml->resultcode:'';
        $result = $xml->result;
        //Logger::info('resultcode为:'.$resultcode);
        return array('result'=>$result,'info'=>$info,'resultcode'=>$resultcode);
    }

    public function send_message_to_web($array){
        try {
            //Logger::info($this->bank_no_webService,'bank');
            libxml_disable_entity_loader(false);
            $client = new \SoapClient($this->bank_no_webService, array('trace' => true, 'exceptions' => true));
            $xml = $this->create_xml_str($array);
            $data = array('in0' => $this->bank_no_user_pwd, 'in1' => $this->bank_no_user_name, 'in2' => $xml);
            $scs = $client->__soapCall('validate', array('parameters' => $data));
            if($scs){
                $response = $this->exceptXml($scs->out);
                return $response;
            }else{
                return false;
            }
        }catch (\SoapFault $e){
            Log::info($e);
            return '9999';
        }
    }

    public function query_message_to_web($out_id){
        try {
            libxml_disable_entity_loader(false);
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
            Log::info($e);
            return false;
        }
    }
}

?>