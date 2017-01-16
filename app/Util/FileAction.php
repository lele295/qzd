<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/3/31
 * Time: 11:18
 */

namespace App\Util;


class FileAction
{
    static public function remove_storage_file($path){
        $path = storage_path(stristr($path,'/uploads'));
        //dd($path);
        if(file_exists($path)){
            unlink($path);
            return true;
        }else{
            throw new \Exception;
        }
    }
}