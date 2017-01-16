<?php

namespace App\Http\Controllers\wx;

use App\Http\Controllers\api\AsapiController;
use App\Log\Facades\Logger;
use App\Model\mobile\ContractModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ContractController extends Controller
{
    public function __construct()
    {
    }

    /**
     * 指定状态的合同信息
     * @param $status
     */
    public function getContractInfo($status)
    {
        $contractModel = new ContractModel();
        $data = $contractModel->get_contract_info($status);

        $contract_nos = '';
        if (!empty($data)) {
            foreach ($data as $v) {
                $contract_nos .= $v->contract_no . ',';
                $openids[$v->contract_no] = $v->openid;
            }
        }

        $contract_nos = substr($contract_nos, 0, -1);

        //请求合同状态接口
        $model = new AsapiController();
        $data = json_decode($model->anyContractstatus($contract_nos));
        //dd($openids);
        if ($data->status == 200){
            foreach ($data->data as $k => $v) {
                $openid = $openids[$v->contractNo];
                $tplModel = new TplController($openid);

                if($status=='070') {
                    switch ($v->contractStatus) {
                        //审批通过
                        case '080':
                            $rs = $tplModel->getExamTpl($v->contractNo);
                            if ($rs) {
                                $contractModel->update_contract_info_by_id($v->contractNo, '080');
                            }
                            break;
                        //已否决
                        case '010':
                            $rs = $tplModel->getRefTpl($v->contractNo);
                            if ($rs) {
                                $contractModel->update_contract_info_by_id($v->contractNo, '010');
                            }
                            break;
                        //已取消（页面可修改）
                        case '100':
                            //请求接口，得到取消合同的原因
                            $data = $model->anyContractstatus($v->contractNo);
                            $result = json_decode($data)->data[0]->reMarks;
                            if(is_null($result)){
                                $result = json_decode($data)->data[0]->cancelReason;
                            }
                            Logger::info('取消原因：'.json_encode($data,JSON_UNESCAPED_UNICODE),'cancel-contract');
                            $rs = $tplModel->getConcelTpl($v->contractNo,$result);
                            Logger::info('模板发送结果：'.$rs,'cancel-contract');
                            if($rs){
                                $contractModel->update_contract_info_by_id($v->contractNo, '100');
                            }
                            break;
                        //其他状态
                        default:
                    }

                }elseif($status=='080'){
                    switch ($v->contractStatus) {
                        case '100':
                            $contractModel->update_contract_info_by_id($v->contractNo, '100');
                            break;
                        case '020':
                            $contractModel->update_contract_info_by_id($v->contractNo, '020');
                            break;
                        default:
                    }

                }elseif($status=='020'){
                    switch ($v->contractStatus) {
                        case '050':
                            $contractModel->update_contract_info_by_id($v->contractNo, '050');
                            break;
                        case '210'://撤销
                            $contractModel->update_contract_info_by_id($v->contractNo, '210');
                            break;
                        default:
                    }

                }
                //elseif($status=='050'){
                //    Logger::info('请求安硕接口状态050：'.$v->contractNo.'---'.$status.'---'.$v->contractStatus,'tpl-status');
                //    $contractModel->update_contract_info_by_id($v->contractNo, $v->contractStatus);
                //}
            }
        }
    }


    /**
     * 定时任务，取050合同信息更新安硕状态，每天晚上12点执行
     * 批量数据太多，导致请求失败，将合同为050，单独重新批量请求处理（每次100合同批量请求接口）
     * @param $status
     */
    public function getStatusInfo($status){

        $contractModel = new ContractModel();
        //总的050数据
        $data = $contractModel->get_contract_info($status);
        $num = ceil(count($data)/100);

        for($i=0;$i<$num;$i++){
            $contract_nos= '' ;
            $rs = $contractModel->get_onehundred_contract_info($i,$status);
            foreach($rs as $v){
                $contract_nos .= $v->contract_no . ',';
                $openids[$v->contract_no] = $v->openid;
            }

            $contract_nos = substr($contract_nos, 0, -1);
            //请求合同状态接口
            $model = new AsapiController();
            $data = json_decode($model->anyContractstatus($contract_nos));
            
            if ($data->status == 200){
                foreach ($data->data as $k => $v) {
                    //$openid = $openids[$v->contractNo];
                    //$tplModel = new TplController($openid);
                    if($v->contractStatus!='050'){
                        Logger::info('050合同状态更新：'.$v->contractNo.'---'.$v->contractStatus,'tpl-status');
                        $contractModel->update_contract_info_by_id($v->contractNo, $v->contractStatus);
                    }
                }
            }
        }
    }

}
