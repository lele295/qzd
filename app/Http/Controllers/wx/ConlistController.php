<?php

namespace App\Http\Controllers\wx;

use App\Model\mobile\ContractModel;
use App\Model\mobile\OrderModel;
use App\Model\mobile\WechatModel;
use App\Util\DownloadExcel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use \Illuminate\Support\Facades\Request as staticRequest;

class ConlistController extends Controller
{

    /**
     * 合同数据查看(暂时一个简单的页面)
     * @param Request $request
     * @return
     */
    public function getPage(Request $request)
    {
        //查询总条数
        $model = new OrderModel();
        $count = $model->get_count_nums();

        $t = time();
        $startTime = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
        $endTime = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
        $tValidCons = $model->get_today_valid_contract($startTime,$endTime);//今天有效订单数
        $tSumMoney = $model->get_today_valid_money($startTime,$endTime);//今天有效订单金额
        if(is_null($tSumMoney)){
            $tSumMoney = 0;
        }
        $tRejectCons = $model->get_today_reject_contract($startTime,$endTime);//今天否决订单数
        $tCancelCons = $model->get_today_cancel_contract($startTime,$endTime);//今天取消订单数
        $sumMoney = $model->get_contract_sum_money();//总钱数
        $validCons = $model->get_valid_contract_nums();//有效合同

        //每页显示条数
        $rev = '50';
        //求总页数
        $sums = ceil($count/$rev);
        //当前前页
        $page = $request->query->get('page');
        if(empty($page)){
            $page = "1";
        }
        //上一页、下一页
        $prev = ($page-1)>0 ? $page-1 : 1;
        $next = ($page+1)<$sums ? $page+1 : $sums;
        //求偏移量
        $offset = ($page-1)*$rev;
        //sql分页数据
        $data = $model->get_page_data($offset,$rev);
        //数字分页(可有可无)
        $pp = array();
        for($i=$page;$i<=$page+3;$i++){
            if($i<=$sums){
                $pp[$i]=$i;
            }
        }

        $type = $request->query->get('type');
        if($type){
            return view('wx.contract_info',[
                'data'=>$data,
                'prev'=>$prev,
                'rev'=>$rev,
                'next'=>$next,
                'sums'=>$sums,
                'pp'=>$pp,
                'page'=>$page,
                'sumMoney'=>$sumMoney,
                'validCons'=>$validCons,
                'tValidCons'=>$tValidCons,
                'tSumMoney'=>$tSumMoney,
                'tRejectCons'=>$tRejectCons,
                'tCancelCons'=>$tCancelCons
            ]);
        }else{
            return view('wx.contract_list',[
                'data'=>$data,
                'prev'=>$prev,
                'rev'=>$rev,
                'next'=>$next,
                'sums'=>$sums,
                'pp'=>$pp,
                'page'=>$page,
                'sumMoney'=>$sumMoney,
                'validCons'=>$validCons,
                'tValidCons'=>$tValidCons,
                'tSumMoney'=>$tSumMoney,
                'tRejectCons'=>$tRejectCons,
                'tCancelCons'=>$tCancelCons
            ]);
        }
    }

