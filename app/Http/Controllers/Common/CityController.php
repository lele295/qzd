<?php
namespace App\Http\Controllers\Common;
use App\Http\Controllers\Controller;
use App\Model\Base\SyncCodeLibrary;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PlanConteroller
 *
 * @author Administrator
 */
class CityController extends Controller{
    public static function getIndex() {
        echo json_encode(SyncCodeLibrary::classificationForCity(), JSON_UNESCAPED_UNICODE);
    }
}