<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service\Curl;
use Illuminate\Support\Arr;
use Log;
use DB;
use App\Service\base\Asapi;
use App\Util\FileReader;
use App\Log\Facades\Logger;

class AsapiController extends Controller{

	public function __construct(){

	}

	/**
	 * 判断用户是否有效
	 * certID  	     客户身份证号码
	 * CustomerName  客户姓名
	 */
	public function anyCheckuser($id_card='',$full_name=''){
		$data['certID'] = $id_card;
		$data['customerName'] = $full_name;
		$as = new Asapi();
		$res = $as->Checkuser($data);
		return $res;
	}

	/**
	 * 合同状态同步接口
	 * contractNo 合同号
	 */
	public function anyContractstatus($contract_id){
		$data['contractNo'] = $contract_id;
		$as = new Asapi();
		$res = $as->ContractStatus($data);
		return $res;
	}

	/**
	 * 查询还款计划接口
	 * contractNo 合同号
	 */
	public function anyRepayment(){ 
		$data['contractNo'] = '71563789001';
		$as = new Asapi();
		$res = $as->Repayment($data);
	}

	/**
	 * 取消合同接口 -- 审核通过前叫取消
	 * contractNo 合同号
	 */
	public function anyCancelcontract($contractNo){
		$data['contractNo'] = $contractNo;
		$as = new Asapi();
		$res = $as->CancelContract($data);
		return $res;
	}


	/**
	 * 撤销合同接口 -- 审核通过后叫撤销
	 * contractNo 合同号
	 */
	public function anyRevokecontract(){		
		$data['contractNo'] = '10000154002';
		$as = new Asapi();
		$res = $as->RevokeContract($data);
	}

	/**
	 * 提交注册已签署合同接口
	 * contractNo 合同号
	 */
	public function anyCommit($contractNo){
		$data['contractNo'] = $contractNo;
		$as = new Asapi();
		$res = $as->CommitRegister($data);
		return $res;
	}

	/**
	 * 还款试算接口
	 */
	public function anyRepaytrial($merchant_code,$periods,$businessSum,$customerName,$certID){
        $productinfo = $this->productinfo($merchant_code,$periods,$businessSum); //获取产品信息

		$data['businessSum'] = $businessSum;  //贷款本金
		$data['periods'] = $periods;    //贷款期数
		$data['productName'] = $productinfo->TYPENAME;  //产品名称
		$data['productID'] = $productinfo->BUSTYPEID;  //产品id
		$data['customerName'] = $customerName;  //客户姓名
		$data['certID'] = $certID;  //身份证号
		$as = new Asapi();
		$res = $as->RepayTrial($data);
		return $res;
	}

	public function anyCommitcontract($order_array = array()){
		$info = $this->Sainfos($order_array['merchant_code']);
		$productinfo = $this->productinfo($order_array['merchant_code'],$order_array['periods'],$order_array['businessSum']);

        //产品代码
        if(is_object($productinfo)){
            $order_array['businessType'] = $productinfo->BUSTYPEID;
        }else{
            $order_array['businessType'] = '';
        }
        //产品名称
        if(is_object($productinfo)){
            $order_array['productName'] = $productinfo->TYPENAME;
        }else{
            $order_array['productName'] = '';
        }
        $order_array['operatorMode'] = '03';	//运作模式
        $order_array['productType'] = '030';	//产品类型
        $order_array['subProductType'] = '0';	//产品子类型
        $order_array['repaymentWay'] = 1; 	//还款方式

        $order_array['stores'] = $info->SNO; 	//销售门店编码
        $order_array['storesName'] = $info->SNAME; 	//销售门店名称
        $order_array['storeCityCode'] = $info->CITY; 	//门店城市
        $order_array['salesexecutive'] = $info->SALESMANNO; 	//销售代表编码
        $order_array['salesexecutiveName'] = $info->USERNAME; 	//销售代表名称
        $order_array['salesManager'] = $info->SALESMANAGER; 	//销售经理
        $order_array['salesManagerName'] = $info->SALESMANAGERNAME; 	//销售经理名称
        $order_array['cityManager'] = $info->CITYMANAGER; 	//城市经理
        $order_array['cityManagerName'] = $info->CITYMANAGERNAME; 	//城市经理名称
        $order_array['falg6'] = 1; 	//是否已取得客户授权
        $order_array['elegicAffix'] = 1; 	//使用中信或中泰标识

        $order_array['qqNo'] = ''; 	//QQ号
        $order_array['wechat'] = ''; 	//微信号码

        //return $order_array;
		$as = new Asapi();
		$res = $as->CommitContract($order_array);
		return $res;
	}