    /**
     * 随机码查看
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getRandCode(Request $request){
        //查询总条数
        $model = new OrderModel();
        $count = $model->get_rand_count_nums();
        //每页显示条数
        $rev = $count;
        //求总页数
        $sums = ceil($count/$rev);
        //当前前页
        $page = $request->query->get('page');

        if(empty($page)){
            $page = "1";
        }
        //上一页、下一页
        $prev = ($page-1)>0 ? $page-1 : 1;
        $next = ($page+1)<$sums ? $page+1 : $sums;
        //求偏移量
        $offset = ($page-1)*$rev;
        //sql查询数据库
        $data = $model->get_rand_data($offset,$rev);
        //数字分页(可有可无)
        $pp = array();
        for($i=$page;$i<=$page+3;$i++){
            if($i<=$sums){
                $pp[$i]=$i;
            }
        }

        $type = $request->query->get('type');
        if($type){
            return view('wx.rand_info',[
                'data'=>$data,
                'prev'=>$prev,
                'rev'=>$rev,
                'next'=>$next,
                'sums'=>$sums,
                'pp'=>$pp,
                'page'=>$page
            ]);
        }else{
            return view('wx.rand_list',[
                'data'=>$data,
                'prev'=>$prev,
                'rev'=>$rev,
                'next'=>$next,
                'sums'=>$sums,
                'pp'=>$pp,
                'page'=>$page
            ]);
        }
    }

    /**
     * 查看上周的订单汇总信息
     */
    public function getLastweekOrderInfo(){

        $model = new OrderModel();
        $str = '<!doctype html><html lang="en"><link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css"><head><meta charset="UTF-8"><title>仟姿贷</title><style>tr,th{text-align: center;}</style></head><body>';
        $str .= '<table width="100%" border="1" bordercolor="black" cellspacing="0"><tr><td colspan="5" align="center">上周订单信息<span style="float: right;margin-right: 20px"><a href="/wx/conlist/page">返回订单列表</a></span></td></tr>';
        $str .='<tr><th></th><th>有效订单</th><th>否决订单数</th><th>取消订单数</th><th>有效订单金额</th></tr>';

        for($i = 1;$i <= 7;$i++){

            $beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+$i-7,date('Y'));
            $endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+$i-7,date('Y'));
            $tValidCons = $model->get_today_valid_contract($beginLastweek,$endLastweek);//今天有效订单数
            $tSumMoney = $model->get_today_valid_money($beginLastweek,$endLastweek);//今天有效订单金额
            $tRejectCons = $model->get_today_reject_contract($beginLastweek,$endLastweek);//今天否决订单数
            $tCancelCons = $model->get_today_cancel_contract($beginLastweek,$endLastweek);//今天取消订单数
            $str .='<tr><td>'.date("m",$beginLastweek).'-'.date("d",$beginLastweek).'</td><td>'.$tValidCons.'</td><td>'.$tRejectCons.'</td><td>'.$tCancelCons.'</td><td>'.$tSumMoney.'</td></tr>';
        }

        $str .= '</table></body></html>';
        echo $str;
    }

    public function getForm(){
        return view('wx.catpl');
    }

    /**
     * 重新推送模板
     * @return string
     */
    public function postReCaTpl(){

        $model = new ContractModel();
        $data = \Illuminate\Support\Facades\Request::input('constract_no');

        if($data){
            $constract_nos = explode(',',$data);
            foreach($constract_nos as $v){
                $info = $model->get_contract_info_by_id($v);
                if($info){
                    //更改状态，从新推送ca签署协议
                    if($info->status=='080'){
                        $rs = $model->update_contract_info_by_id($v,'070');
                        if($rs)
                            $msgs[] = ['msg'=>'合同号'.$v.'：模板信息已重新发送！'];
                    }else{
                        $msgs[] = ['msg'=>'合同号'.$v.'：不需要重新发送模板！'];
                    }

                }else{
                    $msgs[] = ['msg'=>'找不到合同'.$v];
                }
            }

        }else{
            $msgs[] = ['msg'=>'请填写合同号！'];
        }

        return json_encode($msgs);
    }

    
    public function getDownloadExcel(){
        return view('wx.excel');
    }

    /**
     * excel下载
     */
    public function postDownloadExcel(){

        $startTime = \Illuminate\Support\Facades\Request::input('startTime');
        $endTime = \Illuminate\Support\Facades\Request::input('endTime');
        //获取下载所需要的数据

        $model = new OrderModel();
        $data = $model->get_excel_data(strtotime($startTime),strtotime($endTime));

        foreach($data as $k=>$v){
            $data['data'][] = $this->object2array($v);
        }

        $info['title'] = array(
            '订单日期',
            '开始录单时间',
            '提交订单时间',
            '门店',
            '服务类型',
            '身份证',
            '姓名',
            '合同号',
            '合同状态',
            '手机号',
            '工作单位',
            '最高学历',
            '家属联系电话',
            '家属姓名',
            '贷款金额',
            '产品类型',
            '期数',
            '每月还款日',
            '每月还款额',
            '销售代表',
            '销售经理',
            '城市经理'
        );

        foreach($data['data'] as $k=>$v){
            $info['data'][] = [
                $v['order_time'],
                $v['order_start_time'],
                $v['order_commit_time'],
                $v['SNAME'],
                $v['service_type'],
                $v['applicant_id_card'],
                $v['applicant_name'],
                $v['contract_no'],
                $v['ITEMNAME'],
                $v['mobile'],
                $v['work_unit'],
                $v['edu_level'],
                $v['family_mobile'],
                $v['family_name'],
                $v['loan_money'],
                $v['PNAME'],
                $v['periods'],
                $v['monthly_repay_date'],
                $v['monthly_repay_money'],
                $v['USERNAME'],
                $v['SALESMANAGERNAME'],
                $v['CITYMANAGERNAME']
            ];
        }

        $info['name'] = $startTime.'--'.$endTime.'--'.time();
        $rs = DownloadExcel::downLoadExcel($info);
        return $rs;
    }

    /**
     * 将对象转化为数组
     * @param $object
     * @return array
     */
    function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                //[$key] = $value;
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }

    /*
     * 更新token为空
     */
    public function getUpdateToken(){
        $model = new WechatModel();
        $rs = $model->update_access_token('','');
        return Redirect::to('wx/conlist/page');

    }

}
