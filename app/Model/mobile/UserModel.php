<?php

namespace App\Model\mobile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{
    protected $table = 'users';

    /**用户订单列表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userInfo()
    {
        return $this->hasMany('App\Model\mobile\UserInfoModel', 'userid', 'id');
    }


    public function get_user_info_by_openid($openid){
        $data = DB::table($this->table)->where('openid',$openid)->first();
        return $data;
    }
}
