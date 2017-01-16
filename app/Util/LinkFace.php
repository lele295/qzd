<?php
namespace App\Util;

use App\Log\Facades\Logger;
use App\Util\RSA;
use App\Model\Yitu\YituModel;
use Illuminate\Support\Facades\Auth;
use Log;
use Illuminate\Support\Facades\Session;


/**
 * Author: CHQ
 * Time: 2016/7/1 9:43
 * Usage: 人脸识别接口新版
 * Update:
 */
class LinkFace
{
	// 静态成员变量，用来保存类的唯一实例
	private static $_instance;

	// 用private修饰构造函数，防止外部程序来使用new关键字实例化这个类
	private function __construct()
	{
	}

	// 覆盖php魔术方法__clone()，防止克隆
	private function __clone()
	{
		trigger_error('Clone is not allow', E_USER_ERROR);
	}

	// 单例方法，返回类唯一实例的一个引用
	public static function getInstance()
	{
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	// 判断是否开启识别功能
	public static function isOn()
	{
		return config('extension.face_link_switch');
	}

	/**
	 * OCR结果判断逻辑
	 * @param $imgPath
	 * @return array
	 */
	public static function judgeResultForOCR($imgPath, $side)
	{
		// OCR接口原始返回值
		$output = self::characterOCR($imgPath);

		if ('OCR_original_return_value' === $output['status']) {
			$res = json_decode($output['data'], true);
			if(!$res){
				// 请求超时，接口返回值异常，做容错处理（比如私有云服务器崩溃）
				return ['status' => true, 'data' => '请重新拍摄上传清晰的身份证照片。'];
			}
			if (0 === $res['rtn']) {
				// 请求成功
				$idcard_type = isset($res['idcard_ocr_result']['idcard_type']) ? $res['idcard_ocr_result']['idcard_type'] : null;
				if($side === 1){
					// 识别身份证正面
					if(($idcard_type === $side) && !empty($res['idcard_ocr_result']['citizen_id']) && !empty($res['idcard_ocr_result']['name']) && !empty($res['idcard_ocr_result']['address']) && !empty($res['idcard_ocr_result']['birthday']) && !empty($res['idcard_ocr_result']['gender']) && !empty($res['idcard_ocr_result']['nation'])){
                        Session::put('cert_face_info',$res);
                        Session::save();
                        return ['status' => true, 'data' => '正面照识别成功，信息完整！'];
					}else{
						// 按照给定的需求文档提示文字
						return ['status' => false, 'data' => '身份证正面照片未通过。' . '<br>' . '请重新拍摄上传清晰的身份证正面照片。'];
					}
				}elseif($side === 2){
					// 识别身份证反面
					if(($idcard_type === $side) && !empty($res['idcard_ocr_result']['agency']) && !empty($res['idcard_ocr_result']['valid_date_begin'])){
                        Session::put('cert_opposite_info',$res);
                        Session::save();
                        return ['status' => true, 'data' => '反面照识别成功，信息完整！'];
					}else{
						return ['status' => false, 'data' => '身份证反面照片未通过。' . '<br>' . '请重新拍摄上传清晰的身份证反面照片。'];
					}
				}
			} else {
				// 网络请求失败或者其他情况，做容错处理
				return ['status' => true, 'data' => '其他原因！'];
			}
		} else {
			return $output;
		}
	}

	// Compare结果判断逻辑
	public static function judgeResultForCompare($queryImg, $dbImg)
	{
		$output = self::faceCompare($queryImg, $dbImg);
		if ('compare_original_return_value' === $output['status']) {
			$res = json_decode($output['data'], true);
			if(!$res){
				// 请求超时，接口返回值异常，做容错处理（比如私有云服务器崩溃）
				return ['status' => true, 'data' => '接口异常！'];
			}
			if (0 === $res['rtn']) {
				// 请求成功，获取依图接口返回的识别到的人脸数目
				$faceNum = isset($res['verify_detail']['score_list']) ? count($res['verify_detail']['score_list']) : null;
				if($faceNum == 0){
					return ['status' => false, 'data' => '手持身份证合照未通过，请检查：' . '<br>' . '1.手持身份证拍照了吗？' . '<br>' . '2. 身份证正面对外了吗？' . '<br>' . '3. 身份证要拍清楚哦；' . '<br>' . '4. 人脸要完全露出来哦；' . '<br>' . '请重新拍摄上传，谢谢！'];
				}elseif (($faceNum >= 1) && isset($res['pair_verify_result']) && (0 == $res['pair_verify_result']) ){
					return ['status' => true, 'data' => '认为是同一个人'];
				}
				// 2016-07-11 注释，请勿删除！
//				elseif ($faceNum == 1){
//					return ['status' => false, 'data' => '手持身份证合照未通过，请确认：' . '<br>' . '1.手持身份证拍照；' . '<br>' . '2. 身份证正面对外；' . '<br>' . '3. 身份证要拍清楚；' . '<br>' . '4. 人脸要完全露出来；' . '<br>' . '请重新拍摄上传清晰的手持身份证合照。'];
//				}elseif (($faceNum >= 2) && isset($res['pair_verify_result']) && (0 == $res['pair_verify_result'])){
//					return ['status' => true, 'data' => '认为是同一个人'];
//				}
				else{
					return ['status' => false, 'data' => '手持身份证合照未通过，' . '<br>' . '请重新拍摄上传，谢谢！'];
				}
			} elseif (-4600 === $res['rtn']) {
				// 输入图像中未检测到人脸
				return ['status' => false, 'data' => '手持身份证合照未通过，请检查：' . '<br>' . '1.手持身份证拍照了吗？' . '<br>' . '2. 身份证正面对外了吗？' . '<br>' . '3. 身份证要拍清楚哦；' . '<br>' . '4. 人脸要完全露出来哦；' . '<br>' . '请重新拍摄上传，谢谢！'];
			} else {
				// 请求失败或者其他原因，做容错处理
				return ['status' => true, 'data' => '其他原因！'];
			}
		} else {
			return $output;
		}
	}


	/**
	 * 调用第三方接口，获取用户上传的证件照片文字识别结果
	 * @param $imgPath
	 * @return array
	 */
	public static function characterOCR($imgPath)
	{
		if (!config('extension.face_link_switch')) {
			return ['status' => true, 'data' => 'OCR接口配置为未启用！'];
		}
		// 先查找缓存数据，若没有再调用接口
		$qpr = ['type'=>1, 'key_1'=>str_replace(storage_path(), '', $imgPath)];
		$cacheResult = self::getYituCache($qpr);
//		if($cacheResult['isCache']){
//			Logger::info('用户' .  '-' . '使用了OCR缓存数据：' . $cacheResult['data'], 'OCR-return');
//			return ['status' => 'OCR_original_return_value', 'data' => $cacheResult['data']];
//		}
		// $imginfo = getimagesize($imgPath);
		// if (!isset($imginfo[2]) || ($imginfo[2] !== 2)) {
		// 第三方接口要求照片为JPEG格式
		// return ['status' => false, 'data' => '传入的图片格式不正确，请重新传入JPEG格式的图片！'];
		// }

		$qstr = file_get_contents($imgPath);
		$imgContent = base64_encode($qstr);
		$api_url = config('extension.yitu_api_ocr');
		$x_access_id = config('extension.face_compare_accessid');
		$fields = [
			'user_info' => [
				'image_content' => $imgContent
			],
			'options' => [
				'ocr_type' => 1,
				'ocr_mode' => 3,
				'auto_rotate' => true
			]
		];
		$fields = json_encode($fields);

		// 此处需要使用给定的签名算法
		$x_signature = self::sign($fields, str_random(10));
		$timeOut = config('extension.yitu_api_timeout');
		$ch = curl_init();
        //配置代理
       // curl_setopt($ch,CURLOPT_PROXY,'10.80.3.37');
       // curl_setopt($ch,CURLOPT_PROXYPORT,808);
        //curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/4.0');

		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);

		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'x-access-id:' . $x_access_id, 'x-signature:' . $x_signature]);
		$SSL = substr($api_url, 0, 8) == "https://" ? true : false;
		if ($SSL) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		}
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$output = curl_exec($ch);
		curl_close($ch);
        $user_id = Session::get('user_id');
		Logger::info('用户 userid  '.$user_id.'___'. '调用OCR接口，原始返回值为：' . $output, 'OCR-return');
		$saveData = [
			'type' => 1,
			'key_1' => str_replace(storage_path(), '', $imgPath),
			'key_2' => '',
			'result' => $output ? $output : '',
			'createtime' => time()
		];
		self::saveYituReturn($saveData);
		return ['status' => 'OCR_original_return_value', 'data' => $output];
	}

	/**
	 * 比对登记照片和查询照片，判断查询照片中是否有登记照片中的人
	 * @param $queryImg
	 * @param $dbImg
	 * @return array
	 */
	public static function faceCompare($queryImg, $dbImg)
	{
		if (!config('extension.face_link_switch')) {
			return ['status' => true, 'data' => '人脸比对接口配置为未启用！'];
		}
		// 先查找缓存数据，若没有再调用接口
		$qpr = ['type'=>2, 'key_1'=>str_replace(storage_path(), '', $dbImg), 'key_2'=>str_replace(storage_path(), '', $queryImg)];
		$cacheResult = self::getYituCache($qpr);
		if($cacheResult['isCache']){
            $user_id = Session::get('user_id');
			Logger::info('用户 userid  '.$user_id.'___'. '使用了人脸比对缓存数据：' . $cacheResult['data'], 'facecompare-return');
			return ['status' => 'compare_original_return_value', 'data' => $cacheResult['data']];
		}
		$qstr = file_get_contents($queryImg);
		$dstr = file_get_contents($dbImg);
		$queryImgContent = base64_encode($qstr);
		$dbImgContent = base64_encode($dstr);
		$api_url = config('extension.yitu_api_compare');
		$x_access_id = config('extension.face_compare_accessid');
		$fields = [
			'query_image_type' => 3,
			'database_image_type' => 3,
			'query_image_content' => $queryImgContent,
			'database_image_content' => $dbImgContent,
			'auto_rotate_for_query' => true,
			'auto_rotate_for_database' => true,
			'max_faces_allowed' => 2,
			'enable_verify_detail' => true,
			'return_face_rect' => true
		];
		$fields = json_encode($fields);
		// Logger::info('传参：' . $fields, 'prm');
		// 此处需要使用给定的签名算法
		$x_signature = self::sign($fields, str_random(10));
		$timeOut = config('extension.yitu_api_timeout');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'x-access-id:' . $x_access_id, 'x-signature:' . $x_signature]);
		$SSL = substr($api_url, 0, 8) == "https://" ? true : false;
		if ($SSL) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		}
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$output = curl_exec($ch);
		curl_close($ch);
        $user_id = Session::get('user_id');
		Logger::info('用户 userid  '.$user_id.'___'. '调用人脸比对接口，原始返回值为：' . $output, 'facecompare-return');
		$saveData = [
			'type' => 2,
			'key_1' => str_replace(storage_path(), '', $dbImg),
			'key_2' => str_replace(storage_path(), '', $queryImg),
			'result' => $output ? $output : '',
			'createtime' => time()
		];
		self::saveYituReturn($saveData);
		return ['status' => 'compare_original_return_value', 'data' => $output];
	}

	/**
	 * 查询依图接口缓存数据
	 * @param $condition
	 * @return array
	 */
	public static function getYituCache($condition)
	{
		$res = YituModel::getInterfaceResult($condition);
		if (!empty($res)) {
			return ['isCache' => true, 'data' => $res->result];
		}
		return ['isCache' => false, 'data' => '没有缓存数据！'];
	}

	/**
	 * 保存依图接口返回结果
	 * @param $data
	 * @return mixed
	 */
	private static function saveYituReturn($data)
	{
		$res = YituModel::saveInterfaceResult($data);
		return $res;
	}

	/**
	 * 依图接口签名算法
	 * @param $data
	 * @param string $custom
	 * @return string
	 */
	private static function sign($data, $custom = 'bqjr')
	{
		$accesskey = config('extension.face_compare_accesskey');
		$md5 = bin2hex(substr(md5($data), 8, 16));
		$timestamp = self::integerToBytes(time());
		$randomstr = str_random(8);
		$str = $accesskey . $md5 . $timestamp . $randomstr . $custom;
		$rsa = new RSA(config('extension.yitu_public_key'));
		$res = $rsa->encrypt($str, 'hex');
		return $res;
	}

	/**
	 * 按照指定算法，获取四个字节的时间戳
	 * @param $val
	 * @return string
	 */
	private static function integerToBytes($val)
	{
		$byt = array();
		$byt[0] = ($val & 0xff);
		$byt[1] = ($val >> 8 & 0xff);
		$byt[2] = ($val >> 16 & 0xff);
		$byt[3] = ($val >> 24 & 0xff);
		return chr($byt[0]) . chr($byt[1]) . chr($byt[2]) . chr($byt[3]);
	}


    /**
     * post方式请求依图接口
     * @param $fields  请求数据
     * @param $api_url  请求地址
     * @return mixed
     */
    public static function sendToYtByPost($fields=array(),$api_url){
        //$api_url = config('extension.yitu_api_decode');
        $x_access_id = config('extension.face_compare_accessid');
        $fields = json_encode($fields);

        // 此处需要使用给定的签名算法
        $x_signature = self::sign($fields, str_random(10));
        $timeOut = config('extension.yitu_api_timeout');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'x-access-id:' . $x_access_id, 'x-signature:' . $x_signature]);
        $SSL = substr($api_url, 0, 8) == "https://" ? true : false;
        if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}