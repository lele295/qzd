<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/9
 * Time: 17:04
 */

namespace App\Service\admin;


use App\Api\api\SysApi;
use App\Log\Facades\Logger;
use App\Model\Base\LoanSchedulesModel;

class ScheduleService extends Service
{
    private $userAdminMdoel;

    /*
     * 获取还款计划
     */
    public function get_api_schedules($pact_number){
        $asapi = new SysApi();
        $response = $asapi->repayment_plan($pact_number);
        if($response){
            $loanschedules = new LoanSchedulesModel();
            if($response[0]->Status == "Success"){
                $loanschedules->check_schedules($response, $pact_number);
                $arr = array("status"=>true, "msg"=>"更新成功");
            }else{
                $arr = array("status"=>false, "msg"=>"安硕接口失败");
            }
        }else{
            $arr = array("status"=>false, "msg"=>"获取安硕还款计划失败");
        }
        return $arr;
    }
}
