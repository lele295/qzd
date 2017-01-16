<?php

namespace App\Http\Controllers\pc;

use App\Util\FileReader;
use App\Util\LinkFace;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
   //
    public function index()
    {
        return view('pc.index');
    }

    public function getDemo(){

        $linkFace = LinkFace::getInstance();
//        $src = FileReader::get_storage_path('/uploads/wechat/2016-09-08/1473328721_.jpg');
       $src = FileReader::get_storage_path('/uploads/wechat/2016-09-10/1473473869_.jpg');
//        $des = FileReader::get_storage_path('/uploads/wechat/2016-09-08/1473328721_.jpg');
//$res = $linkFace->faceCompare($src,$des);
        $res = $linkFace->characterOCR($src);
        var_dump($res);
        exit;
    }

}
