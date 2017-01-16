<?php
namespace App\Service\mobile;

use App\Log\Facades\Logger;
use App\Model\Base\LoanModel;
use App\Model\mobile\WechatModel;

class WeTemplateService extends Service {

    /*
     * 根据安硕合同号发送还款信息
     * $contract_no_as 安硕合同号
     * $period 期数
     */
    public function send_replay_info($contract_no_as, $PayDate, $ActualTotalAmt)
    {
        if($ActualTotalAmt == "0"){
            $info_error = "编号".$contract_no_as."获取实际还款额失败";
            Logger::info($info_error);
            return false;
        }
        $loanmodel = new LoanModel();
        if(env("APP_ENV") == "product")
        {
            $template_id = "TuLsXmds20RR6RY8zg4SE-KWl2D_xI1vrZLtQf4myT4";
        }else{
            $template_id = "dpje8pI5obBtJCo3cVzfkAIGqMXa_0BV-lWx51xNVz0";
        }
        $user_info = $loanmodel->sel_join_users_pact_number($contract_no_as);
        $arr["key1"] = $PayDate;
        $arr["key2"] = $ActualTotalAmt."元";
        $arr["remark"] = "点击查看详情";
        $wx_res = $this->template($user_info->openid, $template_id, $arr);
        if(!$wx_res){
            $info_error = "编号".$contract_no_as."发送微信还款信息失败";
            Logger::info($info_error);
            return false;
        }
        return $wx_res;
    }


    /*
     * 微信发送模板
     * $openid 用户openid
     * $template_id 模板编号
     * $arr 发送数组
     * $is_url 是否需要url
     */
    public function template($openid, $template_id, $arr, $is_url = true){
        $url = "";
        if($is_url) {
            $url = '"url":'.'"http://'.env("JS_URL").'/users/register?openid='.$openid.'",';
        }
        $data = '';
        $inta = 1;
        foreach($arr as $i=>$val){
            if($i == 'title'){
                $data .= '"first": {"value":"'.$val.'\n", "color":"#000000"},';
            }elseif($i == "remark"){
                $data .= '"remark": {"value":"\n'.$val.'", "color":"#999999"}';
            }else{
                $data .= '"keyword'.$inta.'":{"value":"'.$val.'", "color":"#173177"},';
                $inta++;
            }
        }
        $content='{
               "touser":"'.$openid.'",
               "template_id":"'.$template_id.'",
               '.$url.'
               "topcolor":"#FF0000",
               "data":{'.$data.'}
            }';
	    Logger::info($content,'wechat');
        $wechatmodel = new WechatModel();
        $wx_res = $wechatmodel->send_wxmessage($content);
        return $wx_res;
    }

    //获取贷款审核通知模板
    public function audit_loan_wt(){
        if(env("APP_ENV") == "product")
        {
            $template_id = "A2FZGFvjiSVI1bHlghuUAtkpRjwGSAJQPNnDESXICEY";
        }else{
            // $template_id = "I7FRT2ucoNF-e6ZTfJ_7jvfQ2lJpcJSxQ6H7fgvinmM";
            $template_id = "1piacn8I7WlinoxWHtHdsuYfJU792HItBkwlTO-w_rI";
        }
        return $template_id;
    }

    //获取现金贷申请反馈通知模板
    public function cash_apply_wt(){
        if(env("APP_ENV") == "product")
        {
            $template_id = "BqHAF4GNNzSfKqfd01XNJ9nul-KhjAb3G8sbo3c9Cc0";
        }else{
            // $template_id = "IBHyie26SCTNhtXUZshwwOMe3VNBLvp-SwqG98iUbY8";
            $template_id = "IaBSiumtOwjm2Ycopnv6Mp42Xhktu5xiNHFiiE2TJVA";
        }
        return $template_id;
    }
}
