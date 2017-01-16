<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/8
 * Time: 14:43
 */

namespace App\Util;


class AdminRule extends Rule
{
    static public function admin_bank_rule($array){
        $rule = array(
            'real_name'=>'required',
            'mobile'=>'required',
            'number'=>'required',
            'customer_id'=>'required',
            'open_bank'=>'required',
            'id_card'=>'required'
        );
        $result = self::validator($rule,$array);
        return $result;
    }

    static public function admin_auth($array){
        $rule = array(
            'mobile'=>'required',
            'real_name'=>'required',
            'id_card'=>'required',
        );
        $result = self::validator($rule,$array);
        return $result;
    }

    static public function admin_auth_rule($array){
        $rule = array(
            'real_name'=>'required',
            'id_card'=>'required',
        );
        $result = self::validator($rule,$array);
        return $result;
    }

}