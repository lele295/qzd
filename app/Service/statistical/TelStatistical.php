<?php
namespace App\Service\statistical;
use App\Log\Facades\Logger;
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
class TelStatistical{
    private $_query;
    public $_per_page_count = 15;

    public function __construct(Array $arr = []){
        $this->commQuery($arr);
    }


    private function commQuery(Array $arr = []){
        $this->_query = DB::table('tel_sale_statistical')
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
        $list = $this->_query->get();
        $dataList = array();
        foreach($list as $key=>$item){
            $tempArray = array($item->date,$item->book,$item->book_pick_up,$item->cancel,CommKit::fateToPercent($item->cancel_fate),$item->pass,CommKit::fateToPercent($item->pass_fate),$item->refund,CommKit::fateToPercent($item->refund_fate));
            array_push($dataList,$tempArray);
        }
        $data = array(
            'title'=>array('提单日期','后台录单总量','提单总量','取消数','取消率','审核通过数','通过率','否决数','否决率'),
            'data'=>$dataList,
            'name'=>'电销录单统计'
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
    }


    /**
     * 计算当天和前一天电销录单统计
     */
    static public function telbookCalc(){
        self::telbook(date('Y-m-d'));
        self::telbook(date('Y-m-d',strtotime('-1 day')));
        return true;
    }

    /**
     * @param $date 日期字符串2016-06-06
     */
    static public function telbook($date){
        Logger::info('计算电销录单统计:' . $date,'tel_statistical');
        $sqlStatement = "select (select count(*) from loan where id in (select loan_id from admin_loan) and date(updated_at) = '{$date}') as book_pick_up,(select count(*) from loan where id in (select loan_id from admin_loan) and date(updated_at) = '{$date}' and status='100') as cancel,(select count(*) from loan where id in (select loan_id from admin_loan) and date(updated_at) = '{$date}' and status='010') as refund,(select count(*) from loan where id in (select loan_id from admin_loan) and date(updated_at) = '{$date}' and status not in ('100','010','070')) as pass,count(*) as book from admin_loan where 	date(created_at) = '{$date}';";
        $res = DB::select($sqlStatement);
        $data = [
            'date'=>$date,
            'book'=>0,
            'book_pick_up'=>0,
            'pass'=>0,
            'cancel'=>0,
            'refund'=>0,
            'pass_fate'=>0,
            'cancel_fate'=>0,
            'refund_fate'=>0
        ];
        if($res){
            $data = array_merge($data,(array)$res[0]);
        }

        $obj = TelSaleStatisticalModel::firstOrCreate(['date'=>$data['date']]);
        $obj->book = $data['book'];
        $obj->book_pick_up = $data['book_pick_up'];
        $obj->pass = $data['pass'];
        $obj->cancel = $data['cancel'];
        $obj->refund = $data['refund'];
        $obj->pass_fate = Kits::mathRate($obj->pass,$obj->book_pick_up);
        $obj->cancel_fate = Kits::mathRate($obj->cancel,$obj->book_pick_up);
        $obj->refund_fate = Kits::mathRate($obj->refund,$obj->book_pick_up);
        $obj->save();
        Logger::info('计算电销录单统计结束:' . $date,'tel_statistical');
        return true;
    }
}