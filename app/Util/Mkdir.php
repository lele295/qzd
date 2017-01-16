<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/1
 * Time: 15:52
 */

namespace App\Util;

/**
 * 目录工具类
 * Class Mkdir
 * @package App\Util
 */
class Mkdir
{
    static public function create_dir($path){
        if (!file_exists($path)) {
            if (!mkdir($path, 0777, true)) {
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }
    }
}