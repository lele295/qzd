<?php
namespace App\SysInterface;
use App\Log\Facades\Logger;
use App\Service\SysInterface\CaService;

class CaInterface extends BaseInterface
{
    public function __construct(){

    }

    public function  get_constructno_to_ca($loan_id){
        $caService = new CaService();
        $info = $caService->get_data_to_ca_interface($loan_id);
        if(!empty($info)){
            return json_encode(array('status'=>true,'data'=>$info->pact_number));
        }else{
            return json_encode(array('status'=>false,'data'=>'没有该条订单'));
        }
    }

    public function get_loan_id_by_constructno($constructno){
        $caService = new CaService();
        $info = $caService->gte_loan_id_to_ca_interface($constructno);
        if(!empty($info)){
            return json_encode(array('status'=>true,'data'=>$info->id));
        }else{
            return json_encode(array('status'=>false,'data'=>'没有该条订单'));
        }
    }

    public function get_loan_id_list_by_constructno($constructno){
        str_replace(',',',',$constructno);
        str_replace('，',',',$constructno);
        $constructno = explode(',',$constructno);
        Logger::info($constructno);
        $caService = new CaService();
        $info = $caService->get_loan_id_list_to_ca_interface($constructno);
        $array = array();
        foreach($info as $val){
            $array = array_add($array,$val->pact_number,$val->id);
        }
        if($array){
            $data = json_encode(array('status'=>true,'data'=>$array));
        }else{
            $data = json_encode(array('status'=>false,'data'=>'数据为空'));
        }
        Logger::info($data);
        return $data;
    }
}