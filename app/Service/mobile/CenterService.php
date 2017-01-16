<?php
namespace App\Service\mobile;

use App\Api\api\WechatApi;
use App\Log\Facades\Logger;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanModel;
use App\Model\Base\SyncModel;
use App\Model\Base\UniqueCodeModel;
use App\Model\mobile\WechatModel;
use App\Service\admin\SendService;
use App\Service\mobile\Service;
use App\Util\DxOperator;
use Illuminate\Support\Facades\Log;

class CenterService extends Service
{
	/*
	 * 获取个人中心信息
	 */
	public function get_center_info($uid)
	{
		$loanm = new LoanModel();
		$data["applys"] = $loanm->get_loan_by_user_id($uid);
		$data["schedules"] = $loanm->get_userid_loan_schedules($uid);
		$data["month_range"] = 0;
		/*
		if($data["applys"]) {
			$time_diff = time() - strtotime($data["applys"][0]->updated_at);

			if ($data["applys"][0]->status != "011" && $data["applys"][0]->status != "210" && $time_diff > 172800) {
				$auth = new AuthModel();
				$customer_res = $auth->get_user_id_id_auth_customer($uid);
				if($customer_res && $data["applys"][0]->EventName != $customer_res->EVENTNAME) {
					$data["month_range"] = $customer_res->MONTHRANGE;
				}
			}
		}
		*/
		return $data;
	}

	//发送审核结果微信信息
	public function send_auth_loan($loan_id)
	{
		$loanmodel = new LoanModel();
		$loaninfo = $loanmodel->getattid_userInfo($loan_id);

        if($loaninfo->loan_source == 1){
            $this->send_app_message($loanmodel,$loaninfo);
        }


		if ($loaninfo->status == '020') {
			$mobile_txt = "尊敬的" . $loaninfo->realname . "，恭喜您通过本次现金贷款申请，款项于通过后2个工作日内到账。详询热线：4009987850";
			$title = "恭喜！合同号：" . $loaninfo->pact_number . "审批通过，收到消息第2个工作日到账。";
			$status = "审批通过";
			$remark = "点击此处查看详情";
			$link = '/m/user';
		} elseif ($loaninfo->status == '010') {
			$mobile_txt = "尊敬的" . $loaninfo->realname . "，非常抱歉！您未通过本次现金贷款申请，感谢您选择我司服务。详询热线：4009987850";
			$title = "您好！合同号：" . $loaninfo->pact_number . "已否决。";
			$status = "已否决";
			$remark = "感谢您选择我司服务";
			$link = '';
		} elseif ($loaninfo->status == '100') {
			$mobile_txt = "尊敬的" . $loaninfo->realname . "，由于" . $loaninfo->reason . "，请登录微信重新申请，详询热线：4009987850";
			$title = "您好！合同号：" . $loaninfo->pact_number . ",因" . $loaninfo->reason . "已取消退回。";
			$status = "已取消";
			$remark = "点击此处重新申请";
			$link = '/users/register1';
		} elseif ($loaninfo->status == '210') {
			$mobile_txt = "尊敬的" . $loaninfo->realname . "，您的贷款申请已撤销，您可登录微信重新申请，详询热线：4009987850";
			$title = "您好！合同号：" . $loaninfo->pact_number . ",已撤销。";
			$status = "已撤销";
			$remark = "点击此处可重新申请";
			$link = '/users/register1';
		} elseif ($loaninfo->status == '080' && $loaninfo->loan_source == 1){
            //zl 07-28   若是分期购APP的提单 发送短信+推送
            $content = '尊敬的'.$loaninfo->realname.'，恭喜您通过本次现金贷款申请，请尽快打开“佰仟分期购”APP完成身份验证，款项于认证通过后2个工作日内到账。详询热线：4009987850';
            $smsSend = new SendService();
            $smsSend->send_msn_to_admin($content,$loaninfo->mobile);

            //zl 推送
            $wechatApi = new WechatApi();
            $id_card = array();
            $id_card[] = $loaninfo->id_card;
            //$wechatApi->AppPush($id_card,4);

            return false;
        }else {
			/*	2016-6-3添加：电销录单完成后发送短信、微信通知	*/
			if(DxOperator::$send == true && $loaninfo->status == '011'){
				$mobile_txt = '您已申请了'.$loaninfo->loan_amount.'元的现金贷款，赶紧关注“佰仟分期购”（bqfenqigou），进入“现金分期”进行授权吧！距离成功提现只差一步咯！';
				$title = '您已申请了现金贷款，快来授权一下吧！';
				$status = '待提交';
				$remark = '距离成功提现只差一步咯！';
				$link = '/users/register1';
			}else{
				return false;
			}
		}
		$wetemplate = new WeTemplateService();
		if ($loaninfo->status == '020') {
			$template_id = $wetemplate->cash_apply_wt();
			$arr["key1"] = "¥" . $loaninfo->loan_amount;
			$position1 = mb_strpos($loaninfo->OpenBankName, "行");
			$position2 = mb_strlen($loaninfo->ReplaceAccount);
			$arr["key2"] = mb_substr($loaninfo->OpenBankName, 0, $position1 + 1) . " 尾号" . mb_substr($loaninfo->ReplaceAccount, $position2 - 4, 4);
			$arr["key3"] = date("Y-m-d", strtotime($loaninfo->first_payment_date));
			//$arr['wechat_status'] = '200';

			// 2016-06-01增加 调用cash_apply_wt()方法时,使用的是现金贷申请反馈通知模板,此时type='type_1'
			// 此处的type_1在文件myconfig.php中定义
			$type = 'type_1';
			//给分期购用户发送信息 调用分期购接口1,传递参数如下:
				// 手机号phoneNumber   客户姓名customerName  身份证号certId          额度credit
				// 首期还款日repayDay  还款银行bankName      模板尾部自定义内容remark
			$strPosition = mb_strpos($loaninfo->OpenBankName, '银行');
			$bankAndAccount = mb_substr($loaninfo->OpenBankName, 0, $strPosition+2) . ' 尾号' .mb_substr($loaninfo->ReplaceAccount, -4);
			$link = empty($link) ? '' : base64_encode($link);
			$parameters = [
				'first' => $title,
				'phoneNumber' => $loaninfo->mobile,
				'customerName' => $loaninfo->realname,
				'certId' => $loaninfo->id_card,
				'credit' => $loaninfo->loan_amount,
				'repayDay' => date('Y/m/d', strtotime($loaninfo->first_payment_date)),
				'bankName' => $bankAndAccount,
				'remark' => $remark,
				'link' => $link
			];
		} else {
			$template_id = $wetemplate->audit_loan_wt();
			$arr["key1"] = "¥" . $loaninfo->loan_amount;
			$arr["key2"] = $loaninfo->loan_period;
			$arr["key3"] = $loaninfo->month_interest . "%";
			//$arr['wechat_status'] = '100';
			// 2016-06-01增加 调用audit_loan_wt()方法时,使用的是贷款审核通知模板,此时指定type='type_2'
			$type = 'type_2';
			//给分期购用户发送信息 调用分期购接口2,传递参数如下:
				// 手机号phoneNumber   客户姓名customerName     身份证号certId       额度credit
				// 期数period          月利率monthInterestRate  审核状态auditStatus  模板尾部自定义内容remark
			$link = empty($link) ? '' : base64_encode($link);
			$parameters = [
				'first' => $title,
				'phoneNumber' => $loaninfo->mobile,
				'customerName' => $loaninfo->realname,
				'certId' => $loaninfo->id_card,
				'credit' => $loaninfo->loan_amount,
				'period' => $loaninfo->loan_period,
				'monthInterestRate' => $loaninfo->month_interest . '%',
				'auditStatus' => SyncModel::loanStatusName($loaninfo->status),
				'remark' => $remark,
				'link' => $link
			];
		}
		
		$unique = new UniqueCodeModel($loaninfo->source);
		$unique->select_send_supply($mobile_txt, $loaninfo->mobile);
		$openid = $loaninfo->openid;

		$arr["title"] = $title;
		$arr["key4"] = $status;
		$arr["remark"] = $remark;
		//$wx_res = $wetemplate->template($openid, $template_id, $arr);
		//return $wx_res

        //app端，+发推送   如果是080直接就上面跑
		if($loaninfo->loan_source == 1){
            $wechatApi = new WechatApi();
            $id_card = array();
            $id_card[] = $loaninfo->id_card;
            //取消
            if($loaninfo->status == '100'){
                //$wechatApi->AppPush($id_card,3);
            }else if($loaninfo->status == '010'){
                //$wechatApi->AppPush($id_card,4);
            }
        }

		if ($loaninfo->source == 3) {
			// 给分期购客户发送模板消息
			$wechatApi = new WechatApi();
			Logger::info('合同号为：' . $loaninfo->pact_number . ' 手机号为：' . $loaninfo->mobile .'调用了分期购发送模板消息接口，接口类型：' . $type, 'fqg-msg-interface');
			$res = $wechatApi->send_wechat_message($type, $parameters);
			return $res;
		} else{
			if (!$openid) {
				return false;
			}
			$wx_res = $wetemplate->template($openid, $template_id, $arr);
			return $wx_res;
		}
	}