	/**
	 * 图片上传
	 */
	public function getPhoto($data = array()){
		$res['jsonData'] = json_encode($data);
        $url = config('myconfig.as_api_url').'/rest/contract/upload/addImage?'.http_build_query($res);
		$res = Curl::curl_post($url);
		return $res;
	}

	/**
	 * [file2dir description]
	 * @param  [type] $sourcefile [源文件]
	 * @param  [type] $contractNo [合同号]
	 * @param  [type] $typeNo     20001:客户身份证正面  20002:客户手持身份证  20003:银行卡照片正面 
	 *                            20005:名片/工卡   20025:客户身份证反面  302004:征信授权书照片  6021:手术知情同意书
	 * @param  [return] 		  文件转移成功返回影像目录
	 */
	public function file2dir($sourcefile,$contractNo,$typeNo){
		if(!in_array($typeNo, array('20001','20002','20025','20003','20005','302004','6021'))){
			return false;
		}
        $sourcefile = storage_path().$sourcefile;
	    if(!file_exists($sourcefile)){
	        return false;
	    }

	    $dir = '/share/aip/upload/qzd/'.date('Y/m/d').'/'.$contractNo.'/'.$typeNo;
        $desDir = '/home/weblogic/bqjrfile/als/qzd/'.date('Y/m/d').'/'.$contractNo.'/'.$typeNo;

		if(!is_dir($dir)){
			mkdir($dir,0777,true);
		}

	    $filename = $typeNo.'.jpg';
        if(!is_dir($sourcefile)){
            $r = copy($sourcefile, $dir .'/'. $filename);
        }else{
            return '';
        }

	    if($r == true){
	    	//return  $dir .'/'. $filename;
            return  $desDir .'/'. $filename;
	    }else{
	    	return false;
	    }
	}

	/**
	 * 通过门店获取相关信息
	 * return sno sname salesnameno
	 */
	public function Sainfos($stores){
		$sql = "SELECT st.SNO,st.SNAME,sm.SALESMANNO,ui2.USERNAME,st.SALESMANAGER,ui.USERNAME SALESMANAGERNAME,st.CITY,ui.SUPERID CITYMANAGER,ui3.USERNAME CITYMANAGERNAME FROM sync_store_info st
				LEFT JOIN sync_storerelativesalesman sm on st.SNO=sm.SNO
				LEFT JOIN sync_user_info ui on st.SALESMANAGER=ui.USERID
				LEFT JOIN sync_user_info ui2 on sm.SALESMANNO=ui2.USERID
				LEFT JOIN sync_user_info ui3 on ui.SUPERID=ui3.USERID
				where st.SNO='".$stores."'";			
		$res = DB::select($sql);
		if($res){
			return $res[0];
		}else{
			return false;
		}	
	}

	/**
	 * 获取产品信息
	 * par  store门店号 preiods期数 businesssum 代扣总金额
	 * return bustyeid typename
	 */
	public function productinfo($stores,$preiods,$businessSum){
		$sql = "select pb.BUSTYPEID,bt.TYPENAME from sync_storerelativeproduct sp
				left JOIN sync_product_businesstype pb on sp.PNO=pb.PRODUCTSERIESID
				left JOIN sync_business_type bt on pb.BUSTYPEID=bt.TYPENO
				where sp.SNO='".$stores."' and bt.TERM='".$preiods."' and
				 bt.LOWPRINCIPAL <= '".$businessSum."'
				 and bt.TALLPRINCIPAL>= '".$businessSum."'";

		$res = DB::select($sql);
		if($res){
			return $res[0];
		}else{
			return false;
		}	
	}
	
}