<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Model\Backend;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserInfo extends Model
{

    protected $table = 'sync_user_info';

    public $timestamps = true;

    protected $guarded = [];

    /**
     * 通过user_id列表获取用户名
     * 
     * @param unknown $user_id_list            
     */
    public static function getUserNameListByID($user_id_list)
    {
        $list = self::whereIn('USERID', $user_id_list)->select('USERID', 'USERNAME')->get();
        return $list;
    }

    /**
     * 通过user_id列表获取上级ID列表
     * 
     * @param unknown $user_id_list            
     */
    public static function getSuperiorListByIDList($user_id_list)
    {
        $list = self::whereIn('USERID', $user_id_list)->select('USERID', 'SUPERID')->get();
        return $list;
    }
    
    /**
     * 通过用户姓名获取下级id列表
     * @param unknown $user_id
     */
    public static function getSubordinateListByName($user_name)
    {
        $res=self::getUserIDByName($user_name);
        $user_id_list=[];
        foreach ($res as $v)
        {
            $user_id_list[]=$v->USERID;
        }
        $subordinate_list=self::getSubordinateListByIDList($user_id_list);
        return $subordinate_list;
    }
    
    /**
     * 通过用户id列获取下级id列表
     * @param unknown $user_id
     */
    public static function getSubordinateListByIDList($user_id)
    {
        $list=self::whereIn('SUPERID',$user_id)->select('USERID')->get();
        return $list;
    }

    /**
     * 通过用户名查询id
     * 
     * @param unknown $user_name            
     */
    public static function getUserIDByName($user_name)
    {
        $list = self::where('USERNAME', 'like', '%' . $user_name . '%')->select('USERID', 'USERNAME')
            ->orderBy('USERNAME', 'ASC')
            ->get();
        return $list;
    }
}