	//发微信信息
	public function send_weixin_message($loan_id)
	{
		$loanmodel = new LoanModel();
		$loaninfo = $loanmodel->getattid_userInfo($loan_id);
		$openid = $loaninfo->openid;
		if ($loaninfo->status == '011') {
			$title = "您有一笔贷款未完成申请，该笔申请将在24小时后失效";
			$status = "申请未完成";
			$remark = "点击此处完成申请，立即提现";
		}
		if ($loaninfo->status == '100') {
			$title = "您好！" . $loaninfo->pact_number . "因" . $loaninfo->reason;
			$status = "审批退回";
			$remark = "点击重新申请";
		} else {
			return false;
		}
		$wetemplate = new WeTemplateService();
		$template_id = $wetemplate->audit_loan_wt();
		$arr["title"] = $title;
		$arr["key1"] = "¥" . $loaninfo->loan_amount;
		$arr["key2"] = $loaninfo->loan_period;
		$arr["key3"] = $loaninfo->month_interest . "%";
		$arr["key4"] = $status;
		$arr["remark"] = $remark;
		$wx_res = $wetemplate->template($openid, $template_id, $arr);
		return $wx_res;
	}


    /**
     * @param $loanmodel
     * @param $loaninfo
     * 推送app消息
     */
    public function send_app_message($loanmodel,$loaninfo){
        $wechatApi = new WechatApi();
        Logger::info('订单' .$loaninfo->id. '状态为' . $loaninfo->status . 'idcard为'. $loaninfo->id_card . '等待推送app消息' ,'app_message_record');
        $id_card = array();
        $id_card[] = $loaninfo->id_card;
        switch($loaninfo->status){
            case '080':
                $wechatApi->AppPush($id_card,6);
                break;
            case '010':
                $wechatApi->AppPush($id_card,5);
                break;
            case '100':
                $wechatApi->AppPush($id_card,4);
                break;
        }
    }


}
