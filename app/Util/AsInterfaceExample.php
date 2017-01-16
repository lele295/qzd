<?php
namespace App\Util;

use App\Service\base\AECCBC;

/**
 * 安硕接口测试地址
 * Class AsInterfaceExmaple
 * @package App\Util
 */
class AsInterfaceExample
{
    static public function GetLoanInfo()
    {
        $customInfo = (Object)array(
            'Stores' => 'jieqianme-shenzhen',
            'StoresName' => '借钱么-深圳',
            'RSerialNo' => '4403000471',
            'BusinessType' => 'AprCLCOM056',
            'ProductName' => 'Apr-CL-常规-15000-36期',
            'OriginalPutoutDate' => '2016/05/03',
            'PayDate' => '03',
            'MonthRepayment' => '871.13',
            'FirstDrawingDate' => '844.88',
            'MonthlyInterestrate' => '1.75',
            'AddServiceRates' => '0.7',
            'ManagementFeesrate' => '0.804',
            'CustomerServiceRates' => '0.54',
            'stamptax' => '0.0',
            'TurnAccountNumber' => '755920947910303',
            'OpenBank' => '308',
            'OpenBankName' => '招商银行股份有限公司',
            'TurnAccountName' => '深圳市佰仟金融服务有限公司',
            'CustomerID' => '22953881',
            'HaCreditCardForRepayment' => '',
            'CustomerName' => '张帅',
            'Sex' => '1',
            'CertType' => 'Ind01',
            'CertID' => '130221199704024370',
            'Issueinstitution' => '唐山市公安局丰润分局',
            'MaturityDate' => '2016/11/02',
            'Sino' => '',
            'NativePlace' => '130200',
            'Villagetown' => '丰润区丰登坞镇',
            'Street' => '户尚村',
            'Community' => '后街道南',
            'CellNo' => '14号',
            'CommZip' => '064000',
            'Flag2' => '1',
            'FamilyAdd' => '130200',
            'Countryside' => '丰润区丰登坞镇',
            'Villagecenter' => '户尚村',
            'Plot' => '后街道南',
            'Room' => '14号',
            'FamilyZIP' => '064000',
            'WorkCorp' => '唐山市兄弟修理厂',
            'EmployRecord' => '修理部',
            'HeadShip' => '4',
            'UnitKind' => '16',
            'CellProperty' => '2',
            'Flag3' => '0',
            'WorkZip' => '064000',
            'WorkAdd' => '130200',
            'UnitCountryside' => '路南区',
            'UnitStreet' => '西外环',
            'UnitRoom' => '绿洲对面',
            'UnitNo' => '唐山市兄弟修理厂',
            'Flag8' => '1',
            'CommAdd' => '130200',
            'EmailCountryside' => '丰润区丰登坞镇',
            'EmailStreet' => '户尚村',
            'EmailPlot' => '后街道南',
            'EmailRoom' => '14号',
            'EmailAdd' => '601600668@qq.com',
            'FamilyTel' => '',
            'MobileTelephone' => '13483488802',
            'WorkTel' => '0315-5541440',
            'WorkTelPlus' => '0315-5546006',
            'PhoneMan' => '',
            'QqNo' => '601600668',
            'Wechat' => '601600668',
            'Marriage' => '1',
            'Childrentotal' => '0',
            'SpouseName' => '',
            'SpouseTel' => '',
            'SPOUSEWORKCORP' => '',
            'SPOUSEWORKTEL' => '',
            'House' => '1',
            'HouseRent' => '3000',
            'KinshipName' => '张树国',
            'KinshipTel' => '15832506985',
            'Flag10' => '1',
            'KinshipAdd' => '河北省唐山市',
            'BalanceSheet' => '',
            'RelativeType' => '010',
            'Spouse_Community' => '',
            'EduExperience' => '2',
            'FamilyMonthIncome' => '5000',
            'JobTime' => '2',
            'JobTotal' => '010',
            'SelfMonthIncome' => '5000',
            'OtherRevenue' => '0',
            'Severaltimes' => '0',
            'Falg4' => '2',
            'OtherContact' => '任金雨',
            'Contactrelation' => '6',
            'ContactTel' => '18632586123',
            'furniture_fix_addres' => '',
            'SerialNo' => '22953881001',
            'RepaymentBankCode' => '103',
            'RepaymentBank' => '中国农业银行股份有限公司',
            'ReplaceName' => '张帅',
            'CityCode' => '130200',
            'CityName' => '河北省唐山市',
            'OpenBranch' => '',
            'OpenBranchName' => '',
            'RepaymentNo' => '6228480658454126173',
        );

        $data = (Object)[];
        $data->data = [];
        array_push($data->data, $customInfo);
        $jsonData = json_encode($data);
        echo AECCBC::aes_encode($jsonData);
        exit;
    }

    //存量客户验证
    static public function CheckCustomer()
    {
        $customInfo = (Object)array(
            'CustomerID' => '10500175',
            'CertID' => '371424198501021517',
            'CustomerName' => '肖于',
            'MobileTelephone' => '15215478888',
            'WorkAdd' => '441900',
            'City' => '广东省东莞市',
            'FamilyAdd' => '广东省深圳市龙岗区爱联社区蒲排村28号公司宿舍303房',
            'CustomerPhase' => '第一次邀请',
            'CreditLimit' => '10000',
            'TopMonthPayment' => '958',
            'ProductID' => '20151103001',
            'ProductFeatures' => '常规产品',
            'EventName' => 'wantaoyongtest',
            'EventDate' => '2017/12/31',
            'SerialNo' => '20160427004',
            'Period' => '',
            'CustomerType' => '3',
            'requestStatus' => '1');
        $data = (Object)[];
        $data->data = [];
        array_push($data->data, $customInfo);
        $jsonData = json_encode($data);
        echo AECCBC::aes_encode($jsonData);
        exit;
    }

	// 模拟合同状态查询接口
	static public function GetContractStatus(){
		$customInfo = (Object)array(
			  'Status' => 'Success',
			  'ContractStatus' => '020',
		);
		$data = (Object)[];
		$data->data = [];
		array_push($data->data, $customInfo);
		$jsonData = json_encode($data);
		echo AECCBC::aes_encode($jsonData);
		exit;
	}

	//模拟合同当前审核流程状态查询(如取消会有原因)
	static public function GetSignedTaskOpinion(){
		$customInfo = (Object)array(
			'Status' => 'Success',
			'ContractStatus' => '100',
		);
		$data = (Object)[];
		$data->data = [];
		array_push($data->data, $customInfo);
		$jsonData = json_encode($data);
		echo AECCBC::aes_encode($jsonData);
		exit;
	}


}