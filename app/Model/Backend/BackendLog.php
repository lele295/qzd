<?php

namespace App\Models;
//use DB;
use Illuminate\Database\Eloquent\Model;

class BackendLog extends Model
{

    protected $table = 'backend_log';

    //模板类型
    public static $models = [
        'apply_loan' => '贷款管理-申请贷款',
        'quota_calculation' => '贷款管理-额度测算',
        'activity_settings' => '活动-活动设置',
        'prize_settings' => '活动-奖品设置',
        'recommend_settings' => '推荐奖励活动-活动设置',
        'recommend_reward' => '推荐奖励活动-奖励发放',
    ];

    public static function log($data){
        return self::insert(['user_id' => $data['user_id'], 'user_name' => $data['user_name'], 'ip' => $data['ip'], 'model' => self::$models[$data['model']], 'remark' => $data['remark'], 'add_time' => $data['add_time']]);
    }

}
