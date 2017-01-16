<?php
namespace App\Util;
use Illuminate\Support\Facades\Log;

/**
 * 读取storage/upload目录下上传的文件
 * Class FileReader
 * @package App\Util
 */

class FileReader
{
    /*
     * 读取storage目录下的图片文件 ，以base_64数据的形式返回
     */
    static public function read_storage_image_file($path,$show = true){
        $path = storage_path(stristr($path,'/uploads'));
        if(file_exists($path)){
            $image_info = getimagesize($path);
            if($show){
                $info = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($path)));
            }else{
                $info = base64_encode(file_get_contents($path));
            }
            return $info;
        }else{
            throw new \Exception;
        }
    }


    static public function read_storage_image_resize_file($path,$show = true){
        $path = storage_path().stristr($path,'/uploads');
        if(file_exists($path)){

            $imageUtil = new ImageUtil();
            $info = chunk_split(base64_encode($imageUtil->resizeImage($path)));
            if($show){
                $image_info = getimagesize($path);
                $info = "data:{$image_info['mime']};base64,".chunk_split($info);
            }
            return $info;

        }else{
            return '';
            Log::error("路径不存在".$path);
            throw new \Exception;
        }
    }

    static public function read_storage_image_resize_file_new($path,$show = true){
        if(file_exists($path)){
            $imageUtil = new ImageUtil();
            $info = chunk_split(base64_encode($imageUtil->resizeImage($path)));
            if($show){
                $image_info = getimagesize($path);
                $info = "data:{$image_info['mime']};base64,".chunk_split($info);
            }
            return $info;
        }else{

            Log::error("路径不存在".$path);
            throw new \Exception;
        }
    }

    static public function read_storage_log_file($path){
        $path = storage_path().$path;
        if(file_exists($path)){
            $fp = fopen($path,'r');
            $array = array();
            while(!feof($fp)){
                array_push($array,fgets($fp));
            }
            fclose($fp);
            return $array;
        }else{
            return '';
        }
    }

    /**
     * 读取storage目录下的html文件，以base_64形式返回
     * @param $path
     * @return string
     */
    static public function read_storage_text_file($path){
        $path = self::get_storage_path($path);
        if(file_exists($path)){
            $data = file_get_contents($path);
            if(self::is_base64_encoded($data)){
                $info = $data;
            }else{
                $info = base64_encode(file_get_contents($path));
            }
            return $info;
        }else{
            Log::error("路径不存在".$path);
            throw new \Exception;
        }
    }

    /**
     * 读取images二进制文件，进行hash运算，与接口对接
     * @param $path
     * @return string
     */
    static public function read_storage_text_file_to_binary($path){
        $path = self::get_storage_path($path);
        //dd($path);
        if(file_exists($path)){
            $data = file_get_contents($path);
            //返回hash值
            $data = strtoupper(hash('sha1' , $data , false));
            return $data;
        }else{
            Log::error("路径不存在".$path);
            throw new \Exception;
        }
    }


    /**
     *判断是否经过base64位加密
     * @param $content
     * @return bool
     */
    static public function is_base64_encoded($content)
    {
        if(self::is_html_tags($content)){
            return false;
        }else{
            if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $content)) {
                return true;
            } else {
                return false;
            }
        }
    }


    /**
     * 判断是否存在HTML标签
     * @param $content
     * @return bool
     */
    static public function is_html_tags($content){
        if($content != strip_tags($content)){
            return true;
        }else{
            return false;
        }
    }

    static public function get_storage_path($path){
        $path = stristr($path,'/uploads');
        $path = storage_path().$path;
        return $path;
    }

    /**
     * 查看文件是否存在
     * @param $path
     * @return bool
     */
    static public function get_file_exists($path){
        $path = self::get_storage_path($path);
        if(file_exists($path)){
            return true;
        }else{
            return false;
        }
    }

    static public function get_log_path(){
        $path = storage_path().'/logs/';
        return $path;
    }
}