<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Hash;
use Redirect;
use DB;
use App\Model\Backend\OrdersProduct;
use App\Service\Help;
use Illuminate\Http\Request;
use App\Model\Base\SyncCodeLibrary;
use App\Model\Backend\ContractInfo;
use App\Model\Backend\Store;
use App\Util\DownloadExcel;

/**
 * 报表统计
 *
 * @author yue.huang01
 *        
 */
class StatisticsController extends Controller
{

    /**
     * 销售业绩统计页面
     */
    public function getMarketPerformance()
    {
        return view('backend.statistics.market_performance');
    }

    /**
     * 风控数据统计页面
     */
    public function getRiskControl()
    {
//         return OrdersProduct::getMarketPerformanceList(1);
        return view('backend.statistics.risk_control');
    }

    /**
     * 业绩统计报表接口
     *
     * @return multitype:unknown string
     */
    public function getMarketPerformanceApi(Request $request)
    {
        $cons = $request->all();
        $res = OrdersProduct::getMarketPerformance($cons);
        if ($res) {
            return Help::json(1, '', $res);
        } else
            return Help::json(0, '获取报表数据失败');
    }

    /**
     * 城市排名报表接口
     *
     * @param Request $request            
     * @return multitype:unknown string
     */
    public function getCityRankApi(Request $request)
    {
        $cons = $request->all();
        $res = Store::getCityRank($cons, 5);
        if ($res) {
            $city_code = SyncCodeLibrary::detailCodeToCity()->toArray();
            foreach ($res as $k => $v) {
                $res[$k]->city = $city_code[$res[$k]->city];
            }
            return Help::json(1, '', $res);
        } else
            return Help::json(0, '获取报表数据失败');
    }

    /**
     * 区域排名报表接口
     *
     * @param Request $request            
     * @return multitype:unknown string
     */
    public function getAreaRankApi(Request $request)
    {
        $cons = $request->all();
        $res = Store::getAreaRank($cons,5);
        if ($res) {
            return Help::json(1, '', $res);
        } else
            return Help::json(0, '获取报表数据失败');
    }

    /**
     * 风控数据统计报表接口
     *
     * @return multitype:unknown string
     */
    public function getRiskControlDataApi(Request $request)
    {
        $cons = $request->all();
        $res = ContractInfo::getRiskControlData($cons);
        if ($res) {
            return Help::json(1, '', $res);
        } else
            return Help::json(0, '获取报表数据失败');
    }

    /**
     * 导出业绩统计报表
     */
    public function getMarketPerformanceExport(Request $request)
    {
        $cons = $request->all();
        $cons['s_date'] = ! empty($cons['s_date']) ? $cons['s_date'] : '2016-10-22';
        $cons['e_date'] = ! empty($cons['e_date']) ? $cons['e_date'] : date("Y-m-d", time());
        $list = OrdersProduct::getMarketPerformanceList($cons)->toArray(); // 统计信息列表
        if (empty($list)) {
            return ('暂无可导出数据');
        }
        $date = str_replace('-', '/', $cons['s_date']) . '-' . str_replace('-', '/', $cons['e_date']);
        $data['name'] = '销售业绩统计表_' . date("Y.m.d");
        $data['title'] = array(
            '时间',
            '总金额',
            '总单数'
        );
        $data['data'][] = array(
            $date,
            $list[0]['sum'] . '元',
            $list[0]['count'] . '单'
        );
        $data['length'] = [
            'A' => 30,
            'B' => 20,
            'C' => 20
        ];
        DownloadExcel::publicDownloadExcel($data);
    }

    /**
     * 导出城市排行报表
     */
    public function getCityRankExport(Request $request)
    {
        $cons = $request->all();
        $cons['s_date'] = ! empty($cons['s_date']) ? $cons['s_date'] : '2016-10-22';
        $cons['e_date'] = ! empty($cons['e_date']) ? $cons['e_date'] : date("Y-m-d", time());
        $list = Store::getCityRank($cons)->toArray(); // 城市排名列表
        if (empty($list)) {
            return ('暂无可导出数据');
        }
        $city_code = SyncCodeLibrary::detailCodeToCity()->toArray();
        $date = str_replace('-', '/', $cons['s_date']) . '-' . str_replace('-', '/', $cons['e_date']);
        $data['name'] = '销售业绩排名表_' . date("Y.m.d");
        $data['title'] = array(
            '时间',
            $date
        );
        $data['data'][0] = array(
            '排名',
            '城市',
            '交易额'
        );
        foreach ($list as $k => $v) {
            $data['data'][$k + 1] = array(
                $k + 1,
                $city_code[$v['city']],
                number_format($v['sum']) . '元'
            );
        }
        $data['length'] = [
            'A' => 20,
            'B' => 20,
            'C' => 20
        ];
        DownloadExcel::publicDownloadExcel($data);
    }
    
    /**
     * 导出区域排行报表
     */
    public function getAreaRankExport(Request $request)
    {
        $cons = $request->all();
        $cons['s_date'] = ! empty($cons['s_date']) ? $cons['s_date'] : '2016-10-22';
        $cons['e_date'] = ! empty($cons['e_date']) ? $cons['e_date'] : date("Y-m-d", time());
        $list = Store::getAreaRank($cons)->toArray(); // 区域排名列表
        if (empty($list)) {
            return ('暂无可导出数据');
        }
        $city_code = SyncCodeLibrary::detailCodeToCity()->toArray();
        $date = str_replace('-', '/', $cons['s_date']) . '-' . str_replace('-', '/', $cons['e_date']);
        $data['name'] = '销售业绩排名表_' . date("Y.m.d");
        $data['title'] = array(
            '时间',
            $date
        );
        $data['data'][0] = array(
            '排名',
            '区域总监',
            '交易额'
        );
        foreach ($list as $k => $v) {
            $data['data'][$k + 1] = array(
                $k + 1,
                $v['manager_name'],
                number_format($v['sum']) . '元'
            );
        }
        $data['length'] = [
            'A' => 20,
            'B' => 20,
            'C' => 20
        ];
        DownloadExcel::publicDownloadExcel($data);
    }
    
   /**
     * 导出风控数据统计报表
     */
    public function getRiskControlExport(Request $request)
    {
        
//         $cons = $request->all();
//         $cons['s_date'] = ! empty($cons['s_date']) ? $cons['s_date'] : '2016-10-22';
//         $cons['e_date'] = ! empty($cons['e_date']) ? $cons['e_date'] : date("Y-m-d", time());
//         $list = Store::getAreaRank($cons)->toArray(); // 区域排名列表
//         if (empty($list)) {
//             return ('暂无可导出数据');
//         }
//         $city_code = SyncCodeLibrary::detailCodeToCity()->toArray();
//         $date = str_replace('-', '/', $cons['s_date']) . '-' . str_replace('-', '/', $cons['e_date']);
//         $data['name'] = '销售业绩排名表_' . date("Y.m.d");
//         $data['title'] = array(
//             '时间',
//             $date
//         );
//         $data['data'][0] = array(
//             '排名',
//             '区域总监',
//             '交易额'
//         );
//         foreach ($list as $k => $v) {
//             $data['data'][$k + 1] = array(
//                 $k + 1,
//                 $v['manager_name'],
//                 number_format($v['sum']) . '元'
//             );
//         }
//         $data['length'] = [
//             'A' => 20,
//             'B' => 20,
//             'C' => 20
//         ];
//         DownloadExcel::publicDownloadExcel($data);
    }
}
?>