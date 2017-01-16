<?php

namespace App\Http\Controllers\wx;


use App\Log\Facades\Logger;
use App\Model\Base\TplModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service\Wechat;
use Illuminate\Support\Facades\Cache;


//根据openid发送模板消息
class TplController extends Controller
{

    private $_openId;
    private $_access_token;
    private $_uri;
    private $_url;
    public function __construct($openId)
    {
        $this->_url = config('wx.wx_url');
        $this->_openId = $openId;
        //获取token
        $wemodel = new Wechat();
        $this->_access_token = $wemodel->get_access_token();
        $this->_uri = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->_access_token";
    }

    /*
    {{first.DATA}}
    申请内容：{{keyword1.DATA}}
    申请时间：{{keyword2.DATA}}
    {{remark.DATA}}
     */
    //申请成功通知
    public function getSuccessTpl()
    {
        $uri = $this->_uri;
        $openId = $this->_openId;
        $res = TplModel::get_send_success_tpl_info($openId);
        Logger::info(date('Y-m-d H:i:s',time()),'successtpl');
        Logger::info($openId,'successtpl');
        if($res->order_create_time){
            $date = date('Y-m-d',$res->order_create_time);
        }else{
            $date = 0;
        }

        $data = ' {
           "touser":"'.$openId.'",
           "template_id":"tmhou2G9Q9JMfshwCA3o5ReFZWrM8Hb7mecommE0lnY",
           "url":"",
           "data":{
               "first": {
                   "value":"恭喜您，申请成功！",
                   "color":"#173177"
               },
               "keyword1":{
                   "value":"'.$res->service_type.'",
                   "color":"#173177"
               },
               "keyword2": {
                   "value":"'.$date.'",
                   "color":"#173177"
               },
               "remark":{
                   "value":"请保持电话畅通，我们会致电本人确认申请！",
                   "color":"#173177"
               }
           }

        }';
        $ret = $this->http_post($uri,$data,true);
        Logger::info($ret,'successtpl');
        return $ret;
    }

    /*
    {{first.DATA}}
    审批金额：{{keyword1.DATA}}
    审批期限：{{keyword2.DATA}}
    审批时间：{{keyword3.DATA}}
    {{remark.DATA}}
    */
    //审核通过通知
    public function getExamTpl($contractNo)
    {
        $uri = $this->_uri;
        $openId = $this->_openId;
        $res = TplModel::get_send_exam_tpl_info($contractNo);

        if($res->audit_time){
            $date = date('Y-m-d',$res->audit_time);
        }else{
            $date = date('Y-m-d',time());
        }

        $data = ' {
           "touser":"'.$openId.'",
           "template_id":"3ouIFNQGJk7l_fx5R0utyWOqz5EPNY3LOPJDmMR5JjU",
           "url":"'.$this->_url.'/sign/protocol-info?contractNo='.$contractNo.'",
           "data":{
               "first": {
                   "value":"您好，根据您的申请条件，订单审核已通过，请在12小时内及时确认是否接受审批结果，超时系统将自动取消订单。",
                   "color":"#173177"
               },
               "keyword1":{
                   "value":"'.$res->loan_money.' 元",
                   "color":"#173177"
               },
               "keyword2": {
                   "value":"'.$res->periods.' 期",
                   "color":"#173177"
               },
               "keyword3": {
                   "value":"'.$date.'",
                   "color":"#173177"
               },
               "remark":{
                   "value":"请点击详情确认审批结果。",
                   "color":"#173177"
               }
           }

        }';

        $ret = $this->http_post($uri,$data,true);
        return $ret;
    }
    //逾期通知
    //借款还款提醒

    /*
    {{first.DATA}}
    审批金额：{{keyword1.DATA}}
    审批期数：{{keyword2.DATA}}
    审批时间：{{keyword3.DATA}}
    {{remark.DATA}}
    */
    //审核拒绝通知
    public function getRefTpl($contractNo)
    {
        $uri = $this->_uri;
        $openId = $this->_openId;
        $res = TplModel::get_send_exam_tpl_info($contractNo);

        if($res->audit_time){
            $date = date('Y-m-d',$res->audit_time);
        }else{
            $date = date('Y-m-d',time());
        }

        $data = ' {
           "touser":"'.$openId.'",
           "template_id":"Lbyctgu9z2TE9bcxoXQ1Q8Lfsh3QC1iv-E_VdLamiMQ",
           "url":"",
           "data":{
               "first": {
                   "value":"您好，根据您的申请条件，订单审核已被拒绝。",
                   "color":"#173177"
               },
               "keyword1":{
                   "value":"'.$res->loan_money.' 元",
                   "color":"#173177"
               },
               "keyword2": {
                   "value":"'.$res->periods.' 期",
                   "color":"#173177"
               },
               "keyword3": {
                   "value":"'.$date.'",
                   "color":"#173177"
               },
               "remark":{
                   "value":"感谢您的申请。",
                   "color":"#173177"
               }
           }

        }';

        $ret = $this->http_post($uri,$data,true);
        return $ret;
    }

    //还款成功通知
    //合同状态提醒



    //给指定的人发送模板消息
    //审核拒绝通知
    /*
    {{first.DATA}}
    项目名称：{{keyword1.DATA}}
    订单号：{{keyword2.DATA}}
    订单时间：{{keyword3.DATA}}
    贷款金额：{{keyword4.DATA}}
    还款期数：{{keyword5.DATA}}
    {{remark.DATA}}
    touser  推送给某人
    */
    //推送订单信息给指定的人
    public function getNewTpl($contractNo)
    {
        $uri = $this->_uri;
        $openId = $this->_openId;
        $res = TplModel::get_send_exam_tpl_info($contractNo);//获取用户订单信息

        $data = ' {
           "touser":"'.$openId.'",
           "template_id":"7QeODfQ1teSsqb-wGOckZlpSiW-N2GEtG0uv-CT7TS8",
           "url":"http://weixin.qq.com/download",
           "data":{
               "first": {
                   "value":"新订单信息如下，请查看。",
                   "color":"#173177"
               },
               "keyword1":{
                   "value":"'.$res->service_type.'",
                   "color":"#173177"
               },
               "keyword2": {
                   "value":"'.$res->id.'",
                   "color":"#173177"
               },
               "keyword3": {
                   "value":"'.date('Y-m-d H:s:i',time()).'",
                   "color":"#173177"
               },
               "keyword4": {
                   "value":"'.$res->loan_money.' 元",
                   "color":"#173177"
               },
               "keyword5": {
                   "value":"'.$res->periods.' 期",
                   "color":"#173177"
               },
               "remark":{
                   "value":"如有疑问，请拨打88888888。",
                   "color":"#173177"
               }
           }

        }';

        $ret = $this->http_post($uri,$data,true);
        return $ret;
    }

    /*
    {{first.DATA}}
    申请单号：{{keyword1.DATA}}
    申请金额：{{keyword2.DATA}}
    申请期限：{{keyword3.DATA}}
    取消原因：{{keyword4.DATA}}
    {{remark.DATA}}
    */
    //合同取消通知
    public function getConcelTpl($contractNo,$conselReason){
        $uri = $this->_uri;
        $openId = $this->_openId;
        if(is_null($conselReason)){
            $conselReason = '主动取消';
        }
        $res = TplModel::get_send_exam_tpl_info($contractNo);

        $data = ' {
           "touser":"'.$openId.'",
           "template_id":"toQByDRrLc5pISqxcjYcjrIW9a5vgXRzQoCl49ngkpE",
           "url":"'.$this->_url.'/wx/loan/mcode",
           "data":{
               "first": {
                   "value":"尊敬的'.$res->applicant_name.'，您有一笔贷款申请已取消。",
                   "color":"#173177"
               },
               "keyword1":{
                   "value":"'.$contractNo.'",
                   "color":"#173177"
               },
               "keyword2":{
                   "value":"'.$res->loan_money.' 元",
                   "color":"#173177"
               },
               "keyword3": {
                   "value":"'.$res->periods.' 期",
                   "color":"#173177"
               },
               "keyword4": {
                   "value":"'.$conselReason.'",
                   "color":"#173177"
               },
               "remark":{
                   "value":"如需要贷款，请重新提交申请。",
                   "color":"#173177"
               }
           }

        }';

        $ret = $this->http_post($uri,$data,true);
        return $ret;
    }

    //http post请求
    public function http_post($url, $data, $ssl = FALSE)
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if($ssl)
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        }
        //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
            //echo 'Curl error: ' . curl_error($curl);
            Logger::info('Tpl Curl error：'.curl_error($curl),'tpl-curl');
            return FALSE;
        }
        curl_close($curl);
        return $tmpInfo;
    }
}
