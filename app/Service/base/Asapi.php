<?php
/* 
 * 安硕api model
 */
namespace App\Service\base;
use App\Log\Facades\Logger;
use App\Model\Base\CommErrorModel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Service\Curl;

class Asapi{
	/**
	 * 判断用户是否有效
	 * certID  	     客户身份证号码
	 * CustomerName  客户姓名
	 */
	public function Checkuser($fromDate){
		$data['certID'] = $fromDate['certID'];
		$data['customerName'] = $fromDate['customerName'];
		$url = config('myconfig.as_api_url').'/rest/User/checkUser?'.http_build_query($data);
		$res = Curl::curl_post($url);
        Logger::info('--------------华丽的分割线----------------------------');
        Logger::info('判断用户是否三个月内是否有效接口返回结果：'.json_encode($res,JSON_UNESCAPED_UNICODE));
		return $res;
	}

	/**
	 * 还款试算接口
	 */
	public function RepayTrial($fromData){
		$data['businessSum'] = $fromData['businessSum'];
		$data['periods'] = $fromData['periods'];
		$data['productName'] = $fromData['productName'];
		$data['productID'] = $fromData['productID'];
		$data['customerName'] = $fromData['customerName'];
		$data['certID'] = $fromData['certID'];
		$url  = config('myconfig.as_api_url').'/rest/contract/getLoanInfo?'.http_build_query($data);
        Logger::info('--------------华丽的分割线----------------------------');
        Logger::info('试算传入数据：'.json_encode($data,JSON_UNESCAPED_UNICODE));
		$res = Curl::curl_post($url);
        Logger::info('--------------华丽的分割线----------------------------');
        Logger::info('试算接口返回结果：'.json_encode($res,JSON_UNESCAPED_UNICODE));
		return $res;
	}

	/**
	 * 合同状态同步接口
	 */
	public function ContractStatus($fromDate){
		$data['contractNo'] = $fromDate['contractNo'];
		$url = config('myconfig.as_api_url').'/rest/contract/getContractStatusBatch?'.http_build_query($data);
		$res = Curl::curl_post($url);
		return $res;
	}

	/**
	 * 查询还款计划接口
	 * contractNo 合同号
	 */
	public function Repayment($fromDate){
		$data['contractNo'] = $fromDate['contractNo'];
		$url = config('myconfig.as_api_url').'/PaymentSchedule/query?'.http_build_query($data);
		$res = Curl::curl_post($url);
		return $res;
	}

	/**
	 * 取消合同接口 -- 审核通过前叫取消
	 */
	public function CancelContract($fromDate){
		$data['contractNo'] = $fromDate['contractNo'];
		$url = config('myconfig.as_api_url').'/contract/handle/cancleContract?'.http_build_query($data);
		$res = Curl::curl_post($url);
		return $res;
	}


	/**
	 * 撤销合同接口 -- 审核通过后叫撤销
	 */
	public function RevokeContract($fromDate){
		$data['contractNo'] = $fromDate['contractNo'];
		$url = config('myconfig.as_api_url').'/contract/handle/revokeContract?'.http_build_query($data);
		$res = Curl::curl_post($url);
		return $res;
	}

	/**
	 * 提交注册已签署合同接口
	 */
	public function CommitRegister($fromDate){
		$data['contractNo'] = $fromDate['contractNo'];
		$url = config('myconfig.as_api_url').'/contract/handle/updateContract?'.http_build_query($data);
		$res = Curl::curl_post($url);
		return $res;
	}

