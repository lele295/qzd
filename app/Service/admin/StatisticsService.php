<?php

namespace App\Service\admin;

use App\Model\Admin\CustomerModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class StatisticsService extends Service
{
    public function __construct(){

    }

    /*
     * 获取转化率
     * old_conversion缓存就
     */
    public function conversion_total(){
        if(!Cache::has("conversion")){
            $customermodel = new CustomerModel();
            $cus_res = $customermodel->get_admin_by_real_name();
            if(Cache::has("old_conversion")){
                $all_total = Cache::get("old_conversion");
            }
            foreach($cus_res as $i=>$val){
                $all_sub = $customermodel->get_loan_by_conversion_weeks($val->EVENTNAME);
                $all_sub["all"] = $val->total;
                $param = $val->EVENTNAME;
                $all_total[$param] = $all_sub;
            }
            Cache::forever("old_conversion", $all_total);

            $data["total_all"] = $all_total;
            $data["total_td"] = $customermodel->get_loan_by_conversion_name();

            $expiresAt = Carbon::now()->addMinutes(60);
            Cache::put("conversion", $data, $expiresAt);
        }
        return Cache::get("conversion");
    }

    /*
     * 搜索获取转化率
     */
    public function search_conversion_total($EventName){
        $cache_data = Cache::get("conversion");
        foreach($cache_data["total_all"] as $i=>$val){
            if(stristr($i, $EventName)){
                $search_data["total_all"][$i] = $val;
            }
        }
        foreach($cache_data["total_td"] as $i=>$val){
            if(stristr($i, $EventName)){
                $search_data["total_td"][$i] = $val;
            }
        }
        return $search_data;
    }

    /*
     * 获取申请率
     */
    public function apply_total(){
        if(!Cache::has("apply-rate")) {
            $customermodel = new CustomerModel();
            $cus_res = $customermodel->get_admin_by_real_name();
            if(Cache::has("old_apply_rate")){
                $all_total = Cache::get("old_apply_rate");
            }
            foreach ($cus_res as $i => $val) {
                $all_sub = $customermodel->get_auth_by_weeks($val->EVENTNAME);
                $all_sub["all"] = $val->total;
                $param = $val->EVENTNAME;
                $all_total[$param] = $all_sub;
            }
            Cache::forever("old_apply_rate", $all_total);

            $data["total_all"] = $all_total;
            $data["total_td"] = $customermodel->get_auth_by_real_name();
            $expiresAt = Carbon::now()->addMinutes(60);
            Cache::put("apply-rate", $data, $expiresAt);
        }
        return Cache::get("apply-rate");
    }

    /*
     * 搜索获取申请率
     */
    public function search_apply_total($EventName){
        $cache_data = Cache::get("apply-rate");
        foreach($cache_data["total_all"] as $i=>$val){
            if(stristr($i, $EventName)){
                $search_data["total_all"][$i] = $val;
            }
        }
        foreach($cache_data["total_td"] as $i=>$val){
            if(stristr($i, $EventName)){
                $search_data["total_td"][$i] = $val;
            }
        }
        return $search_data;
    }

    /*
     * 获取足额率
     */
    public function full_total(){
        if(!Cache::has("full_total")) {
            $customermodel = new CustomerModel();
            $cus_res = $customermodel->get_auth_by_avg_money();
            foreach($cus_res as $i=>$val){
                $all_sub = $customermodel->get_auth_avg_money_weeks($val->EventName);
                $all_sub["loan_amount"] = $val->loan_amount;
                $all_sub["CreditLimit"] = $val->CreditLimit;
                $param = $val->EventName;
                $all_total[$param] = $all_sub;
            }
            $data["total_all"] = $all_total;
            $expiresAt = Carbon::now()->addMinutes(60);
            Cache::put("full_total", $data, $expiresAt);
        }
        return Cache::get("full_total");
    }

    /*
     * 搜索获取足额率
     */
    public function search_full_total($EventName){
        $cache_data = Cache::get("full_total");
        foreach($cache_data["total_all"] as $i=>$val){
            if(stristr($i, $EventName)){
                $search_data["total_all"][$i] = $val;
            }
        }
        return $search_data;
    }

    /*
     * 获取通过率
     */
    public function pass_total(){
        if(!Cache::has("pass_rate")) {
            $customermodel = new CustomerModel();
            $cus_res = $customermodel->get_loan_by_pass_count();
            foreach ($cus_res as $i => $val) {
                $all_sub = $customermodel->get_loan_by_pass_weeks($val->EventName);
                $all_sub["total"] = $val->total;
                $all_sub["no_pass"] = $val->no_pass;
                $param = $val->EventName;
                $all_total[$param] = $all_sub;
            }
            $data["total_all"] = $all_total;
            $expiresAt = Carbon::now()->addMinutes(60);
            Cache::put("pass_rate", $data, $expiresAt);
        }
        return Cache::get("pass_rate");
    }

    /*
     * 搜索获取通过率
     */
    public function search_pass_total($EventName){
        $cache_data = Cache::get("pass_rate");
        foreach($cache_data["total_all"] as $i=>$val){
            if(stristr($i, $EventName)){
                $search_data["total_all"][$i] = $val;
            }
        }
        return $search_data;
    }

    /*
     * 转化率下载
     */
    public function down_conversion_total(){
        $down_data = $this->conversion_total();

        $csvDocumentService = new CsvDocumentService();
        $header = array('活动名称', '第1周' ,'第2周', '第3周', '第4周', '第5周', '第6周', '第7周', '第8周', '第9周', '第10周', '第11周', '第12周', '第13周', '累计', '总量');
        $filename = "转化率统计".date("Y-m-d");
        $fp = $csvDocumentService->down($header,$filename);

        foreach ($down_data["total_all"] as $key => $val) {
            $row = array();
            array_push($row, $csvDocumentService->text_format($key));
            array_push($row, $csvDocumentService->text_format(round($val[0]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[1]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[2]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[3]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[4]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[5]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[6]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[7]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[8]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[9]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[10]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[11]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[12]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(isset($down_data["total_td"][$key])?round($down_data["total_td"][$key]/$val["all"],5)*100 ."%":0));
            array_push($row, $csvDocumentService->text_format($val["all"]));
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    /*
     * 申请率下载
     */
    public function down_apply_total(){
        $down_data = $this->apply_total();

        $csvDocumentService = new CsvDocumentService();
        $header = array('活动名称', '第1周' ,'第2周', '第3周', '第4周', '第5周', '第6周', '第7周', '第8周', '第9周', '第10周', '第11周', '第12周', '第13周', '累计', '总量');
        $filename = "申请率统计".date("Y-m-d");
        $fp = $csvDocumentService->down($header,$filename);

        foreach ($down_data["total_all"] as $key => $val) {
            $row = array();
            array_push($row, $csvDocumentService->text_format($key));
            array_push($row, $csvDocumentService->text_format(round($val[0]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[1]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[2]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[3]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[4]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[5]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[6]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[7]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[8]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[9]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[10]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[11]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[12]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val[12]/$val["all"],5)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(isset($down_data["total_td"][$key])?round($down_data["total_td"][$key]/$val["all"],5)*100 ."%":0));
            array_push($row, $csvDocumentService->text_format($val["all"]));
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    /*
     * 足额率下载
     */
    public function down_full_total(){
        $down_data = $this->full_total();

        $csvDocumentService = new CsvDocumentService();
        $header = array('活动名称', '第1周' ,'第2周', '第3周', '第4周', '第5周', '第6周', '第7周', '第8周', '第9周', '第10周', '第11周', '第12周', '第13周', '累计', '平均最高额');
        $filename = "足额率统计".date("Y-m-d");
        $fp = $csvDocumentService->down($header,$filename);

        foreach ($down_data["total_all"] as $key => $val) {
            $row = array();
            array_push($row, $csvDocumentService->text_format($key));
            array_push($row, $csvDocumentService->text_format($val[0]));
            array_push($row, $csvDocumentService->text_format($val[1]));
            array_push($row, $csvDocumentService->text_format($val[2]));
            array_push($row, $csvDocumentService->text_format($val[3]));
            array_push($row, $csvDocumentService->text_format($val[4]));
            array_push($row, $csvDocumentService->text_format($val[5]));
            array_push($row, $csvDocumentService->text_format($val[6]));
            array_push($row, $csvDocumentService->text_format($val[7]));
            array_push($row, $csvDocumentService->text_format($val[8]));
            array_push($row, $csvDocumentService->text_format($val[9]));
            array_push($row, $csvDocumentService->text_format($val[10]));
            array_push($row, $csvDocumentService->text_format($val[11]));
            array_push($row, $csvDocumentService->text_format($val[12]));
            array_push($row, $csvDocumentService->text_format(isset($val["loan_amount"])?round($val["loan_amount"]/$val["CreditLimit"],5)*100 ."%":0));
            array_push($row, $csvDocumentService->text_format(round($val["CreditLimit"])));
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    /*
     * 通过率下载
     */
    public function down_pass_rate(){
        $down_data = $this->pass_total();

        $csvDocumentService = new CsvDocumentService();
        $header = array('活动名称', '第1周' ,'第2周', '第3周', '第4周', '第5周', '第6周', '第7周', '第8周', '第9周', '第10周', '第11周', '第12周', '第13周', '累计', '总数');
        $filename = "通过率统计".date("Y-m-d");
        $fp = $csvDocumentService->down($header,$filename);

        foreach ($down_data["total_all"] as $key => $val) {
            $row = array();
            array_push($row, $csvDocumentService->text_format($key));
            array_push($row, $csvDocumentService->text_format($val[0]));
            array_push($row, $csvDocumentService->text_format($val[1]));
            array_push($row, $csvDocumentService->text_format($val[2]));
            array_push($row, $csvDocumentService->text_format($val[3]));
            array_push($row, $csvDocumentService->text_format($val[4]));
            array_push($row, $csvDocumentService->text_format($val[5]));
            array_push($row, $csvDocumentService->text_format($val[6]));
            array_push($row, $csvDocumentService->text_format($val[7]));
            array_push($row, $csvDocumentService->text_format($val[8]));
            array_push($row, $csvDocumentService->text_format($val[9]));
            array_push($row, $csvDocumentService->text_format($val[10]));
            array_push($row, $csvDocumentService->text_format($val[11]));
            array_push($row, $csvDocumentService->text_format($val[12]));
            array_push($row, $csvDocumentService->text_format(round(($val["total"]-$val["no_pass"])/$val["total"],3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format($val["total"]));
            fputcsv($fp, $row);
        }
        fclose($fp);
    }
}