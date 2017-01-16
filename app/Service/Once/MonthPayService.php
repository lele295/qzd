<?php
namespace App\Service\Once;

use App\Model\Base\AsBaseInformationModel;
use App\Model\Base\SyncBusinessTypeModel;
use App\Service\mobile\Service;
use App\Util\IntersetRate;
use Illuminate\Support\Facades\DB;

class MonthPayService extends Service
{
    public function correct_month_pay_order(){
        AsBaseInformationModel::where(DB::raw('date_format(OperationTime,\'%Y-%m-%d\')'),'>=',date('Y-m-d',strtotime('2016-4-22')))->chunk(500,function($baseInformation){
            foreach($baseInformation as $item){
                $info = SyncBusinessTypeModel::where('TYPENO',$item->BusinessType)->first();
                $intersetRate = new IntersetRate($info);
                $pay_message = $intersetRate->get_month_pay();
                $pay_month = $pay_message['month_pay'];
                AsBaseInformationModel::where('Id',$item->Id)->update(array('MonthRepayment'=>$pay_month));
            }
        });
    }
    
}