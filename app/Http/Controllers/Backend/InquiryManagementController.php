<?php
namespace App\Http\Controllers\Backend;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;
use DB;
use App\Model\Backend\Store;
use App\Service\Help;
use App\Model\Backend\UserInfo;
use App\Model\Backend\CodeLibrary;
use Validator;
use App\Model\Base\SyncCodeLibrary;
use App\Model\Backend\Merchant;

/**
 * 查询管理
 * @author yue.huang01
 *
 */
class InquiryManagementController extends BaseController
{

    public function getTest(Request $request)
    {
        $list = SyncCodeLibrary::mapCodeToCity();
        echo "<pre>";
        print_r($list);
    }

    /**
     * 商户查询
     *
     * @param Request $request            
     */
    public function getMerchant(Request $request)
    {
        // 过滤条件
        $cons = $request->input('search');
        $request->input('storeCity') ? $cons['storeCity'] = $request->input('storeCity') : '';
        $list = Store::getStoreInfo($cons);

        $salesmanager_id_list = [];
        $regionalmanager_id_list = [];
        //获取销售经理及区域总监列表
        foreach ($list as $k => $v) {
            $salesmanager_id_list[] = $v->SALESMANAGER;
            $regionalmanager_id_list[] = $v->CITYMANAGER;
        }
        //合并数组
        $user_id_list = array_merge($salesmanager_id_list, $regionalmanager_id_list);
        $user_id_list = array_unique($user_id_list);
        $user_id_list = array_filter($user_id_list);
        //获取用户姓名列表
        $user_name_list = UserInfo::getUserNameListByID($user_id_list)->toArray();
        $user_name_list = Help::fixArray($user_name_list, 'USERID');
        
        // 获取门店状态列表
        $status_list = CodeLibrary::getRetailStoreStatus();
        $pages = $list->appends($request->all())
            ->render();
        return view('backend.inquiry.merchant')->with([
            'list' => $list,
            'cons' => $cons,
            'pages' => $pages,
            'user_name_list' => $user_name_list,
            'status_list' => $status_list
        ]);
    }

    /**
     * 商户详情
     * 
     * @param Request $requiest            
     * @return string|\Illuminate\View\$this
     */
    public function getMerchantDetail(Request $requiest)
    {
        $serial_no = $requiest->rid;
        if ($serial_no == '') {
            return '商户号不可为空';
        }
        $item = Merchant::getMerchantDefail($serial_no);
        $map_city = SyncCodeLibrary::mapCodeToCity(); // 省市编码
        $retail_type = Help::fixObject(SyncCodeLibrary::retailType(), 'SORTNO'); // 商户类型
        return view('backend.inquiry.merchant_detail')->with([
            'item' => $item,
            'map_city' => $map_city,
            'retail_type' => $retail_type
        ]);
    }

    /**
     * 获取绑定产品json
     * @param Request $request
     * @return multitype:NULL string
     */
    public function getProduct(Request $request)
    {
        $serial_no = $request->sid;
        $info = Store::getStoreBaseInfo($serial_no);
        if ($info) {
            $product = Store::getProduct($info->SNO);
            $json = [
                'no' => $info->SNO,
                'name' => $info->SNAME,
                'product' => isset($product[0]->PNAME) ? $product[0]->PNAME : ''
            ];
        }
        return $json;
    }
}
