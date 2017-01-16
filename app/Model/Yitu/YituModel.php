<?php
namespace App\Model\Yitu;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Log\Facades\Logger;
use Illuminate\Support\Facades\Session;
/**
 * Author: CHQ
 * Time: 2016/7/6 10:46
 * Usage: 依图接口查询结果缓存
 * Update: 2016/08/01 14:30 增加日志
 */
class YituModel extends Model
{
	protected static $yitu_table = 'yitu_query_cache';

	public static function getInterfaceResult($condition)
	{
		// DB::enableQueryLog();
		$data = DB::table(self::$yitu_table)->where(function ($query) use ($condition) {
			$query->where('type', '=', $condition['type']);
			$query->where('key_1', '=', $condition['key_1']);
			if (!empty($condition['key_2'])) {
				$query->where('key_2', '=', $condition['key_2']);
			}
		})->first();
		// Log::info(DB::getQueryLog());
		return $data;
	}

	public static function saveInterfaceResult($data)
	{
        $data['user_id'] = Session::get('user_id');
		$insert = DB::table(self::$yitu_table)->insertGetId([
			'type' => $data['type'],
			'key_1' => $data['key_1'],
			'key_2' => $data['key_2'],
			'result' => $data['result'],
			'createtime' => $data['createtime'],
            'user_id' => $data['user_id']
		]);
		return $insert;
	}

	public static function cleanExpiredRecords()
	{
		$currentZero = strtotime(date('Y-m-d 00:00:00', time()));
		// 2016-08-01 14:39增加日志
		$count = DB::table(self::$yitu_table)->where('createtime', '<', $currentZero)->count();
		$delete = DB::table(self::$yitu_table)->where('createtime', '<', $currentZero)->delete();
		Logger::info('删除依图查询缓存。共删除了' . $count . '条数据','clean-yitu-cache');
		return $delete;
	}

}
