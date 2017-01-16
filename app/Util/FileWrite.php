<?php
namespace App\Util;


use App\Log\Facades\Logger;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;

class FileWrite
{
    /**
     * 根据内容生成新的文件
     * @param $path
     * @param $name
     * @param $content
     * @return bool
     */
    static public function write_file($path,$name,$content){
        try {
            if (Mkdir::create_dir($path)) {
                if (ends_with($path, '/') || ends_with($path, '\\')) {
                    $path = $path . $name;
                } else {
                    $path = $path . '/' . $name;
                }
                $fp = fopen($path, 'w');
                if ($fp) {
                   if (false === fwrite($fp, $content)) {
                     return false;
                   }
                } else {
                     return false;
                }
                fclose($fp);
                return $path;
            } else {
                return false;
            }
        }catch(\Exception $e){
            Logger::info('---------'.$path.'-'.$name.'文件生成失败------------');
            throw $e;
        }
    }

    static public function write_storage_file($path,$name,$content){
        $path = storage_path().$path;
        $info = self::write_file($path,$name,$content);
        if($info){
            //return ltrim($info,storage_path()); 这个方法需要做一下修改，这个函数在这里不适合使用 里面的u会把upload这个前面的去掉
            //获取u在字符串中最后一次出现的位置，就是upload这里的u
            $pos = strripos($info,'u');
            return substr($info,$pos-1);
        }else{
            return false;
        }
    }

    static public function write_storage_base64_encode_file($path,$name,$content){
        $info = self::write_storage_file($path,$name,base64_encode($content));
        return $info;
    }

    static public function remove_storage_file($path){

    }

}