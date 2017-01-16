<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/30
 * Time: 17:22
 */

namespace App\Service\admin;


use App\Api\api\AnApi;
use App\Log\Facades\Logger;
use App\Model\Admin\AdminLoanModel;
use App\Model\Admin\AdminMessageModel;
use App\Model\Admin\CustomerModel;
use App\Model\Admin\LoanAdminModel;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanModel;
use App\Model\Base\LoanStatusModel;
use App\Model\Base\UniqueCodeModel;
use App\Service\mobile\CenterService;
use App\Service\mobile\WeTemplateService;
use App\Util\CodeLibrary;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use App\Api\api\WechatApi;
use App\Model\Base\SyncModel;

class LoanService extends Service
{
    public function __construct(){

    }

    public function get_loan_list_with_condition($condition){
        if(empty($condition)){
            $empty = new \App\Util\EmptyPaginate();
            return $empty;
        }else{
            return $this->get_loan_list_without_condition($condition);
        }
    }

    public function get_loan_list_without_condition($condition){
        if(empty($condition)){
            $loanAdminModel = new LoanAdminModel();
            $info = $loanAdminModel->get_loan_list();
        }else{
            $searchService = new SearchService();
            $info = $searchService->get_loan_index_search($condition);
        }
        return $info;
    }

    public function get_loan_list_for_sale_with_condition($condition,$admin_id){
        if(empty($condition)){
            $adminLoanModel = new AdminLoanModel();
            return $adminLoanModel->get_admin_loan_un_complete($admin_id);
        }else{
            return $this->get_loan_list_for_sale($condition,$admin_id);
        }
    }

    public function get_loan_list_for_sale($condition,$admin_id){
        if(empty($condition)){
            $loanAdminModel = new LoanAdminModel();
            $info = $loanAdminModel->get_loan_for_sale_list($admin_id);
        }else{
            $searchService = new SearchService();
            $info = $searchService->get_sale_loan_condition($condition,$admin_id);
        }
        return $info;
    }

    public function get_loan_status(){
        $info = LoanStatusModel::get_status('loan');
        return $info;
    }

    public function get_loan_by_status($status){
        $loanModel = new LoanAdminModel();
        $info = $loanModel->get_loan_by_status($status);
        $string = '借钱么订单合同号为：';
        if($info){
            foreach($info as $val){
                $string = $string.$val->pact_number.',';
            }
            $string = $string.'的状态为空';
            Logger::info($string);
            return $string;
        }else{
            return '';
        }

    }

