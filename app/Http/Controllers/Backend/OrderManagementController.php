<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BaseController;
use EasyWeChat\Payment\Order;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Model\Backend\Orders;
use Redirect;
use App\Services\Help;
use DB;
use App\Util\DownloadExcel;
use Symfony\Component\HttpFoundation\Response;
use App\Model\Base\SyncCodeLibrary;

/**
 * Description of MainController
 *
 * @author lenovo
 */
class OrderManagementController extends BaseController {
    /**
     * 已提交订单
     */
    public function getSubmitIndex(Request $request) {

        $user_id = 225500;  //用户id
        $role_id = 4; //角色类型
        $input = $request->all();
        $applicant_name = $request->input('applicant_name');
        $contract_no = $request->input('contract_no');
        $applicant_id_card = $request->input('applicant_id_card');
        $city = $request->input('city');
        $order_create_time = $request->input('order_create_time');
        $contract_status = $request->input('contract_status');

        /**
         * 销售相关人员权限设定
         */
        $authority_condition = Orders::getAuthorityCondition($role_id, $user_id);
        $preCondition = [];

        //查询条件构建
        $condition = [
            'orders_product.applicant_name' => [$applicant_name, '='],
            'contract_info.contract_no' => [$contract_no, '='],
            'orders_product.applicant_id_card' => [$applicant_id_card, '='],
            'sync_store_info.CITY' => [$city, '='],
            'orders.order_create_time' => [strtotime($order_create_time), '>'],
            'contract_info.status' => [$contract_status, '=']
        ];

        //条件前端显示构建
        $preCondition = [
            'applicant_name' => $applicant_name,
            'contract_no' => $contract_no,
            'applicant_id_card' => $applicant_id_card,
            'city' => $city,
            'order_create_time' => $order_create_time,
            'contract_status' => $contract_status
        ];


        $orderList = Orders::getOrders($authority_condition, Orders::getSubmitOrdersStatus(), $condition);
        $pages = $orderList->appends($preCondition)->render();
        $contractStatusText = SyncCodeLibrary::getContractStatus();

        return view('backend.order.submit_index')->with([
            'orderList' => $orderList,
            'pages' => $pages,
            'contractStatusText' => $contractStatusText,
            'precondition' => $preCondition,
        ]);
    }


    /**
     * 未提交订单
     */
    public function getNotSubmitIndex(Request $request) {

        $user_id = 225500;  //用户id
        $role_id = 4; //角色类型
        $input = $request->all();
        $applicant_name = $request->input('applicant_name');
        $contract_no = $request->input('contract_no');
        $applicant_id_card = $request->input('applicant_id_card');
        $city = $request->input('city');
        $order_create_time = $request->input('order_create_time');
        $contract_status = $request->input('contract_status');

        /**
         * 销售相关人员权限设定
         */
        $authority_condition = Orders::getAuthorityCondition($role_id, $user_id);
        $preCondition = [];


        //查询条件构建
        $condition = [
            'orders_product.applicant_name' => [$applicant_name, '='],
            'contract_info.contract_no' => [$contract_no, '='],
            'orders_product.applicant_id_card' => [$applicant_id_card, ''],
            'sync_store_info.COUNTRY' => [$city, ''],
            'orders.order_create_time' => [strtotime($order_create_time), '>'],
            'contract_info.status' => [$contract_status, '=']
        ];

        //条件前端显示构建
        $preCondition = [
            'applicant_name' => $applicant_name,
            'contract_no' => $contract_no,
            'applicant_id_card' => $applicant_id_card,
            'city' => $city,
            'order_create_time' => $order_create_time,
            'contract_status' => $contract_status
        ];


        $orderList = Orders::getOrders($authority_condition, Orders::getNotSubmitOrdersStatus(), $condition);
        $pages = $orderList->appends($preCondition)->render();
        $contractStatusText = SyncCodeLibrary::getContractStatus();

        return view('backend.order.not_submit_index')->with([
            'orderList' => $orderList,
            'pages' => $pages,
            'contractStatusText' => $contractStatusText,
            'precondition' => $preCondition,
        ]);
    }



    /**
     * 查看详情页
     */
    public function getOrderDetailInfo (Request $request) {
        $order_id = $request->input('order_id');

        $info = Orders::getOrderDetailInfo($order_id);
        $city_name_list = SyncCodeLibrary::mapCodeToCity();
        $info['work_bank_city'] = $city_name_list[$info['work_bank_city']];
        $info['CITY'] = $city_name_list[$info['CITY']];

        if( empty($info) ) {
            return Redirect::to('/backend/order/submit-index');
        }

        return view('backend.order.order_detail')->with([
            'info' => $info
        ]);
    }

    /**
     * 查看影像
     */
    public function getOrderImage (Request $request) {
        $order_id = $request->input('order_id');

        $info = Orders::getOrderImage($order_id);


        return view('backend.order.order_image')->with([
            'info' => $info
        ]);
    }

