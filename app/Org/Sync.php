<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Org;

use App\Models\SyncCodeLibrary;

/**
 * Description of Sync
 *
 * @author lenovo
 */
class Sync {

    public static function areaCodeToCity() {
        $res = SyncCodeLibrary::areaCodeToCity();
        $firstLevel = array();
        $secondeLevel = array();
        foreach ($res as $item) {
            if (($item->ITEMNO % 10000) == 0) {
                array_push($firstLevel, array('name' => $item->ITEMNAME, 'value' => $item->ITEMNO, 'data' => array()));
            }
        }

        foreach ($res as $item) {
            if (($item->ITEMNO % 10000) == 0) {
                continue;
            }
            $parentValue = intval($item->ITEMNO / 10000) * 10000;
            foreach ($firstLevel as $key => $val) {
                if ($val['value'] == $parentValue) {
                    array_push($firstLevel[$key]['data'], array('name' => str_replace($val['name'], '', $item->ITEMNAME), 'val' => $item->ITEMNO));
                    break;
                }
            }
        }
        return $firstLevel;
    }

}
