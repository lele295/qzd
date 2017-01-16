<?php
namespace App\Service\mobile;



use App\Log\Facades\Logger;
use App\Model\Base\SuggestModel;
use Illuminate\Support\Facades\Log;

class SuggestService extends Service
{
    /*
     * 用户建议统计,解析标题，按总数排序
     */
    public function class_stat(){
        $suggest_m = new SuggestModel();
        $stats = $suggest_m->stat_suggest();

        foreach($stats as $i=>$val) {
            $val->name = $suggest_m->parse_title($val->title);
        }
        foreach($stats as $i=>$val){
            $inta = $val->total;
            $key = $i;
            for($j = $i+1; $j<count($stats); $j++){
                if($inta < $stats[$j]->total){
                    $inta = $stats[$j]->total;
                    $key = $j;
                }
            }
            if($key != $i) {
                $max_arr = $stats[$key];
                $stats[$key] = $stats[$i];
                $stats[$i] = $max_arr;
            }
        }
        //die(print_r($stats));
        return $stats;
    }
}