    /**
     * 查看协议
     */
    public function getOrderProtocol(Request $request) {
        $order_id = $request->input('order_id');

        try{
            $file_path = DB::table('orders')->where('id', $order_id)->value('protocol_url');
            $file = file_get_contents(\App\Util\FileReader::read_storage_image_resize_file($file_path));
            $file = base64_decode($file);
        }catch(\Exception $e) {
            $file = "<p>查不到该订单协议</p>";
        }

        return view('backend.order.order_protocol')->with([
            'file' => $file
        ]);
    }

    /**
     * 导出数据
     */
    public function getOrderExportIndex() {
        $precondition = [];
        $contract_status_text = SyncCodeLibrary::getContractStatus();
        $city_manager_list = Orders::getCityManager();
        return view('backend.order.order_export_index')->with([
            'precondition' => $precondition,
            'contractStatusText' => $contract_status_text,
            'cityManagerList' => $city_manager_list
        ]);
    }


    /**
     * 导出excel
     */
    public function postDownloadExcel(Request $request){
        $user_id = 225500;  //用户id
        $role_id = 4; //角色类型
        $sales_manager = $request->input('salesManager');
        $sales = $request->input('sales');
        $city_manager = $request->input('city_manager');
        $orders_status = $request->input('ordersStatus') ? explode(',', $request->input('ordersStatus')) : '';
        $contract_status = $request->input('contractStatus') ? explode(',', $request->input('contractStatus')) : '';
        $s_date = $request->input('s_date') ? strtotime($request->input('s_date')) : '';
        $e_date = $request->input('e_date') ? strtotime($request->input('e_date')) : '';
        /**
         * 销售相关人员权限设定
         */
        $authority_condition = Orders::getAuthorityCondition($role_id, $user_id);
        $preCondition = [];
        $condition = [];

        $condition = [
            ['key' => 'sync_store_info.SALESMANAGER', 'values' => [$sales_manager, '=']],
            ['key' => 'sync_storerelativesalesman.SALESMANNO', 'values' => [$sales, '=']],
            ['key' => 'sync_store_info.CITYMANAGER', 'values' => [$city_manager, '=']],
            ['key' => 'orders.order_status', 'values' => [$orders_status, 'in']],
            ['key' => 'contract_info.status', 'values' => [$contract_status, 'in']],
            ['key' => 'orders.order_create_time', 'values' => [$s_date, '>']],
            ['key' => 'orders.order_create_time', 'values' => [$e_date, '<']]
        ];

        $data = Orders::getPushOrders($authority_condition, [0,1,2,3,4,5,6,7], $condition);
        $data = json_decode(json_encode($data, true));
        if( !empty($data) ) {

            $info['title'] = array(
                '时间',
                '门店名称',
                '姓名',
                '身份证号码',
                '手机号码',
                '推荐人',
                '服务类型',
                '产品类型',
                '贷款金额',
                '期数',
                '每月还款日',
                '每月还款额',
                '行业类别',
                '工作单位',
                '单位电话',
                '单位地址',
                '区/县',
                '街道/乡镇',
                '详细地址',
                '门牌号',
                '银行卡号',
                '开户银行',
                '开户银行城市',
                '学历',
                '邮箱',
                '亲属关系',
                '亲属姓名',
                '亲属手机号',
                '家庭住址',
                '区/县',
                '街道/乡镇',
                '详细地址',
                '门牌号',
                '合同号',
                '合同状态',
                '取消原因',
                '销售代表',
                '销售经理',
                '区域总监',
            );

            foreach ($data as $k => $v) {
                $info['data'][] = [
                    date("Y-m-d H:i:s", $v->order_create_time),
                    $v->SNAME,
                    $v->applicant_name,
                    $v->applicant_id_card . ' ',
                    $v->mobile,
                    $v->reference,
                    $v->service_type,
                    $v->product_type,
                    $v->loan_money,
                    $v->periods,
                    $v->monthly_repay_date,
                    $v->monthly_repay_money,
                    $v->industry_name,
                    $v->work_unit,
                    $v->work_unit_mobile,
                    $v->work_addr1,
                    $v->work_addr2,
                    $v->work_addr3,
                    $v->work_addr4,
                    $v->work_addr5,
                    $v->work_repayment_account,
                    $v->work_deposit_bank,
                    $v->work_bank_city,
                    $v->edu_level,
                    $v->qq_email,
                    $v->family_relation,
                    $v->family_name,
                    $v->family_mobile,
                    $v->family_addr1,
                    $v->family_addr2,
                    $v->family_addr3,
                    $v->family_addr4,
                    $v->family_addr5,
                    $v->contract_no,
                    $v->status,
                    $v->order_remark,
                    $v->sales,
                    $v->sale_manage,
                    $v->city_manage,
                ];
            }

            $info['name'] = time();
            $rs = DownloadExcel::pushExcel($info);
            echo $rs;
        } else {
            echo json_encode(['success' => false, 'url' => '']);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * 下载excel文件
     */
    public function getDownloadFile(Request $request) {
        $path = $request->input('path');
        $path = storage_path().$path;
        return response()->download($path);
    }



}
