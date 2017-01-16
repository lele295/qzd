<?php
namespace App\Api\api;

use App\Log\Facades\Logger;
use App\Service\base\AECCBC;
use App\Util\Curl;
use Illuminate\Support\Facades\Config;

class UserGroupApi{
    private $ip='';

    public function __construct(){
        $this->ip = Config::get('myconfig.user_group_ip');
    }


    public function user_baiqian_customer($cert_id,$tel)
    {
        $data = array("CertID"=>$cert_id,"Telephone"=>$tel);
        $url = $this->ip."EGetCustomerInfo";
        $response = Curl::curlPostForAs($url ,$data);
        $response = json_decode(AECCBC::aes_decode($response));
        return $response;
    }
}