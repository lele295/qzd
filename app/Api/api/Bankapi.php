<?php

namespace App\Api\api;
use App\Log\Facades\Logger;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class  Bankapi{
    private $webService;
    private $user_name;
    private $user_pwd;

    public function __construct(){
        $this->webService = Config::get('myconfig.bank_web_service');
        $this->user_name = Config::get('myconfig.bank_user_name');
        $this->user_pwd = Config::get('myconfig.bank_user_pwd');
    }

    //组装XML文件
    public function createXml($array){
        $str = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>"
            ."<SinglePay>"
            ."<subdate>".date('Ymd',time())."</subdate>"
            ."<businessdate>".date('Ymd',time())."</businessdate>"
            ."<outid>".$array['outid']."</outid>"
            ."<contranctno></contranctno>"
            ."<customerid>".$array['customerid']."</customerid>"
            ."<paychannel>1</paychannel>"
            ."<infotype>2</infotype>"
            ."<payamt>".$array['payamt']."</payamt>"
            ."<currency>CNY</currency>"
            ."<payacctno>".$array['payacctno']."</payacctno>"
            ."<payacctname>".$array['payacctname']."</payacctname>"
            ."<payacctbankno>".$array['payacctbankno']."</payacctbankno>"
            ."<payacctbankname>".$array['payacctbankno']."</payacctbankname>"
            ."<acpbankno>".$array['acpbankno']."</acpbankno>"
            ."<acpbankname>".$array['acpbankno']."</acpbankname>"
            ."<acpacctno>".$array['acpacctno']."</acpacctno>"
            ."<acpacctname>".$array['acpacctname']."</acpacctname>"
            ."</SinglePay>";
        Logger::info('发送报文内容为：');
        Logger::info($str);
        return $str;
    }
    //解析XML文件
    public function exceptXml($str){
        Logger::info('返回银行卡认证信息为：');
        Logger::info($str);
        $xml = simplexml_load_string($str);
        return $xml->result;
    }

    public function sent_message_to_web($array){
        try {
            if(is_array($array)){
                $client = new \SoapClient($this->webService, array('trace' => true, 'exceptions' => true));
                $xml = $this->createXml($array);
                $data = array('in0' => $this->user_pwd, 'in1' => $this->user_name, 'in2' => $xml);
                $scs = $client->__soapCall('pay', array('parameters' => $data));
                Logger::info((array)$scs);
                if($scs){
                    $response = $this->exceptXml($scs->out);
                    return $response;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }catch (Exception $e){
            Logger::info('银行卡认证webservice出现错误'.$this->webService);
            return false;
        }
    }

    public function query_message_to_web($out_id){
        $client = new \SoapClient($this->webService, array('trace' => true, 'exceptions' => true));
        $data = array('user_pwd' => $this->user_pwd, 'user_name' => $this->user_name, 'serialno' => $out_id);
        $scs = $client->__soapCall('pay', array('parameters' => $data));
        if($scs){
            $response = $this->exceptXml($scs->out);
            return $response;
        }else{
            return false;
        }
    }
}