	/**
	 *
	 * 提单接口
	 * @param  [type] $fromdata [description]
	 * @return [type]           [description]
	 */
	public function CommitContract($fromdata){
		$data['appNo'] = $fromdata['appNo']; 	//申请号
		$data['businessType'] = $fromdata['businessType']; 	//产品代码
		$data['productName'] = $fromdata['productName'];	//产品名称
		$data['periods'] = $fromdata['periods'];	//分期期数
		$data['monthRepayment'] = $fromdata['monthRepayment'];	//每月还款额
		$data['businessSum'] = $fromdata['businessSum'];	//贷款本金
		$data['operatorMode'] = $fromdata['operatorMode'];	//运作模式
		$data['productType'] = $fromdata['productType'];	//产品类型
		$data['subProductType'] = $fromdata['subProductType'];	//产品子类型

		$data['shopType'] = $fromdata['shopType']; 	//商品类型
		$data['repaymentWay'] = $fromdata['repaymentWay']; 	//还款方式
		$data['replaceAccount'] = $fromdata['replaceAccount']; 	//代扣/放款账号
		$data['openBank'] = $fromdata['openBank']; 	//代扣/放款账号开户行代码
		$data['openBankName'] = $fromdata['openBankName']; 	//代扣/放款账号开户行名称
        $data['bankCity'] = $fromdata['bankCity']; 	//开户行城市
		$data['subopenBank'] = $fromdata['subopenBank']; 	//代扣/放款账号开户行支行代码
		$data['subopenBankName'] = $fromdata['subopenBankName']; 	//代扣/放款账号开户行支行名称

		$data['stores'] = $fromdata['stores']; 	//销售门店编码
		$data['storesName'] = $fromdata['storesName']; 	//销售门店名称
		$data['storeCityCode'] = $fromdata['storeCityCode']; 	//门店城市
		$data['salesexecutive'] = $fromdata['salesexecutive']; 	//销售代表
		$data['salesexecutiveName'] = $fromdata['salesexecutiveName']; 	//销售代表名称
		$data['salesManager'] = $fromdata['salesManager']; 	//销售经理
		$data['salesManagerName'] = $fromdata['salesManagerName']; 	//销售经理名称
        $data['cityManager'] = $fromdata['cityManager']; 	//城市经理
        $data['cityManagerName'] = $fromdata['cityManagerName']; 	//城市经理名称

		$data['falg6'] = $fromdata['falg6']; 	//是否已取得客户授权
		$data['elegicAffix'] = $fromdata['elegicAffix']; 	//使用中信或中泰标识

		$data['customerName'] = $fromdata['customerName']; 	//姓名
		$data['certID'] = $fromdata['certID']; 	//身份证号
		$data['mobileTelephone'] = $fromdata['mobileTelephone']; 	//手机号码
		$data['qqNo'] = $fromdata['qqNo']; 	//QQ号
		$data['wechat'] = $fromdata['wechat']; 	//微信号码
		$data['emailAdd'] = $fromdata['emailAdd']; 	//电子邮箱

		$data['unitKind'] = $fromdata['unitKind']; 	//行业
		$data['workCorp'] = $fromdata['workCorp']; 	//工作单位
        $data['workTel'] = $fromdata['workTel']; 	//工作单位电话
		$data['eduexperience'] = $fromdata['eduexperience']; 	//最高学历
		$data['creditCardNo'] = $fromdata['creditCardNo']; 	//信用卡

		$data['kinshipName'] = $fromdata['kinshipName']; 	//家庭成员姓名
		$data['kinshipTel'] = $fromdata['kinshipTel']; 	//家庭成员联系电话
        //家庭住址
		$data['familyAdd'] = $fromdata['familyAdd'];
        $data['countryside'] = $fromdata['countryside'];
        $data['villagecenter'] = $fromdata['villagecenter'];
        $data['plot'] = $fromdata['plot'];
        $data['room'] = $fromdata['room'];

        //工作单位地址
        $data['workAdd'] = $fromdata['workAdd'];
        $data['unitCountryside'] = $fromdata['unitCountryside'];
        $data['unitStreet'] = $fromdata['unitStreet'];
        $data['unitRoom'] = $fromdata['unitRoom'];
        $data['unitNo'] = $fromdata['unitNo'];

		$data['servicePass'] = $fromdata['servicePass']; 	//手机服务密码
		$data['jdAccount'] = $fromdata['jdAccount']; 	//京东账号
		$data['jdAccountPass'] = $fromdata['jdAccountPass']; 	//京东密码
		$data['taobaoAccount'] = $fromdata['taobaoAccount']; 	//淘宝账号
		$data['taobaoAccountPass'] = $fromdata['taobaoAccountPass']; 	//淘宝密码

        $data['businessRange'] = $fromdata['businessRange']; 	//商品范畴
        $data['inputDate'] = $fromdata['inputDate']; 	//订单生成的时间

        $data['relativeType'] = $fromdata['relativeType']; 	//亲属关系

        $data['otherContact'] = $fromdata['otherContact']; 	//其他联系人姓名
        $data['contactrelation'] = $fromdata['contactrelation']; 	//其他联系人关系
        $data['contactTel'] = $fromdata['contactTel']; 	//其他联系人手机

        $data['cellNo'] = $fromdata['cellNo']; 	//身份证户籍地址
        $data['nationality'] = $fromdata['nationality']; 	//民族
        $data['sex'] = $fromdata['sex']; 	//性别
        $data['issueinstitution'] = $fromdata['issueinstitution']; 	//身份证签证机关
        $data['maturityDate'] = $fromdata['maturityDate']; 	//身份证有效期(到期日)

        Logger::info('--------------华丽的分割线----------------------------');
        Logger::info('提单传入数据：'.json_encode($data,JSON_UNESCAPED_UNICODE));
		$url = config('myconfig.as_api_url').'/newContract/submit?'.http_build_query($data);
		$res = Curl::curl_post($url);
        Logger::info('--------------华丽的分割线----------------------------');
        Logger::info('提单接口返回结果：'.json_encode($res,JSON_UNESCAPED_UNICODE));
		return $res;
	}

	/**
	 * 影像接口
	 */
	public function PhotoCommit($fromdata){
		$data['cusIdCardPositive'] = $fromdata['cusIdCardPositive']; 	//客户身份证正面
		$data['cusIdCardNegative'] = $fromdata['cusIdCardNegative']; 	//客户身份证反面
		$data['cusHanderIdCard'] = $fromdata['cusHanderIdCard']; 	//客户手持身份证
		$data['chsiScreenshot'] = $fromdata['chsiScreenshot']; 	//学信网截图
		$data['surgicalNoticeBook'] = $fromdata['surgicalNoticeBook']; 	//手术知情同意书
		$data['workCard'] = $fromdata['workCard']; 	//名片/工卡
		$data['bankCardImg'] = $fromdata['bankCardImg']; 	//银行卡照片正面
		$data['creditAutImg'] = $fromdata['creditAutImg']; 	//征信授权书照片
		$url = config('myconfig.as_api_url').'/rest/contract/upload/addImage?'.http_build_query($data);
		$res = Curl::curl_post($url);
        Logger::info('--------------华丽的分割线----------------------------');
        Logger::info('影像接口返回结果：'.json_encode($res,JSON_UNESCAPED_UNICODE));
		return $res;
	}
    

}