    /*
     * 导出订单信息
     */
    public function get_out_loan_info($condition){
        set_time_limit(7200);
        $csvDocumentService = new CsvDocumentService();
        //$header = array('订单id', '姓名' ,'身份证号', '手机号', '城市', '安硕编号', '申请时间', "提单时间", '联系时间', '合同号','最高贷款额', '贷款金额', '贷款期数', '安硕状态', '取消原因', '来源', '活动名称');
        // 2016-05-30新增“每月还款额” 2016-06-13添加“录单员姓名”
        //$header = array('订单id', '姓名' ,'身份证号', '手机号', '城市', '安硕编号', '申请时间', "提单时间", '联系时间', '合同号','最高贷款额', '贷款金额', '贷款期数', '每月还款额', '安硕状态', '取消原因', '取消类型', '来源', '活动名称', '录单员');
        $header = array('订单id', '申请时间', '提单时间', '联系时间', '合同号', '贷款金额', '贷款期数', '每月还款额', '编号', '姓名', '性别', '身份证id', '省份', '城市', '现住址', '工作地址', '最高贷款额', '最高还款额', '电话', 'POS代码', 'POS门店', '活动客户所属阶段', '产品类型', '产品特征', '活动名称', '活动有效期', '安硕状态', '取消原因', '取消类型', '来源', '录单员姓名');
        $filename = "借款列表".date('Y-m-d',time());
        $fp = $csvDocumentService->down($header,$filename);
        $loanAdminMdoel = new LoanAdminModel();
        if(empty($condition)){
            $res = $info = $loanAdminMdoel->get_loan_list('down');
        }else{
            $searchService = new SearchService();
            $res = $info = $searchService->get_loan_index_search($condition,'down');
        }

        if(!$res){
            return false;
        }
        //计数器
        $cnt = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 1000;
        foreach ($res as $info) {
            $cnt++;
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }
            $row = array();
            array_push($row, $csvDocumentService->text_format($info->id));  //订单id
            array_push($row, $csvDocumentService->text_format($info->created_at));  //申请时间
            array_push($row, $csvDocumentService->text_format($info->updated_at));  //提单时间
            array_push($row, $csvDocumentService->text_format($info->contact_time));    //联系时间
            array_push($row, $csvDocumentService->text_format($info->pact_number)); //合同号
            array_push($row, $csvDocumentService->text_format($info->loan_amount)); //贷款金额
            array_push($row, $csvDocumentService->text_format($info->loan_period)); //贷款期数
            array_push($row, $csvDocumentService->text_format($info->month_payment));   //每月还款额
            array_push($row, $csvDocumentService->text_format($info->CustomerID));  //编号
            array_push($row, $csvDocumentService->text_format($info->real_name));   //姓名
            array_push($row, $csvDocumentService->text_format(substr($info->id_card, strlen($info->id_card)-2,1)%2 == 1?"男":"女"));    //性别
            array_push($row, $csvDocumentService->text_format($info->id_card, true));   //身份证id
            if($info->WorkAdd){
                $position = strrpos($info->WorkAdd,'省');
                if($position){
                    $province = substr($info->WorkAdd,0,$position).'省';
                    $city = str_replace('省','',substr($info->WorkAdd,$position));
                }else{
                    if(strpos($info->WorkAdd,'区')){
                        $area_position = strrpos($info->WorkAdd,'区');
                        $province = substr($info->WorkAdd,0,$area_position).'区';
                        $city = str_replace('区','',substr($info->WorkAdd,$area_position));
                    }else{
                        $province = $info->WorkAdd;
                        $city = $info->WorkAdd;
                    }
                }
            }
            array_push($row, isset($province) ? $csvDocumentService->text_format($province) : '');   //省份
            array_push($row, isset($city) ? $csvDocumentService->text_format($city) : '');  //城市
            array_push($row, $csvDocumentService->text_format($info->FamilyAdd));   //现住址
            if($info->work_address){
                $work_addr = CodeLibrary::get_city_name_by_code($info->work_address);
                $work_address = $work_addr . Kits::addressPact($info->UnitCountryside,$info->UnitStreet,$info->UnitRoom,$info->UnitNo);
            }
            array_push($row, isset($work_address) ? $csvDocumentService->text_format($work_address) : '');   //工作地址
            array_push($row, $csvDocumentService->text_format($info->CreditLimit)); //最高贷款额
            array_push($row, $csvDocumentService->text_format($info->TopMonthPayment)); //最高还款额
            array_push($row, $csvDocumentService->text_format($info->mobile));  //电话
            array_push($row, '');   //POS代码
            array_push($row, '');   //POS门店
            array_push($row, $csvDocumentService->text_format($info->customerPhase));   //活动客户所属阶段
            if($info->SubProductType == 3){
                array_push($row, $csvDocumentService->text_format("车主现金贷"));    //产品类型
            }else{
                array_push($row, $csvDocumentService->text_format("交叉现金贷"));    //产品类型
            }
            array_push($row, $csvDocumentService->text_format($info->ProductFeatures)); //产品特征
            array_push($row, $csvDocumentService->text_format($info->EventName));   //活动名称
            array_push($row, $csvDocumentService->text_format($info->EventDate));   //活动有效期
            array_push($row, $csvDocumentService->text_format(LoanStatusModel::get_descript($info->status, 'loan')));   //安硕状态
            array_push($row, $csvDocumentService->text_format($info->reason));  //取消原因
            array_push($row, $csvDocumentService->text_format($info->cancel_type)); //取消类型
            array_push($row, $csvDocumentService->text_format($info->source==1?"借钱么微信":"佰仟分期购"));   //来源
            $adminLoanModel = new AdminLoanModel();
            $admin_loan_info = $adminLoanModel->get_admin_loan_by_loan_id($info->id);
            if($admin_loan_info){
                $adminMessage = new AdminMessageModel();
                $adminMsg = $adminMessage->get_admin_message_by_admin_id($admin_loan_info->admin_id);
                $input_username = $adminMsg ? $adminMsg->work_no : '';
            }else{
                $input_username = '';
            }
            array_push($row, $csvDocumentService->text_format($input_username));    //录单员姓名
            fputcsv($fp, $row);
            unset($row);
        }
        ob_flush();
        flush();
        fclose($fp);
        exit;
    }

     /*
     * 查询每天期数统计
     */
    public function get_count_period_info($where){
        $loanModel = new LoanModel();
        $info = $loanModel->get_count_period_info($where);
        $start_time = $where['start_time'];
        $end_time = $where['end_time'];
        $data = array();
        $num = 0;
        $sum = array('date' => '总计','9'=>0,'12'=>0,'18'=>0,'24'=>0,'30'=>0,'36'=>0,'sum'=>0);
        for($i = strtotime($start_time); $i <= strtotime($end_time); $i += 86400) 
        {
            $tmpl_time=date("Y-m-d",$i);
            $tmpl_data = array('date' => $tmpl_time,'9'=>'0%','12'=>'0%','18'=>'0%','24'=>'0%','30'=>'0%','36'=>'0%','sum'=>0);
            if(count($info)>0){
                for($j=0;$j<count($info);$j++) {
                    if('20'.$info[$j]->date==$tmpl_time){
                        $tmpl_data['sum'] += $info[$j]->sum;
                        $tmpl_data[$info[$j]->period] = $info[$j]->sum;
                        $sum[$info[$j]->period] += $info[$j]->sum;
                        $sum['sum'] += $info[$j]->sum;
                    }
                }
            }
            $data[$num] = $tmpl_data;
            $num++;
        }

        for ($i = 0;$i<count($data);$i++) {
            if ($data[$i]['sum']!=0) {
                $data[$i]['9'] = 100*round($data[$i]['9']/$data[$i]['sum'],4).'%';
                $data[$i]['12'] = 100*round($data[$i]['12']/$data[$i]['sum'],4).'%';
                $data[$i]['18'] = 100*round($data[$i]['18']/$data[$i]['sum'],4).'%';
                $data[$i]['24'] = 100*round($data[$i]['24']/$data[$i]['sum'],4).'%';
                $data[$i]['30'] = 100*round($data[$i]['30']/$data[$i]['sum'],4).'%';
                $data[$i]['36'] = 100*round($data[$i]['36']/$data[$i]['sum'],4).'%';
            }
        }

        if($sum['sum']!=0){
            $data[$num++] = array('date' => '总计','9'=>100*round($sum['9']/$sum['sum'],4).'%','12'=>100*round($sum['12']/$sum['sum'],4).'%','18'=>100*round($sum['18']/$sum['sum'],4).'%','24'=>100*round($sum['24']/$sum['sum'],4).'%','30'=>100*round($sum['30']/$sum['sum'],4).'%','36'=>100*round($sum['36']/$sum['sum'],4).'%','sum'=>$sum['sum']);
        }else{
            $data[$num++] = $sum;
        }
        return $data;
    }


    /*
     * 导出期数统计
     */
    public function get_perioddown($where){
        $csvDocumentService = new CsvDocumentService();
        $header = array('提单日期', '9期' ,'12期', '18期', '24期', '30期', '36期', '总单量');
        $filename = "期数统计".$where['start_time'].'-'.$where['end_time'];
        $fp = $csvDocumentService->down($header,$filename);
        $loanModel = new LoanModel();
        $res = $this->get_count_period_info($where);
        if(!$res)
        {
            return false;
        }
        //计数器
        $cnt = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 1000;
        foreach ($res as $key => $info) {
            $cnt++;
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }
            $row = array();
            array_push($row, $csvDocumentService->text_format($info['date']));
            array_push($row, $csvDocumentService->text_format($info['9']));
            array_push($row, $csvDocumentService->text_format($info['12']));
            array_push($row, $csvDocumentService->text_format($info['18']));
            array_push($row, $csvDocumentService->text_format($info['24']));
            array_push($row, $csvDocumentService->text_format($info['30']));
            array_push($row, $csvDocumentService->text_format($info['36']));
            array_push($row, $csvDocumentService->text_format($info['sum']));
            fputcsv($fp, $row);
            unset($row);
        }
        ob_flush();
        flush();
        fclose($fp);
        exit;
    }


    /*
     * 查询每天期数统计
     */
    public function get_count_amount_info($where){
        $loanModel = new LoanModel();
        $info = $loanModel->get_count_amount_info($where);
        $start_time = $where['start_time'];
        $end_time = $where['end_time'];
        $data = array();
        $num = 0;

        $sum = array('date' => '总计','3000'=>0,'4000'=>0,'5000'=>0,'6000'=>0,'7000'=>0,'8000'=>0,'9000'=>0,'10000'=>0,'11000'=>0,'12000'=>0,'13000'=>0,'14000'=>0,'15000'=>0,'sum'=>0);

        for($i = strtotime($start_time); $i <= strtotime($end_time); $i += 86400) 
        {
            $tmpl_time=date("Y-m-d",$i);
            $tmpl_data = array('date' => $tmpl_time,'3000'=>'0%','4000'=>'0%','5000'=>'0%','6000'=>'0%','7000'=>'0%','8000'=>'0%','9000'=>'0%','10000'=>'0%','11000'=>'0%','12000'=>'0%','13000'=>'0%','14000'=>'0%','15000'=>'0%','sum'=>0);
            if(count($info)>0){
                for($j=0;$j<count($info);$j++) {
                    if('20'.$info[$j]->date==$tmpl_time){
                        $tmpl_data['sum'] += $info[$j]->sum;
                        $tmpl_data[floor($info[$j]->amount)] = $info[$j]->sum;
                        $sum['sum'] += $info[$j]->sum;
                        $sum[floor($info[$j]->amount)] += $info[$j]->sum;
                    }
                }
            }
            $data[$num] = $tmpl_data;
            $num++;
        }

        for ($i = 0;$i<count($data);$i++) {
            if ($data[$i]['sum']!=0) {
                $data[$i]['3000'] = 100*round($data[$i]['3000']/$data[$i]['sum'],4).'%';
                $data[$i]['4000'] = 100*round($data[$i]['4000']/$data[$i]['sum'],4).'%';
                $data[$i]['5000'] = 100*round($data[$i]['5000']/$data[$i]['sum'],4).'%';
                $data[$i]['6000'] = 100*round($data[$i]['6000']/$data[$i]['sum'],4).'%';
                $data[$i]['7000'] = 100*round($data[$i]['7000']/$data[$i]['sum'],4).'%';
                $data[$i]['8000'] = 100*round($data[$i]['8000']/$data[$i]['sum'],4).'%';
                $data[$i]['9000'] = 100*round($data[$i]['9000']/$data[$i]['sum'],4).'%';
                $data[$i]['10000'] = 100*round($data[$i]['10000']/$data[$i]['sum'],4).'%';
                $data[$i]['11000'] = 100*round($data[$i]['11000']/$data[$i]['sum'],4).'%';
                $data[$i]['12000'] = 100*round($data[$i]['12000']/$data[$i]['sum'],4).'%';
                $data[$i]['13000'] = 100*round($data[$i]['13000']/$data[$i]['sum'],4).'%';
                $data[$i]['14000'] = 100*round($data[$i]['14000']/$data[$i]['sum'],4).'%';
                $data[$i]['15000'] = 100*round($data[$i]['15000']/$data[$i]['sum'],4).'%';
            }
        }
        if($sum['sum']!=0){
            $data[$num++] = array('date' => '总计','3000'=>100*round($sum['3000']/$sum['sum'],4).'%','4000'=>100*round($sum['4000']/$sum['sum'],4).'%','5000'=>100*round($sum['5000']/$sum['sum'],4).'%','6000'=>100*round($sum['6000']/$sum['sum'],4).'%','7000'=>100*round($sum['7000']/$sum['sum'],4).'%','8000'=>100*round($sum['8000']/$sum['sum'],4).'%','9000'=>100*round($sum['9000']/$sum['sum'],4).'%','10000'=>100*round($sum['10000']/$sum['sum'],4).'%','11000'=>100*round($sum['11000']/$sum['sum'],4).'%','12000'=>100*round($sum['12000']/$sum['sum'],4).'%','13000'=>100*round($sum['13000']/$sum['sum'],4).'%','14000'=>100*round($sum['14000']/$sum['sum'],4).'%','15000'=>100*round($sum['15000']/$sum['sum'],4).'%','sum'=>$sum['sum']);
        }else{
            $data[$num++] = $sum;
        }
        return $data;
    }

    /*
     * 导出金额统计
     */
    public function get_amountdown($where){
        $csvDocumentService = new CsvDocumentService();
        $header = array('提单日期', '3000' ,'4000', '5000', '6000', '7000', '8000','9000','10000', '11000','12000','13000','14000','15000','总单量');
        $filename = "金额统计".$where['start_time'].'-'.$where['end_time'];
        $fp = $csvDocumentService->down($header,$filename);
        $loanModel = new LoanModel();
        $res = $this->get_count_amount_info($where);
        if(!$res)
        {
            return false;
        }
        //计数器
        $cnt = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 1000;
        foreach ($res as $key => $info) {
            $cnt++;
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }
            $row = array();
            array_push($row, $csvDocumentService->text_format($info['date']));
            array_push($row, $csvDocumentService->text_format($info['3000']));
            array_push($row, $csvDocumentService->text_format($info['4000']));
            array_push($row, $csvDocumentService->text_format($info['5000']));
            array_push($row, $csvDocumentService->text_format($info['6000']));
            array_push($row, $csvDocumentService->text_format($info['7000']));
            array_push($row, $csvDocumentService->text_format($info['8000']));
            array_push($row, $csvDocumentService->text_format($info['9000']));
            array_push($row, $csvDocumentService->text_format($info['10000']));
            array_push($row, $csvDocumentService->text_format($info['11000']));
            array_push($row, $csvDocumentService->text_format($info['12000']));
            array_push($row, $csvDocumentService->text_format($info['13000']));
            array_push($row, $csvDocumentService->text_format($info['14000']));
            array_push($row, $csvDocumentService->text_format($info['15000']));
            array_push($row, $csvDocumentService->text_format($info['sum']));
            fputcsv($fp, $row);
            unset($row);
        }
        ob_flush();
        flush();
        fclose($fp);
        exit;
    }


    /*
     * 查询每天销量统计
     */
    public function get_count_sales_info($where){
        $loanModel = new LoanModel();
        $data = $loanModel->get_count_sales_info($where);

        return $data;
    }

    /*
     * 导出销量统计
     */
    public function get_salesdown($where){
        $csvDocumentService = new CsvDocumentService();
        $header = array('提单日期', '成交额' ,'成交单', '借钱么', '分期购', '通过率', '足额率', '满足率', '平均贷款金额');
        $filename = "销量统计".date("Y-m-d", time());
        $fp = $csvDocumentService->down($header,$filename);
        $res = $this->get_count_sales_info($where);
        if(!$res)
        {
            return false;
        }
        foreach ($res as $key => $val) {
            $row = array();
            array_push($row, $csvDocumentService->text_format($key));
            array_push($row, $csvDocumentService->text_format(round($val["sum"])));
            array_push($row, $csvDocumentService->text_format($val["count"]));
            array_push($row, $csvDocumentService->text_format(isset($val["1"])?$val["1"]:0));
            array_push($row, $csvDocumentService->text_format(isset($val["3"])?$val["3"]:0));
            array_push($row, $csvDocumentService->text_format(round($val["count"]/($val["count"]+$val["no_pass"]),3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val["loan_avg"]/$val["max_loan"],3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round(($val["count"]-$val["no_fill"])/$val["count"],3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format($val["loan_avg_zd"]));
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit;
    }

    /*
     * 查询每天订单统计
     */
    public function get_loan_stauts_count(){
        $loanModel = new LoanModel();
        $start_time = Request::has('start_time')? Request::input('start_time').' 00:00:00':date('Y-m-01',time()).' 00:00:00';
        $end_time = Request::has('end_time')? Request::input('end_time').' 23:59:59':date('Y-m-d',time()).' 23:59:59';

        $info = $loanModel->get_loan_stauts_by($start_time, $end_time);
        return $info;
    }

    /*
     * 下载每天订单统计
     */
    public function down_loan_stauts_count(){

        $res = $this->get_loan_stauts_count();

        $csvDocumentService = new CsvDocumentService();
        $header = array('日期', '待提交' ,'审核中', '已取消', '已否决', '已签署', '已结清', '提前还款结清', '已注册', '审批通过', '分期购提单','累计申请');
        $filename = "订单统计".date("Y-m-d");
        $fp = $csvDocumentService->down($header,$filename);

        foreach ($res as $key => $val) {
            $row = array();
            array_push($row, $csvDocumentService->text_format("20".$key));
            array_push($row, $csvDocumentService->text_format($val["011"]));
            array_push($row, $csvDocumentService->text_format($val["070"]));
            array_push($row, $csvDocumentService->text_format($val["100"]));
            array_push($row, $csvDocumentService->text_format($val["010"]));
            array_push($row, $csvDocumentService->text_format($val["020"]));
            array_push($row, $csvDocumentService->text_format($val["110"]));
            array_push($row, $csvDocumentService->text_format($val["160"]));
            array_push($row, $csvDocumentService->text_format($val["050"]));
            array_push($row, $csvDocumentService->text_format($val["080"]));
            array_push($row, $csvDocumentService->text_format($val["fqg"]));
            array_push($row, $csvDocumentService->text_format($val["080"]+$val["070"]+$val["100"]+$val["010"]+$val["020"]+$val["110"]+$val["160"]+$val["050"]));
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit;
    }

    /*
     * 查询每天客户统计
     */
    public function get_auth_stauts_count(){
        $authmodel = new AuthModel();
        $start_time = Request::has('start_time')? Request::input('start_time'):date('Y-m-01',time());
        $end_time = Request::has('end_time')? Request::input('end_time').' 23:59:59':date('Y-m-d',time()).' 23:59:59';

        $info = $authmodel->get_auth_stauts_day($start_time, $end_time);
        return $info;
    }

    /*
     * 下载每天客户统计
     */
    public function down_auth_stauts_count(){

        $res = $this->get_auth_stauts_count();

        $csvDocumentService = new CsvDocumentService();
        $header = array('提单日期', '已认证用户' ,'未认证用户', '累计注册', '填写贷款', '个人资料', '单位资料', '上传图片', '签署协议', 'Ca认证','重新贷款','已提单');
        $filename = "客户统计".date("Y-m-d");
        $fp = $csvDocumentService->down($header,$filename);

        foreach ($res as $key => $val) {
            $row = array();
            array_push($row, $csvDocumentService->text_format($key));
            array_push($row, $csvDocumentService->text_format($val["success"]));
            array_push($row, $csvDocumentService->text_format($val["fail"]));
            array_push($row, $csvDocumentService->text_format($val["success"]+$val["fail"]));
            array_push($row, $csvDocumentService->text_format($val["101"]));
            array_push($row, $csvDocumentService->text_format($val["102"]));
            array_push($row, $csvDocumentService->text_format($val["103"]));
            array_push($row, $csvDocumentService->text_format($val["104"]));
            array_push($row, $csvDocumentService->text_format($val["108"]));
            array_push($row, $csvDocumentService->text_format($val["109"]));
            array_push($row, $csvDocumentService->text_format($val["200"]));
            array_push($row, $csvDocumentService->text_format($val["201"]));
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit;
    }

    /*
     * 查询每天通过订单满足率统计
     */
    public function get_day_pass_full_count(){
        $loanModel = new LoanModel();
        $start_time = Request::has('start_time')? Request::input('start_time'):date('Y-m-01',time());
        $end_time = Request::has('end_time')? Request::input('end_time'):date('Y-m-d',time());
        $end_time = $end_time.' 23:59:59';
        $info = $loanModel->get_day_full_rate($start_time, $end_time);
        $total_data["total_t1"] = 0;
        $total_data["total_t2"] = 0;
        $total_data["total_t3"] = 0;
        $total_data["total_t4"] = 0;
        $total_data["total_t5"] = 0;
        $total_data["total_sum"] = 0;
        foreach($info as $val){
            $val->total = $val->t1 + $val->t2 + $val->t3 + $val->t4 + $val->t5;
            $total_data["total_t1"] += $val->t1;
            $total_data["total_t2"] += $val->t2;
            $total_data["total_t3"] += $val->t3;
            $total_data["total_t4"] += $val->t4;
            $total_data["total_t5"] += $val->t5;
            $total_data["total_sum"] += $val->total;
        }
        $data["info"] = $info;
        $data["total_data"] = $total_data;
        return $data;
    }

    /*
     * 下载每天订单满足率统计
     */
    public function down_full_rate_down(){

        $res = $this->get_day_pass_full_count();

        $csvDocumentService = new CsvDocumentService();
        $header = array('提单日期', '100%' ,'70%-99%', '50%-69%', '30%-49%', '29%以下', '总数');
        $filename = "满足率统计".date("Y-m-d");
        $fp = $csvDocumentService->down($header,$filename);

        foreach ($res["info"] as $key => $val) {
            $row = array();
            array_push($row, $csvDocumentService->text_format($val->updated_at));
            array_push($row, $csvDocumentService->text_format(round($val->t1/$val->total, 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val->t2/$val->total, 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val->t3/$val->total, 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val->t4/$val->total, 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($val->t5/$val->total, 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format($val->total));
            fputcsv($fp, $row);
        }
        if($res["total_data"]){
            $row = array();
            array_push($row, $csvDocumentService->text_format("合计："));
            array_push($row, $csvDocumentService->text_format(round($res["total_data"]["total_t1"]/$res["total_data"]["total_sum"], 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($res["total_data"]["total_t1"]/$res["total_data"]["total_sum"], 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($res["total_data"]["total_t1"]/$res["total_data"]["total_sum"], 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($res["total_data"]["total_t1"]/$res["total_data"]["total_sum"], 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format(round($res["total_data"]["total_t1"]/$res["total_data"]["total_sum"], 3)*100 ."%"));
            array_push($row, $csvDocumentService->text_format($res["total_data"]["total_sum"]));
            fputcsv($fp, $row);
        }

        fclose($fp);
        exit;
    }

    public function get_loan_by_id($loan_id)
    {
        $loanAdminModel = new LoanAdminModel();
        $info = $loanAdminModel->get_loan_by_id($loan_id);
        return $info;
    }

    /*
     * 手动取消未提交订单
     */
    public function cancel_nopass_loan($order_id, $reason){
        $loanadmin = new LoanAdminModel();
        $loan= $loanadmin->sel_id_info($order_id);
        if(!$loan || $loan->status != "011"){
            $arr = array("status"=>false, "msg"=>"该订单不能取消");
        }else {
            $this->start_conn();
            $loanmodel = new LoanModel();
            $affect = $loanmodel->update_loan_by_id(array("reason" => $reason, "status" => 100), $order_id);
            $auth_m = new AuthModel();
            $affect2 = $auth_m->update_auth_info_by_user_id(array("step_status"=>200), $loan->user_id);
            $flag = $this->end_conn(array($affect, $affect2));
            if($flag) {
                $centerservice = new CenterService();
                $centerservice->send_auth_loan($order_id);
                $arr = array("status" => true, "msg" => "订单取消成功");
            }else{
                $arr = array("status" => false, "msg" => "订单取消失败");
            }
        }
        return $arr;
    }

    /*
     * 获取未提交订单并发送微信
     *
    public function no_submit_wechat(){
        $customer_m = new CustomerModel();
        $res = $customer_m->get_auth_join_cashloan("get");
	    
        $content = Request::input("content", false);
        if(!$content){
            return array("status"=>false, "msg"=>"填写内容不能为空");
        }
        $wetemplate_s = new WeTemplateService();
        $template_id = $wetemplate_s->audit_loan_wt();
        foreach($res["info"] as $val){
            $arr["title"] = $content;
            $arr["key1"] = "¥".$val->loan_amount;
            $arr["key2"] = $val->loan_period;
            $arr["key3"] = "1.75%";
            $arr["key4"] = "待提交";
            $arr["remark"] = "点击此处查看详情";
	        if($val->source == 3){
		        // 给分期购客户发送模板消息
		        $parameters = [
			        'phoneNumber' => $val->mobile,
			        'customerName' => $val->real_name,
			        'certId' => $val->id_card,
			        'credit' => $val->loan_amount,
			        'period' => $val->loan_period,
			        'monthInterestRate' => $val->month_interest,
			        'auditStatus' => $val->status,
			        'remark' => $arr['remark']
		        ];
		        $wechatApi = new WechatApi();
		        $interfaceType = 'type_2';
		        Logger::info('合同号为：' . $val->pact_number . ' 手机号为：' . $val->mobile .'调用了分期购发送模板消息接口，接口类型：' . $interfaceType, 'fqg-msg-interface');
		        $interfaceReturn = $wechatApi->send_wechat_message($interfaceType, $parameters);
	        }else{
		        $affect = $wetemplate_s->template($val->openid, $template_id, $arr);
	        }
        }
        return array("status"=>true, "msg"=>"发送成功");
    }
*/
	/*
	 * 获取未提交订单并发送微信
	 */
	public function no_submit_wechat()
	{
		ignore_user_abort();
		set_time_limit(3600);
		$content = Request::input("content", false);
		if (!$content) {
			return array("status" => false, "msg" => "填写内容不能为空");
		}
		$wetemplate_s = new WeTemplateService();
		$template_id = $wetemplate_s->audit_loan_wt();
		$anApi = new AnApi();
		$wechatApi = new WechatApi();
		$customer_m = new CustomerModel();
		$res = $customer_m->get_auth_join_cashloan("get");
		foreach ($res["info"] as $val) {
			// 先调用接口,判断用户是否实名认证通过
			Logger::info('手机' . $val->mobile . ' 身份证号' . $val->id_card . '真实姓名' . $val->real_name . '的用户调用实名认证接口', 'no_submit_wechat');
			$info = $anApi->get_customer_message($val->real_name, $val->id_card);
			Logger::info('实名认证接口返回值为：' . json_encode($info, JSON_UNESCAPED_UNICODE), 'no_submit_wechat');
			if(!empty($info)){
				// 实名认证通过,发送消息
				$arr["title"] = $content;
				$arr["key1"] = "¥" . $val->loan_amount;
				$arr["key2"] = $val->loan_period;
				$arr["key3"] = "1.75%";
				$arr["key4"] = "待提交";
				$arr["remark"] = "点击此处查看详情";
				if ($val->source == 3) {
					// 给分期购客户发送模板消息
					$parameters = [
						'phoneNumber' => $val->mobile,
						'customerName' => $val->real_name,
						'certId' => $val->id_card,
						'credit' => $arr['key1'],
						'period' => $arr['key2'],
						'monthInterestRate' => $arr['key3'],
						'auditStatus' => $arr['key4'],
						'remark' => $arr['remark'],
						'first' => $arr['title'],
						'link' => base64_encode('/users/register1')
					];
					$interfaceType = 'type_2';
					Logger::info('合同号为：' . $val->pact_number . ' 手机号为：' . $val->mobile . '调用了分期购发送模板消息接口，接口类型：' . $interfaceType, 'fqg-msg-interface');
					$interfaceReturn = $wechatApi->send_wechat_message($interfaceType, $parameters);
				} else {
					$affect = $wetemplate_s->template($val->openid, $template_id, $arr);
				}
			}else{
				// 实名认证不通过跳过本次循环
				continue;
			}
		}
		return array("status" => true, "msg" => "发送成功");
	}

	/*
	 * 获取未提交订单并发送短信
	 */
	public function no_submit_sms(){
		ignore_user_abort();
		set_time_limit(3600);
		$customer_m = new CustomerModel();
		$res = $customer_m->get_auth_join_cashloan("get");

		$content = Request::get("content", false);
		if(!$content){
			return array("status"=>false, "msg"=>"内容不能为空");
		}

		$sms_m = new UniqueCodeModel(3);

		$sum = count($res["info"])-1;
		$mobiles = "";
		Logger::info('群发开始！','nosubmitsms');
		$total = 0;
		$smsTotal = 0;
		$anApi = new AnApi();
		foreach($res["info"] as $i=>$val) {
			/*实名*/
			Logger::info($val->mobile . '实名' . $val->id_card . $val->real_name,'nosubmitsms');
			$info = $anApi->get_customer_message($val->real_name,$val->id_card);
			$total++;
			if($info){
				//实名通过
				Logger::info($val->mobile . '实名' . '通过','nosubmitsms');
				$smsTotal++;
			}else{
				//实名不通过
				Logger::info($val->mobile . '实名' . '不通过','nosubmitsms');
				continue;
			}

			if(!$mobiles){
				$mobiles = $val->mobile;
			}else{
				$mobiles = $mobiles.",".$val->mobile;
			}
			if($i!=0 && $i%10 == 0 || $sum == $i){
				$sms_m->select_send_supply($content, $mobiles);
				$mobiles = "";
			}
		}
		Logger::info('群发结束,有' . $total . '单' . '发送短信' . $smsTotal. '单','nosubmitsms');
		return array("status"=>true, "msg"=>"发送成功");
	}

    /*
     * 获取未申请并发送微信
     */
    public function no_apply_wechat(){
        $select_type = Request::input('select_type', 1);
        /*
        $no_apply_name = "no_apply_time".$select_type;
        if(Cache::has($no_apply_name)){
            $no_submit_time = strtotime(Cache::get($no_apply_name));
            if(time()<$no_submit_time){
                return array("status"=>false, "msg"=>"7天只能发一次，未到期限");
            }
        }

        if(!Request::has("EventName")) {
            Cache::forever($no_apply_name, date("Y-m-d", strtotime("+7 day", time())));
        }else{
            $no_apply_item = $no_apply_name.Request::input("EventName");
            if(Cache::has($no_apply_item)){
                $no_submit_time = strtotime(Cache::get($no_apply_item));
                if(time()<$no_submit_time){
                    return array("status"=>false, "msg"=>"7天只能发一次，未到期限");
                }
            }
            Cache::forever($no_apply_item, date("Y-m-d", strtotime("+7 day", time())));
        }*/

        $content = Request::input("content", false);
        if(!$content){
            return array("status"=>false, "msg"=>"填写内容不能为空");
        }
        $wetemplate_s = new WeTemplateService();
        $template_id = $wetemplate_s->audit_loan_wt();
        if($select_type==1){
            $status_name = "待实名";
        }elseif($select_type==3){
            $status_name = "待申请";
        }else{
            $status_name = "待提交";
        }
	    $customer_m = new CustomerModel();
	    $res = $customer_m->get_user_join_cashloan("get");
	    $anApi = new AnApi();
	    $wechatApi = new WechatApi();
        foreach($res["info"] as $val){
	        // 先调用接口,判断用户是否实名认证通过
	        Logger::info('手机' . $val->mobile . ' 身份证号' . $val->id_card . '真实姓名' . $val->real_name . '的用户调用实名认证接口', 'no_apply_wechat');
	        $info = $anApi->get_customer_message($val->real_name, $val->id_card);
	        Logger::info('实名认证接口返回值为：' . json_encode($info, JSON_UNESCAPED_UNICODE), 'no_apply_wechat');
	        if(!empty($info)){
		        // 实名认证通过,发送消息
		        $arr["title"] = $content;
		        $arr["key1"] = "¥".$val->CreditLimit;
		        $arr["key2"] = "36";
		        $arr["key3"] = "1.75%";
		        $arr["key4"] = $status_name;
		        $arr["remark"] = "点击此处查看详情";
		        if ($val->source == 3) {
			        // 给分期购客户发送模板消息
			        $parameters = [
				        'phoneNumber' => $val->mobile,
				        'customerName' => $val->real_name,
				        'certId' => $val->id_card,
				        'credit' => $arr['key1'],
				        'period' => $arr['key2'],
				        'monthInterestRate' => $arr['key3'],
				        'auditStatus' => $arr['key4'],
				        'remark' => $arr['remark'],
				        'first' => $arr['title'],
				        'link' => base64_encode('/users/register1')
			        ];
			        $interfaceType = 'type_2';
			        Logger::info('合同号为：暂无 手机号为：' . $val->mobile . '调用了分期购发送模板消息接口，接口类型：' . $interfaceType, 'fqg-msg-interface');
			        $interfaceReturn = $wechatApi->send_wechat_message($interfaceType, $parameters);
		        }else{
			        $affect = $wetemplate_s->template($val->openid, $template_id, $arr);
		        }
	        }else{
		        // 实名认证不通过跳过本次循环
		        continue;
	        }
        }
        return array("status"=>true, "msg"=>"发送成功");
    }

	/*
	 * 获取未申请并发送短信
	 * 2016-06-14新增：
	 * 1).发短信前调用安硕接口实名认证，认证通过才会发送。
	 * 2).增加日志文件，记录安硕实名认证结果、短信内容以及发送结果。
	 */
	public function no_apply_sms(){
		ignore_user_abort();
		set_time_limit(3600);
		$content = Request::get("content", false);
		if(!$content){
			return array("status"=>false, "msg"=>"内容不能为空");
		}
		$customer_m = new CustomerModel();
		$res = $customer_m->get_user_join_cashloan("get");
		$sum = count($res["info"])-1;
		$sms_m = new UniqueCodeModel(3);
		$mobiles = "";
		Logger::info('群发开始！','noapplysms');
		$total = 0;
		$smsTotal = 0;
		$anApi = new AnApi();
		foreach($res["info"] as $i=>$val) {
			// 调用安硕接口实名认证
			Logger::info($val->mobile . '实名' . $val->id_card . $val->real_name,'noapplysms');
			$info = $anApi->get_customer_message($val->real_name,$val->id_card);
			$total++;
			if($info){
				// 实名通过，发送短信
				Logger::info($val->mobile . '实名' . '通过','noapplysms');
				$smsTotal++;
				if(!$mobiles){
					$mobiles = $val->mobile;
				}else{
					$mobiles = $mobiles.",".$val->mobile;
				}
				if($i!=0 && $i%10 == 0 || $sum == $i){
					$sms_m->select_send_supply($content, $mobiles);
					$mobiles = "";
				}
			}else{
				// 实名不通过，跳过本次循环
				Logger::info($val->mobile . '实名' . '不通过','noapplysms');
				continue;
			}
		}
		Logger::info('群发结束,有' . $total . '单' . '发送短信' . $smsTotal. '单','noapplysms');
		return array("status"=>true, "msg"=>"发送成功");
	}
/*
    public function no_apply_sms(){
        $customer_m = new CustomerModel();
        $res = $customer_m->get_user_join_cashloan("get");

        $content = Request::get("content", false);
        if(!$content){
            return array("status"=>false, "msg"=>"内容不能为空");
        }
        $sms_m = new UniqueCodeModel(3);
        $sum = count($res["info"])-1;
        $mobiles = "";
        foreach($res["info"] as $i=>$val) {
            if(!$mobiles){
                $mobiles = $val->mobile;
            }else{
                $mobiles = $mobiles.",".$val->mobile;
            }
            if($i!=0 && $i%10 == 0 || $sum == $i){
                $sms_m->select_send_supply($content, $mobiles);
                $mobiles = "";
            }
        }

        return array("status"=>true, "msg"=>"发送成功");
    }
*/

    /**
     * 订单图片列表
     * @param $condition
     * @return mixed
     */
    public function get_loan_picture_list($condition, $type=''){
        $loanAdmin = new LoanAdminModel();
        $data = $loanAdmin->get_loan_picture_list($condition, $type);
        return $data;
    }
}