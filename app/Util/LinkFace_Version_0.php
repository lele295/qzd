<?php
namespace App\Util;

use App\Log\Facades\Logger;

/**
 * Time: 2016/6/8 13:59
 * Usage: 人脸识别接口
 * Update: 重写类，改为单例模式，增加类方法(识别身份证正反面文字)
 */
class LinkFace_Version_0
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

	static public function isOn()
	{
		return config('extension.face_link_switch');
	}

	/**
	 * @param string $selfiePath 图片1路径
	 * @param string $historicalPath 图片2路径
	 * @return bool
	 */
	static public function compareOriginalVersion($selfiePath, $historicalPath)
	{
		if (!config('extension.face_link_switch')) {
			return true;
		}
		$testurl = 'https://v1-auth-api.visioncloudapi.com/identity/historical_selfie_verification';  // url
		$selfieContent = new \CURLFile($selfiePath);
		$historicalContent = new \CURLFile($historicalPath);
		$post_data = array(
			'api_id' => config('extension.face_link_key'),
			'api_secret' => config('extension.face_link_secret'),
			'selfie_file' => $selfieContent,
			'historical_selfie_file' => $historicalContent,
			'auto_rotate' => true
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $testurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//您可以根据需要，决定是否打开SSL验证
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$output = curl_exec($ch);
		curl_close($ch);
		Logger::info($output, 'facelink');
		$res = json_decode($output);
		if (isset($res->status) && $res->status == 'OK') {
			return ($res->confidence >= config('extension.face_link_fate')) ? true : false;
		} else {
			return false;
		}
	}

	// 人脸识别
	public static function compare($selfiePath, $historicalPath)
	{
		$obj = self::getInstance();
		return $obj->compareOriginalVersion($selfiePath, $historicalPath);
	}

	/**
	 * 识别身份证文字
	 * @param string $imgPath 图片路径
	 * @param string $type 类型，type='front'代表身份证正面，type='back'代表身份证反面
	 * @return array
	 */
	public function characterRecognize($imgPath, $type)
	{
		if (!is_string($imgPath) || !is_string($type) || empty($imgPath) || empty($type)) {
			return ['status' => false, 'data' => '非法参数！'];
		}
		if (!in_array($type, ['front', 'back'])) {
			return ['status' => false, 'data' => '请选择是正面还是反面！'];
		}
		$interfaceUrl = 'https://v1-auth-api.visioncloudapi.com/ocr/idcard';
		$imgContent = new \CURLFile($imgPath);
		$fields = [
			'api_id' => config('extension.face_link_key'),
			'api_secret' => config('extension.face_link_secret'),
			'file' => $imgContent,
			'auto_rotate' => true,
			'side' => $type
		];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $interfaceUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//您可以根据需要，决定是否打开SSL验证
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$output = curl_exec($ch);
		curl_close($ch);
		Logger::info('人脸识别OCR接口返回值为：' . $output, 'OCR-interface-return');
		$res = json_decode($output, true);
		if (isset($res['status']) && ($res['status'] == 'OK')) {
			$success = in_array(false, $res['validity'], true) ? false : true;
			return ['status' => $success, 'data' => json_encode($res['validity'], JSON_UNESCAPED_UNICODE)];
		} else {
			return ['status' => false, 'data' => $res['status']];
		}
	}
}