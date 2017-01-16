<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/4/1
 * Time: 15:03
 */

namespace App\Util;


class AppRule extends Rule
{
    static function register_rule($info){
        $rule = array(
            'mobile' => 'required|numeric|min:10|unique:users',
            'password' => 'required|min:5',
            'mobile_code' => 'required',
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static function login_rule($info){
        $rule = array(
            'mobile'=>'required|numeric|min:10',
            'password' => 'required|min:5'
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static function resetPwd_rule($info){
        $rule = array(
            'mobile'=>'required',
            'password'=>'required|confirmed',
            'mobile_code'=>'required'
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static function forgetPwd_rule($info){
        $rule = array(
            'mobile' => 'required|numeric|min:10',
            'password' => 'required|min:5',
            'mobile_code' => 'required'
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static function updatePwd_rule($info){
        $rule = array(
            'old_password' => 'required|min:5',
            'password' => 'required|min:5'
        );
        $result = self::validator($rule,$info);
        return $result;
    }
}