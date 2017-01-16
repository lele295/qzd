<?php
namespace App\Service\statistical;
use App\Log\Facades\Logger;
use App\Model\Base\TeleSaleStatisticalModel;
use App\Model\Base\TelSaleStatisticalModel;
use App\Util\CommKit;
use App\Util\DownloadExcel;
use App\Util\Kits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 *
 * Class TelStatistical
 * @package App\Service\statistical
 */
class TelesaleStatistical{
    private $_query;
    public $_per_page_count = 15;

    public function __construct(Array $arr = []){
        $this->commQuery($arr);
    }


    private function commQuery(Array $arr = []){
        $this->_query = DB::table('tele_sale_statistical')->selectRaw('tele_sale_statistical.*,admin.real_name')->leftJoin('admin','admin.id','=','admin_id')
            ->where(function($query) use ($arr){
                self::fitler($query,$arr);
            });
    }

    /**
     * 分页
     */
    public function paging(){
        return $this->_query->paginate($this->_per_page_count);
    }


    /**
     * 下载excel
     */
    public function downloadExcel(){
        $list = $this->_query;
        $dataList = array();
        $list->chunk(1000,function($chunkList) use(&$dataList){
            foreach($chunkList as $key=>$item){
                $tempArray = array($item->date,$item->real_name,$item->book_pick_up,$item->amount_total);
                array_push($dataList,$tempArray);
            }
        });

        $data = array(
            'title'=>array('提单日期','用户姓名','提单数','总成交金额'),
            'data'=>$dataList,
            'name'=>'坐席单量统计'
        );
        DownloadExcel::publicDownloadExcel($data);
        exit;
    }


    /**
     * @param $query
     * @param Array $arr
     * 搜索过滤条件,走两条逻辑
     */
    static public function fitler($query,Array $arr = [])
    {
        $query->whereRaw("UNIX_TIMESTAMP(date) between UNIX_TIMESTAMP('  ".CommKit::dateStrDefaultToday('start_time')."') and UNIX_TIMESTAMP('".CommKit::dateStrDefaultToday('end_time')."')");
        CommKit::equalQuery($query,['real_name'=>Request::input('real_name')]);
    }


    /**
     * 计算当天和前一天电销录单统计
     */
    static public function calc(){
        self::telbook(date('Y-m-d'));
        self::telbook(date('Y-m-d',strtotime('-1 day')));
        return true;
    }

    /**
     * @param $date 日期字符串2016-06-06
     */
    static public function telbook($date){
        Logger::info('计算电销录单统计:' . $date,'tel_statistical');

        $sqlStatement = "select admin_id,sum(loan_amount) as amount_total,count(*) as book_pick_up from (SELECT admin_id,loan_amount FROM admin_loan LEFT JOIN loan ON admin_loan.loan_id = loan.id where date(loan.updated_at) = '{$date}') A GROUP BY A.admin_id;";
        $res = DB::select($sqlStatement);
        foreach($res as $item){
            Logger::info("坐席统计admin:".$item->admin_id.",date:" . $date,'telesale_statistical');
            $obj = TeleSaleStatisticalModel::firstOrCreate(['date'=>$date,'admin_id'=>$item->admin_id]);
            $obj->book_pick_up = $item->book_pick_up;
            $obj->amount_total = $item->amount_total;
            $obj->save();
            Logger::info("坐席统计完成admin:".$item->admin_id.",date:" . $date,'telesale_statistical');
        }
        return true;
    }
}