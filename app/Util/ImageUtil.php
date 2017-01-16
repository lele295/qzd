<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/27
 * Time: 13:46
 */

namespace App\Util;


use App\Log\Facades\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImageUtil
{
    private $maxwidth;
    private $maxheight;
    public function __construct($maxwidth = 600,$maxheight = 480){
        $this->maxwidth = $maxwidth;
        $this->maxheight = $maxheight;
    }

    public function setMaxWidth($maxwidth){
        $this->maxwidth = $maxwidth;
    }
    public function setMaxHeight($maxheight){
        $this->maxheight = $maxheight;
    }

    public function resizeImage($path){
        try {
            $img = ImageCreateFromJpeg($path);
            $pic_width = imagesx($img);
            $pic_height = imagesy($img);
            $ratio = 1;
            $resizewith_tag = false;
            $resizeheight_tag = false;
            if ($pic_width > $this->maxwidth) {
                $widthratio = $this->maxwidth / $pic_width;
                $resizewith_tag = true;
            }
            if ($pic_height > $this->maxheight) {
                $heightratio = $this->maxheight / $pic_height;
                $resizeheight_tag = true;
            }

            if ($resizewith_tag && $resizeheight_tag) {
                if ($widthratio < $heightratio) {
                    $ratio = $widthratio;
                } else {
                    $ratio = $heightratio;
                }
            }
            if ($resizewith_tag && !$resizeheight_tag) {
                $ratio = $widthratio;
            }
            if ($resizeheight_tag && !$resizewith_tag) {
                $ratio = $heightratio;
            }
            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;
            if (function_exists("imagecopyresampled")) {
                $newim = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($newim, $img, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            } else {
                $newim = imagecreate($newwidth, $newheight);
                imagecopyresized($newim, $img, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            }
            ob_start();
            imagejpeg($newim);
            $final_image = ob_get_contents();
            imagedestroy($newim);
            ob_end_clean();
            return $final_image;
        }catch (\Exception $e){
            Logger::info('图片路径：'.$path,'imagecreatefromjpeg');
    //        Logger::error($e);
            return file_get_contents($path);
        }
    }

}