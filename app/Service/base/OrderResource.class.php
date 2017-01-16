<?php
namespace App\Service\base;
use App\Log\Facades\Logger;
use App\Model\Base\AsBaseInformationModel;
use App\Model\Base\AsCommAddModel;
use App\Model\Base\AsCustombaseMessageMobel;
use App\Model\Base\AsCustomPicModel;
use App\Model\Base\AsFamilyMessageModel;
use App\Model\Base\AsIncomeMessageModel;
use App\Model\Base\AsInsideMessageModel;
use App\Model\Base\AsRepaymentMessage;
use App\Model\Base\AsWordMessageModel;
use App\Model\Base\AuthModel;
use App\Model\Base\CommErrorModel;
use App\Model\Base\LoanModel;
use App\Model\Base\ResourceErrorModel;
use App\Model\Base\SyncModel;
use App\Service\admin\SendService;
use App\Service\base\Order;
use App\Service\mobile\Service;
use App\Util\AppKits;
use App\Util\DxOperator;
use App\Util\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class OrderResource{
    const MOBILE_TYPE_SELF = 'MobileTelephone';
    const MOBILE_TYPE_FAMILY = 'KinshipTel';
    const MOBILE_TYPE_OTHER = 'ContactTel';
    private $_order_obj;
    private $_as_work_message_model;

    /**
     * @param $orderFlag 订单标识,传id或者传order
     */
    public function __construct($orderFlag){
        if($orderFlag instanceof Order){
            $this->_order_obj = $orderFlag;
        }else{
            $this->_order_obj = new Order($orderFlag);
        }
    }

    /**
     * 初始化数据
     * @desc 初始化
     */
    public function init($apiData){
        $user_id = $this->_order_obj->_model->user_id;
        $loanModel = new LoanModel();
        $loanInfo = $loanModel->get_loan_newest_eliminate_un_submit($user_id);  //根据用户ID获取上一笔订单
        if($loanInfo){
            return $this->initPre($loanInfo->id, $apiData);
        }else{
            return $this->initPos($apiData);
        }
    }

    /**
     * pos贷初始化
     */
    public function initPos($apiData){
        $user = $this->_order_obj->initUser();
        $ownInfo = $this->_order_obj->ownInfo();
        /**
         * 回填信息分几类
         * 1.门店信息
         * 2.产品信息
         * 3.分期服务内容
         * 4.归集户信息
         * 5.客户信息
         * 6.代扣账号信息
         */
        $asBaseInformation = AsBaseInformationModel::firstOrCreate($this->_order_obj->_filter);
        $asCustomBaseMessage = AsCustombaseMessageMobel::firstOrCreate($this->_order_obj->_filter);
        $asWorkMessage = AsWordMessageModel::firstOrCreate($this->_order_obj->_filter);
        $asRepaymentMessage = AsRepaymentMessage::firstOrCreate($this->_order_obj->_filter);
        $asIncomeMessage = AsIncomeMessageModel::firstOrCreate($this->_order_obj->_filter);
        $asInsideMessage = AsInsideMessageModel::firstOrCreate($this->_order_obj->_filter);
        $asFamilyMessage = AsFamilyMessageModel::firstOrCreate($this->_order_obj->_filter);
        $asCommAdd = AsCommAddModel::firstOrCreate($this->_order_obj->_filter);
        $asCustomPic = AsCustomPicModel::firstOrCreate($this->_order_obj->_filter);

        //基本信息
        $asBaseInformation->BusinessType = $apiData['BusinessType'];
        $asBaseInformation->ProductName = $apiData['ProductName'];
        $asBaseInformation->CreditCycle = $this->_order_obj->_model->issure;
        $asBaseInformation->Periods = $this->_order_obj->_model->loan_period;
        $asBaseInformation->MonthRepayment = $apiData['MonthRepayment'];
        $asBaseInformation->BusinessSum = $this->_order_obj->_model->loan_amount;
        $asBaseInformation->CashPurpose = $this->_order_obj->_model->remark;
        $asBaseInformation->PurposeRemark = $this->_order_obj->_model->remark_descript;
        $asBaseInformation->SubProductType = $this->_order_obj->subProductType();
        $asBaseInformation->EventID = $apiData['EventID'];
        $asBaseInformation->EventName = $apiData['EventName'];

        //内部信息
        $asInsideMessage->Stores = $apiData['Stores'];
        $asInsideMessage->StoreCityCode = $apiData['StoreCityCode'];
        $asInsideMessage->DSM = 1; //默认在场
        $asInsideMessage->Falg6 = 1;
        $asInsideMessage->InteriorCode = 1;

        //客户基本信息
        $asCustomBaseMessage->CustomerID = $ownInfo->CustomerID;
        $asCustomBaseMessage->CustomerName = $ownInfo->real_name;
        $asCustomBaseMessage->CertType = $apiData['CertType'];
        $asCustomBaseMessage->CertID = $apiData['CertID'];
        $asCustomBaseMessage->Sex = $apiData['Sex'];
        $asCustomBaseMessage->Issueinstitution = $apiData['Issueinstitution'];
        $asCustomBaseMessage->MaturityDate = date('Y-m-d',strtotime($apiData['MaturityDate']));
        $asCustomBaseMessage->NativePlace = $apiData['NativePlace'];
        $asCustomBaseMessage->Villagetown = $apiData['Villagetown'];
        $asCustomBaseMessage->Street = $apiData['Street'];
        $asCustomBaseMessage->Community = $apiData['Community'];
        $asCustomBaseMessage->CellNo = $apiData['CellNo'];
        $asCustomBaseMessage->Flag2 = $apiData['Flag2'];
        $asCustomBaseMessage->FamilyAdd = $apiData['FamilyAdd'];
        $asCustomBaseMessage->Countryside = $apiData['Countryside'];
        $asCustomBaseMessage->Villagecenter = $apiData['Villagecenter'];
        $asCustomBaseMessage->Plot = $apiData['Plot'];
        $asCustomBaseMessage->Room = $apiData['Room'];


        //单位信息
//        $asWorkMessage->WorkCorp = $apiData['WorkCorp'];
        $asWorkMessage->Flag3 = $apiData['Flag3']?$apiData['Flag3']:0;
        $asWorkMessage->EmployRecord = $apiData['EmployRecord'];
        $asWorkMessage->HeadShip = $apiData['HeadShip'];
        $asWorkMessage->UnitKind = $apiData['UnitKind'];
        $asWorkMessage->CellProperty = $apiData['CellProperty'];
        if(DxOperator::$flag){
            $asWorkMessage->WorkCorp = $apiData['WorkCorp'];
            $asWorkMessage->UnitCountryside = $apiData['UnitCountryside'];
            $asWorkMessage->UnitStreet = $apiData['UnitStreet'];
            $asWorkMessage->UnitRoom = $apiData['UnitRoom'];
            $asWorkMessage->UnitNo = $apiData['UnitNo'];
            $asWorkMessage->WorkZip = $apiData['WorkZip'];
            $asWorkMessage->WorkAdd = $apiData['WorkAdd'];
        }else{
            $asWorkMessage->WorkAdd = 0;
        }

        //邮寄地址及联系方式
        $asCommAdd->Flag8 = $apiData['Flag8'];
        $asCommAdd->CommAdd = $apiData['CommAdd'];
        $asCommAdd->EmailCountryside = $apiData['EmailCountryside'];
        $asCommAdd->EmailStreet = $apiData['EmailStreet'];
        $asCommAdd->EmailPlot = $apiData['EmailPlot'];
        $asCommAdd->EmailRoom = $apiData['EmailRoom'];
        if(env("APP_ENV")=="product"){
            $asCommAdd->MobileTelephone = $user->mobile;
        }elseif(App::environment('test')){
            $asCommAdd->MobileTelephone = '13'.substr(time(),-9);
        }else{
            $asCommAdd->MobileTelephone = $apiData['MobileTelephone'];
        }
        if(DxOperator::$flag){
            $asCommAdd->WorkTel = $apiData['WorkTel'];
            $asCommAdd->WorkTelPlus = $apiData['WorkTelPlus'];
        }

        //家庭信息
        $asFamilyMessage->Marriage = $apiData['Marriage'];
        $asFamilyMessage->SPOUSEWORKCORP = $apiData['SPOUSEWORKCORP'];
        $asFamilyMessage->SPOUSEWORKTEL = $apiData['SPOUSEWORKTEL'];
        if(DxOperator::$flag){
            $asFamilyMessage->KinshipName = $apiData['KinshipName'];
            $asFamilyMessage->KinshipTel = $apiData['KinshipTel'];
            $asFamilyMessage->RelativeType = $apiData['RelativeType'];
        }else{
            $asFamilyMessage->KinshipName = "";//$apiData['KinshipName'];
            $asFamilyMessage->KinshipTel = "";//$apiData['KinshipTel'];
            $asFamilyMessage->RelativeType = "";//$apiData['RelativeType'];
        }
        $asFamilyMessage->Flag10 = $apiData['Flag10'];
        $asFamilyMessage->KinshipAdd = $apiData['KinshipAdd'];
        $asFamilyMessage->Spouse_Community = $apiData['Spouse_Community'];
        $asFamilyMessage->SpouseName = $apiData['SpouseName'];
        $asFamilyMessage->SpouseTel = $apiData['SpouseTel'];
        $asFamilyMessage->Childrentotal = $apiData['Childrentotal'];
        $asFamilyMessage->House = $apiData['House'];
        $asFamilyMessage->Houserent = $apiData['HouseRent'];


        //收入及其它信息
        $asIncomeMessage->FamilyMonthIncome = $apiData['FamilyMonthIncome'];
        $asIncomeMessage->JobTime = $apiData['JobTime'];
        $asIncomeMessage->JobTotal = $apiData['JobTotal'];
        $asIncomeMessage->OtherRevenue = $apiData['OtherRevenue'];
        $asIncomeMessage->Falg4 = $apiData['Falg4'];
        $asIncomeMessage->EduExperience = $apiData['EduExperience'];
        $asIncomeMessage->Severaltimes = $apiData['Severaltimes'];
        if(DxOperator::$flag){
            $asIncomeMessage->SelfMonthIncome = $apiData['SelfMonthIncome'];
            $asIncomeMessage->OtherContact = $apiData['OtherContact'];
            $asIncomeMessage->Contactrelation = $apiData['Contactrelation'];
            $asIncomeMessage->ContactTel = $apiData['ContactTel'];
        }else{
            $asIncomeMessage->OtherContact = "";//$apiData['OtherContact'];
            $asIncomeMessage->Contactrelation = '8';//$apiData['Contactrelation'];
            $asIncomeMessage->ContactTel = "";//$apiData['ContactTel'];
        }

        //归集户信息
        $asRepaymentMessage->RepaymentWay = 1;//默认都是代扣
        $asRepaymentMessage->RepaymentNo = $apiData['TurnAccountNumber'];
        $asRepaymentMessage->RepaymentBank = $apiData['OpenBank'];
        $asRepaymentMessage->RepaymentBankName = $apiData['OpenBankName'];
        $asRepaymentMessage->RepaymentName = $apiData['TurnAccountName'];

        //代扣账号信息
        /**
         * 查看支行代码是否有效
         */
        $bankDetail  = SyncModel::bankBranchDetialNormal($apiData['OpenBranch']);
        if(false === ($bankDetail instanceof CommErrorModel)){
            $asRepaymentMessage->ReplaceAccount = $apiData['RepaymentNo'];
            $asRepaymentMessage->OpenBank = $bankDetail['OpenBank'];
            $asRepaymentMessage->OpenBankName = $bankDetail['OpenBankName'];
            $asRepaymentMessage->City = $bankDetail['City'];
            $asRepaymentMessage->CityName = $bankDetail['CityName'];
            $asRepaymentMessage->OpenBranch = $bankDetail['OpenBranch'];
            $asRepaymentMessage->OpenBranchName = $bankDetail['OpenBranchName'];
        }else{
            $asRepaymentMessage->ReplaceAccount = $apiData['RepaymentNo'];
            $asRepaymentMessage->OpenBank = $apiData['RepaymentBankCode'];
            $asRepaymentMessage->OpenBankName = $apiData['RepaymentBank'];
            $asRepaymentMessage->City = $apiData['CityCode'];
            $asRepaymentMessage->CityName = $apiData['CityName'];
            $asRepaymentMessage->OpenBranch = $apiData['OpenBranch'];
            $asRepaymentMessage->OpenBranchName = $apiData['OpenBranchName'];
        }

        $asRepaymentMessage->ReplaceName = $ownInfo->real_name;
        $asBaseInformation->save();
        $asRepaymentMessage->save();
        $asIncomeMessage->save();
        $asWorkMessage->save();
        $asCustomBaseMessage->save();
        $asInsideMessage->save();
        $asFamilyMessage->save();
        $asCommAdd->save();
        $asCustomPic->save();
        return true;
    }

    /**
     * 上一笔初始化
     */
    public function initPre($loan_id, $apiData){
        /**
         * 回填信息分几类
         * 1.门店信息
         * 2.产品信息
         * 3.分期服务内容
         * 4.归集户信息
         * 5.客户信息
         * 6.代扣账号信息
         */
        $asBaseInformation = AsBaseInformationModel::firstOrCreate($this->_order_obj->_filter);
        $asCustomBaseMessage = AsCustombaseMessageMobel::firstOrCreate($this->_order_obj->_filter);
        $asWorkMessage = AsWordMessageModel::firstOrCreate($this->_order_obj->_filter);
        $asRepaymentMessage = AsRepaymentMessage::firstOrCreate($this->_order_obj->_filter);
        $asIncomeMessage = AsIncomeMessageModel::firstOrCreate($this->_order_obj->_filter);
        $asInsideMessage = AsInsideMessageModel::firstOrCreate($this->_order_obj->_filter);
        $asFamilyMessage = AsFamilyMessageModel::firstOrCreate($this->_order_obj->_filter);
        $asCommAdd = AsCommAddModel::firstOrCreate($this->_order_obj->_filter);
        $asCustomPic = AsCustomPicModel::firstOrCreate($this->_order_obj->_filter);

        /**
         * 取出以前的数据
         */
        $filter = array('OrderId' => $loan_id);
        $asBaseInformationOrigin = AsBaseInformationModel::where($filter)->first();
        $asCustomBaseMessageOrigin = AsCustombaseMessageMobel::where($filter)->first();
        $asWorkMessageOrigin = AsWordMessageModel::where($filter)->first();
        $asRepaymentMessageOrigin = AsRepaymentMessage::where($filter)->first();
        $asIncomeMessageOrigin = AsIncomeMessageModel::where($filter)->first();
        $asInsideMessageOrigin = AsInsideMessageModel::where($filter)->first();
        $asFamilyMessageOrigin = AsFamilyMessageModel::where($filter)->first();
        $asCommAddOrigin = AsCommAddModel::where($filter)->first();
        //$asCustomPicOrigin = AsCustomPicModel::where($filter)->first();

        if(false === (($asBaseInformationOrigin instanceof AsBaseInformationModel) && ($asCustomBaseMessageOrigin instanceof AsCustombaseMessageMobel) && ($asWorkMessageOrigin instanceof AsWordMessageModel) && ($asRepaymentMessageOrigin instanceof AsRepaymentMessage) && ($asIncomeMessageOrigin instanceof AsIncomeMessageModel) && ($asInsideMessageOrigin instanceof AsInsideMessageModel) && ($asFamilyMessageOrigin instanceof AsFamilyMessageModel) && ($asCommAddOrigin instanceof AsCommAddModel))){
            Logger::info('订单ID：'.$loan_id.'-数据丢失');
            return $this->initPos($apiData);
        }

        //基本信息
        $asBaseInformation->BusinessType = $apiData['BusinessType'];
        $asBaseInformation->ProductName = $apiData['ProductName'];
        $asBaseInformation->CreditCycle = $this->_order_obj->_model->issure;
        $asBaseInformation->Periods = $this->_order_obj->_model->loan_period;
        $asBaseInformation->MonthRepayment = $apiData['MonthRepayment'];
        $asBaseInformation->BusinessSum = $this->_order_obj->_model->loan_amount;
        $asBaseInformation->CashPurpose = $this->_order_obj->_model->remark;
        $asBaseInformation->PurposeRemark = $this->_order_obj->_model->remark_descript;
        $asBaseInformation->SubProductType = $this->_order_obj->subProductType();
        $asBaseInformation->EventID = $apiData['EventID'];
        $asBaseInformation->EventName = $apiData['EventName'];

        //内部信息
        $asInsideMessage->Stores = $apiData['Stores'];
        $asInsideMessage->StoreCityCode = $apiData['StoreCityCode'];
        $asInsideMessage->DSM = 1; //默认在场
        $asInsideMessage->Falg6 = 1;
        $asInsideMessage->InteriorCode = 1;

        //客户基本信息
        $asCustomBaseMessage->CustomerID = $asCustomBaseMessageOrigin->CustomerID;
        $asCustomBaseMessage->CustomerName = $asCustomBaseMessageOrigin->CustomerName;
        $asCustomBaseMessage->CertType = $apiData['CertType'];
        $asCustomBaseMessage->CertID = $apiData['CertID'];
        $asCustomBaseMessage->Sex = $apiData['Sex'];
        $asCustomBaseMessage->Issueinstitution = $apiData['Issueinstitution'];
        $asCustomBaseMessage->MaturityDate = date('Y-m-d',strtotime($apiData['MaturityDate']));
        $asCustomBaseMessage->NativePlace = $asCustomBaseMessageOrigin->NativePlace;
        $asCustomBaseMessage->Villagetown = $asCustomBaseMessageOrigin->Villagetown;
        $asCustomBaseMessage->Street = $asCustomBaseMessageOrigin->Street;
        $asCustomBaseMessage->Community = $asCustomBaseMessageOrigin->Community;
        $asCustomBaseMessage->CellNo = $asCustomBaseMessageOrigin->CellNo;
        $asCustomBaseMessage->Flag2 = $asCustomBaseMessageOrigin->Flag2;
        $asCustomBaseMessage->FamilyAdd = $asCustomBaseMessageOrigin->FamilyAdd;
        $asCustomBaseMessage->Countryside = $asCustomBaseMessageOrigin->Countryside;
        $asCustomBaseMessage->Villagecenter = $asCustomBaseMessageOrigin->Villagecenter;
        $asCustomBaseMessage->Plot = $asCustomBaseMessageOrigin->Plot;
        $asCustomBaseMessage->Room = $asCustomBaseMessageOrigin->Room;

        //单位信息
        $asWorkMessage->WorkCorp = $asWorkMessageOrigin->WorkCorp;
        $asWorkMessage->Flag3 = $asWorkMessageOrigin->Flag3 ? $asWorkMessageOrigin->Flag3 : 0;
        $asWorkMessage->WorkAdd = $asWorkMessageOrigin->WorkAdd;
        $asWorkMessage->EmployRecord = $asWorkMessageOrigin->EmployRecord;
        $asWorkMessage->HeadShip = $asWorkMessageOrigin->HeadShip;
        $asWorkMessage->UnitKind = $asWorkMessageOrigin->UnitKind;
        $asWorkMessage->CellProperty = $asWorkMessageOrigin->CellProperty;
        $asWorkMessage->UnitCountryside = $asWorkMessageOrigin->UnitCountryside;
        $asWorkMessage->UnitStreet = $asWorkMessageOrigin->UnitStreet;
        $asWorkMessage->UnitRoom = $asWorkMessageOrigin->UnitRoom;
        $asWorkMessage->UnitNo = $asWorkMessageOrigin->UnitNo;
        if(DxOperator::$flag){
            $asWorkMessage->WorkZip = $asWorkMessageOrigin->WorkZip;
        }

        //邮寄地址及联系方式
        $asCommAdd->Flag8 = $asCommAddOrigin->Flag8;
        $asCommAdd->CommAdd = $asCommAddOrigin->CommAdd;
        $asCommAdd->EmailCountryside = $asCommAddOrigin->EmailCountryside;
        $asCommAdd->EmailStreet = $asCommAddOrigin->EmailStreet;
        $asCommAdd->EmailPlot = $asCommAddOrigin->EmailPlot;
        $asCommAdd->EmailRoom = $asCommAddOrigin->EmailRoom;
        $asCommAdd->MobileTelephone = $asCommAddOrigin->MobileTelephone;
        $asCommAdd->WorkTel = $asCommAddOrigin->WorkTel;
        if(DxOperator::$flag){
            $asCommAdd->WorkTelPlus = $asCommAddOrigin->WorkTelPlus;
        }

        //家庭信息
        $asFamilyMessage->Marriage = $asFamilyMessageOrigin->Marriage;
        $asFamilyMessage->SPOUSEWORKCORP = $asFamilyMessageOrigin->SPOUSEWORKCORP;
        $asFamilyMessage->SPOUSEWORKTEL = $asFamilyMessageOrigin->SPOUSEWORKTEL;
        $asFamilyMessage->KinshipName = $asFamilyMessageOrigin->KinshipName;
        $asFamilyMessage->KinshipTel = $asFamilyMessageOrigin->KinshipTel;
        $asFamilyMessage->Flag10 = $asFamilyMessageOrigin->Flag10;
        $asFamilyMessage->KinshipAdd = $asFamilyMessageOrigin->KinshipAdd ;
        $asFamilyMessage->RelativeType = $asFamilyMessageOrigin->RelativeType;
        $asFamilyMessage->Spouse_Community = $asFamilyMessageOrigin->Spouse_Community;
        $asFamilyMessage->SpouseName = $asFamilyMessageOrigin->SpouseName;
        $asFamilyMessage->SpouseTel = $asFamilyMessageOrigin->SpouseTel;
        $asFamilyMessage->Childrentotal = $asFamilyMessageOrigin->Childrentotal;
        $asFamilyMessage->House = $asFamilyMessageOrigin->House;
        $asFamilyMessage->Houserent = $asFamilyMessageOrigin->Houserent;
        $asFamilyMessage->OtherTelephone = $asFamilyMessageOrigin->OtherTelephone;

        //收入及其它信息
        $asIncomeMessage->FamilyMonthIncome = $asIncomeMessageOrigin->FamilyMonthIncome;
        $asIncomeMessage->JobTime = $asIncomeMessageOrigin->JobTime;
        $asIncomeMessage->JobTotal = $asIncomeMessageOrigin->JobTotal;
        $asIncomeMessage->OtherRevenue = $asIncomeMessageOrigin->OtherRevenue;
        $asIncomeMessage->Falg4 = $asIncomeMessageOrigin->Falg4;
        $asIncomeMessage->EduExperience = $asIncomeMessageOrigin->EduExperience;
        $asIncomeMessage->SelfMonthIncome = $asIncomeMessageOrigin->SelfMonthIncome;
        $asIncomeMessage->Severaltimes = $asIncomeMessageOrigin->Severaltimes;
        $asIncomeMessage->OtherContact = $asIncomeMessageOrigin->OtherContact;
        $asIncomeMessage->Contactrelation = $asIncomeMessageOrigin->Contactrelation;
        $asIncomeMessage->ContactTel = $asIncomeMessageOrigin->ContactTel;//$apiData['ContactTel'];

        //归集户信息
        $asRepaymentMessage->RepaymentWay = 1;//默认都是代扣
        $asRepaymentMessage->RepaymentNo = $asRepaymentMessageOrigin->RepaymentNo;
        $asRepaymentMessage->RepaymentBank = $asRepaymentMessageOrigin->RepaymentBank;
        $asRepaymentMessage->RepaymentBankName = $asRepaymentMessageOrigin->RepaymentBankName;
        $asRepaymentMessage->RepaymentName = $asRepaymentMessageOrigin->RepaymentName;

        //代扣账号信息
        /**
         * 查看支行代码是否有效
         */
        $asRepaymentMessage->ReplaceAccount = $asRepaymentMessageOrigin->ReplaceAccount;
        $asRepaymentMessage->OpenBank = $asRepaymentMessageOrigin->OpenBank;
        $asRepaymentMessage->OpenBankName = $asRepaymentMessageOrigin->OpenBankName;
        $asRepaymentMessage->City = $asRepaymentMessageOrigin->City;
        $asRepaymentMessage->CityName = $asRepaymentMessageOrigin->CityName;
        $asRepaymentMessage->OpenBranch = $asRepaymentMessageOrigin->OpenBranch;
        $asRepaymentMessage->OpenBranchName = $asRepaymentMessageOrigin->OpenBranchName;
        $asRepaymentMessage->ReplaceName = $asRepaymentMessageOrigin->ReplaceName;

        $asBaseInformation->save();
        $asRepaymentMessage->save();
        $asIncomeMessage->save();
        $asWorkMessage->save();
        $asCustomBaseMessage->save();
        $asInsideMessage->save();
        $asFamilyMessage->save();
        $asCommAdd->save();
        $asCustomPic->save();
        return true;
    }

    public function customBaseMessage(){
        $obj = AsCustomBaseMessage::firstOrCreate($this->_order_obj->_filter);
        return $obj;
    }

    /**
     * 工作信息
     */
    public function workMessage(){
        $obj = AsWorkMessage::firstOrCreate($this->_order_obj->_filter);
        return $obj;
    }

    /**
     * 还款信息
     */
    public function repaymentMessage(){
        $obj = AsRepaymentMessage::firstOrCreate($this->_order_obj->_filter);
        return $obj;
    }

    /**
     * 家庭信息
     * @return mixed
     */
    public function familyMessage(){
        $obj = AsFamilyMessage::firstOrCreate($this->_order_obj->_filter);
        return $obj;
    }


    public function incomeMessage(){
        $obj = AsIncomeMessage::firstOrCreate($this->_order_obj->_filter);
        return $obj;
    }

    public function commAdd(){
        $obj = AsCommAdd::firstOrCreate($this->_order_obj->_filter);
        return $obj;
    }

    /**
     * 图片信息
     */
    public function customPic(){
        $obj = AsCustomPic::firstOrCreate($this->_order_obj->_filter);
        return $obj;
    }

    /**
     * 获得一个订单的户籍地址，现居住地址，单位地址,邮寄地址
     */
    public function nativeAddress(){
        $obj = $this->customBaseMessage();
        $addressConfig = array(
            'city_id'=>$obj->NativePlace,
            'city_name'=>SyncModel::cityName($obj->NativePlace),
            'town'=>$obj->Villagetown,
            'street'=>$obj->Street,
            'community'=>$obj->Community,
            'room'=>$obj->CellNo
        );
        return $addressConfig;
    }

    public function familyAddress(){
        $obj = $this->customBaseMessage();
        if($obj->Flag2 == 1){
            return $this->nativeAddress();
        }
        $addressConfig = array(
            'city_id'=>$obj->FamilyAdd,
            'city_name'=>SyncModel::cityName($obj->FamilyAdd),
            'town'=>$obj->Countryside,
            'street'=>$obj->Villagecenter,
            'community'=>$obj->Plot,
            'room'=>$obj->Room
        );
        return $addressConfig;
    }

    public function workAddress(){
        $obj = $this->workMessage();
        $addressConfig = array(
            'city_id'=>$obj->WorkAdd,
            'city_name'=>SyncModel::cityName($obj->WorkAdd),
            'town'=>$obj->UnitCountryside,
            'street'=>$obj->UnitStreet,
            'community'=>$obj->UnitRoom,
            'room'=>$obj->UnitNo
        );
        return $addressConfig;
    }

    public function commAddress(){
        $obj = $this->workMessage();
        $addressConfig = array(
            'city_id'=>$obj->WorkAdd,
            'city_name'=>SyncModel::cityName($obj->WorkAdd),
            'town'=>$obj->UnitCountryside,
            'street'=>$obj->UnitStreet,
            'community'=>$obj->UnitRoom,
            'room'=>$obj->UnitNo
        );
        return $addressConfig;
    }

    /**
     * 把户籍地址，现在居住地址，工作地址转换成json串
     */
    public function addressJson(){
        $native = $this->nativeAddress();
        $family = $this->familyAddress();
        $work = $this->workAddress();

        return json_encode(array(
            array('key'=>'3','status'=>$native['city_id']?true:false,'data'=>$native),
            array('key'=>'1','status'=>$family['city_id']?true:false,'data'=>$family),
            array('key'=>'2','status'=>$work['city_id']?true:false,'data'=>$work)
        ),JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc 易百分系统在默认情况下需要像这个表提供这几个字段
     * 产品名称
     * 总价格
     * 分期期数
     * 每月还款额
     * 贷款本金
     * 首付状态
     * 产品类型
     * 运作模式
     */
    public function saBaseInformationDefault(){
        return array(
            'business_type'=>$this->_order_obj->_model->BusinessType,
            'product_name'=>$this->_order_obj->_model->ProductName,
            'total_price'=>$this->_order_obj->_model->price,
            'periods'=>$this->_order_obj->_model->periods,
            'month_repayment'=>$this->_order_obj->_model->month_payment,
            'downpayment_status'=>1,
            'business_sum'=>$this->_order_obj->_model->price,
            'operator_mode'=>'学生消费贷',
            'sub_product_type_name'=>'学生消费贷'
        );
    }

    /**
     * @desc 学校需要默认提供的几个字段
     * 学校全称
     * 所在学院
     * 系
     * 专业名称
     * 班级
     * 学号
     */
    public function saSchoolMessageDefault(){
        $info = Authentication::where(array('user_id'=>$this->_order_obj->_model->user_id))->first();
        if($info){
            return array(
                'school_name'=>$info->campus_dep,
                'school_department'=>$info->department,
                'school_professional_name'=>$info->major,
                'school_class'=>$info->class,
                'school_student_no'=>$info->student_no,
            );
        }else{
            return array();
        }
    }

    /**
     * @desc 易百分系统在默认情况下需要像这个表提供这几个字段
     * 价格
     * 自付金额
     */
    public function saProductMessageDefault(){
        return array(
            'price1'=>$this->_order_obj->_model->fav_price,
            'total_sum1'=>$this->_order_obj->_model->down_payment,
            'business_sum1'=>$this->_order_obj->_model->fav_price - $this->_order_obj->_model->down_payment
        );
    }



    /**
     * @desc 提交安硕之前的check
     */
    public function submitCheck(){
        $returnFormData = array();
        $formData = AsBaseInformationModel::tryCheck($this->_order_obj->id());
        if($formData instanceof ResourceErrorModel){
            return $formData;
        }else{
            $returnFormData = array_merge($returnFormData,$formData);
        }

        $formData = AsRepaymentMessage::tryCheck($this->_order_obj->id());
        if($formData instanceof ResourceErrorModel){
            Logger::info(json_encode($formData));
            return $formData;
        }else{
            $returnFormData = array_merge($returnFormData,$formData);
        }

        $formData = AsInsideMessageModel::tryCheck($this->_order_obj->id());
        if($formData instanceof ResourceErrorModel){
            return $formData;
        }else{
            $returnFormData = array_merge($returnFormData,$formData);
        }

        $formData = AsCustombaseMessageMobel::tryCheck($this->_order_obj->id());
        if($formData instanceof ResourceErrorModel){
            return $formData;
        }else{
            $returnFormData = array_merge($returnFormData,$formData);
        }


        $formData = AsWordMessageModel::tryCheck($this->_order_obj->id());
        if($formData instanceof ResourceErrorModel){
            return $formData;
        }else{
            $returnFormData = array_merge($returnFormData,$formData);
        }


        $formData = AsCommAddModel::tryCheck($this->_order_obj->id());
        if($formData instanceof ResourceErrorModel){
            return $formData;
        }else{
            $returnFormData = array_merge($returnFormData,$formData);
        }


        $formData = AsFamilyMessageModel::tryCheck($this->_order_obj->id());
        if($formData instanceof ResourceErrorModel){
            return $formData;
        }else{
            $returnFormData = array_merge($returnFormData,$formData);
        }


        $formData = AsIncomeMessageModel::tryCheck($this->_order_obj->id());
        if($formData instanceof ResourceErrorModel){
            return $formData;
        }else{
            $returnFormData = array_merge($returnFormData,$formData);
        }

        if(true === ($brige = AppKits::bridge())){
            //检测图片但是不要任何内容
            $formData = AsCustomPicModel::tryCheckButData($this->_order_obj->id());
            if($formData instanceof ResourceErrorModel){
                return $formData;
            }
        }

        return $returnFormData;
    }


    /**
     * 返回汽车金融用户的资料填写状态
     * @return array
     */
    public function checkStatusArray(){
       $configArray = array(
           'fill_out'=>false,
           'custom_base_message'=>false,
           'work_message'=>false,
           'repayment_message'=>false,
           'comm_add_message'=>false,
           'family_message'=>false,
           'income_message'=>false
       );

        //检测个人信息
        $obj = AsCustombaseMessageMobel::where($this->_order_obj->_filter)->first();
        $res = Rule::custom_base_message_filter($obj->toArray());
        if($res['status']){
            $configArray['custom_base_message'] = true;
        }

        //开户信息
        $obj = AsRepaymentMessage::where($this->_order_obj->_filter)->first();
        $res = Rule::repayment_message_filter($obj->toArray());
        if($res['status']){
            $configArray['repayment_message'] = true;
        }

        return $configArray;
    }


    /**
     * @desc 图片上传检测
     * @param $serialNo
     * @param $saId
     * @return mixed
     */
    public function submitPhoto($serialNo,$saId){
        $formData = AsCustomPicModel::tryCheck($this->_order_obj->_model->id);

        if($formData instanceof ResourceError){
            return $formData;
        }

        $formData = array(
            'objType'=>'Business',
            'objNo'=>$serialNo,
            'userId'=>$saId,
            'orgId'=>15,
            'image'=>$formData
        );
        return array('args'=>json_encode($formData));
    }

    /**
     *为pc端单独提供的公共类方法
     */
    public function commit(){
        $reponse = $this->submitCheck();
        if($reponse instanceof ResourceErrorModel){
            return $reponse->toArray();
        }
        unset($reponse['SPOUSEWORKCORP']);
        unset($reponse['SPOUSEWORKTEL']);
        if(!$reponse['OtherTelephone']){
            unset($reponse['OtherTelephone']);  //其它联系方式
        }
        unset($reponse['Flag6']);
        if(!$reponse['OtherRevenue']){
            unset($reponse['OtherRevenue']);    //其它月收入
        }
        unset($reponse['Severaltimes']);
        unset($reponse['Issueinstitution']);
        if(!$reponse['WorkTelPlus']){
            unset($reponse['WorkTelPlus']);   //分机号
        }
        $reponse['Flag2'] = intval($reponse['Flag2']);
        //调用接口
        $reponse = array_merge($reponse,array('InputUserID'=>$this->_order_obj->_model->sa_id,'Salesexecutive'=>$this->_order_obj->_model->sa_id,'ContactTime'=>$this->_order_obj->_model->contact_time));
        $reponse["PurposeRemark"] = $this->_order_obj->_model->remark_descript;
        //2016-07-22添加：增加SureType字段：JQM、FQG、FQGAPP
        switch($this->_order_obj->_model->source){
            case 1:
                $sureType = 'FQG';
                break;
            case 2:
                $sureType = 'JQM';
                break;
            case 3:
                $sureType = 'JQM';
                break;
            case 4:
                $sureType = 'TS';
                break;
            case 5:
                $sureType = 'FQG';
                break;
            default:
                $sureType = 'JQM';
                break;
        }
        $reponse['SureType'] = $sureType;
        $res = (new Asapi())->submit_order($reponse);
        //判定返回值是否成功
        if(false == AsKits::submitModelOk($res)){
            return array('status'=>false,'data'=>'通信故障，无法提交！');
        }else{
            $loan_model = new LoanModel();
            if(true !== ($brige = AppKits::bridge())){
                //zl app端提单发送短信
                $content = '【佰仟金融】您已申请了'.$this->_order_obj->_model->loan_amount.'的现金贷款，请于审批通过后完成身份认证，放款更快哦！';
                $smsSend = new SendService();
                $smsSend->send_msn_to_admin($content,Auth::user()->mobile);

                $loan_model->update_loan_by_id(array('source'=>Service::app_source),$this->_order_obj->_model->id);
            }else{
                $loan_model->update_loan_by_id(array('source'=>Service::wei_xin_source),$this->_order_obj->_model->id);
                $response = $this->submitPhoto($res['SerialNo'],$this->_order_obj->_model->sa_id);
                if($response instanceof ResourceError){
                    return $reponse->toArray();
                }
                $photoRes = (new Asapi())->uploadImageFile($response);
                if(AsKits::uploadImageOk($photoRes) === false){
                    Logger::error('-----安硕接口图片上传失败-------','yunwei');
                    return (new CommErrorModel('图片上传失败！'))->toArray();
                }
            }
            return array('status'=>true,'SerialNo'=>$res['SerialNo']);
        }
    }

    public function detail(){
        $data = array();
        $base = AsBaseInformationModel::where($this->_order_obj->_filter)->first();
        $comm = AsCommAddModel::where($this->_order_obj->_filter)->first();
        $custom = AsCustombaseMessageMobel::where($this->_order_obj->_filter)->first();
        $pic = AsCustomPicModel::where($this->_order_obj->_filter)->first();
        $family = AsFamilyMessageModel::where($this->_order_obj->_filter)->first();
        $income = AsIncomeMessageModel::where($this->_order_obj->_filter)->first();
        $Inside = AsInsideMessageModel::where($this->_order_obj->_filter)->first();
        $repayment = AsRepaymentMessage::where($this->_order_obj->_filter)->first();
        $work = AsWordMessageModel::where($this->_order_obj->_filter)->first();
        $data = array_add($data,'base',$base);
        $data = array_add($data,'custom',$custom);
        $data = array_add($data,'pic',$pic);
        $data = array_add($data,'comm',$comm);
        $data = array_add($data,'family',$family);
        $data = array_add($data,'income',$income);
        $data = array_add($data,'inside',$Inside);
        $data = array_add($data,'repayment',$repayment);
        $data = array_add($data,'work',$work);
        return $data;
    }

    /**
     * 判断手机号是否唯一
     * @param $mobile
     * @param $mobileType
     * @return mixed
     */
    public function mobileUnique($mobile,$mobileType){
        $commAdd = AsCommAdd::where($this->_order_obj->_filter)->first();
        $familyMessage = AsFamilyMessage::where($this->_order_obj->_filter)->first();
        $incomeMessage = AsIncomeMessage::where($this->_order_obj->_filter)->first();

        $selfMobile = '';
        $familyMobile = '';
        $otherMobile = '';

        if($commAdd){
            $selfMobile = $commAdd->MobileTelephone;
        }

        if($familyMessage){
            $familyMobile = $commAdd->KinshipTel;
        }

        if($incomeMessage){
            $otherMobile = $commAdd->ContactTel;
        }

        $compareArray = array();
        switch($mobileType){
            case self::MOBILE_TYPE_SELF:
                $compareArray = array($familyMobile,$otherMobile);
                break;
            case self::MOBILE_TYPE_FAMILY:
                $compareArray = array($selfMobile,$otherMobile);
                break;
            case self::MOBILE_TYPE_OTHER:
                $compareArray = array($selfMobile,$familyMobile);
                break;
        }

        if(in_array($mobile,$compareArray)){
            return false;
        }else{
            return true;
        }
    }

    public function check_loan_message_is_complete(){
        $response = $this->submitCheck();
        if($response instanceof ResourceErrorModel){
            return array('status'=>false,'message'=>array('data'=>$response->data()));
        }else {
            unset($response['SPOUSEWORKCORP']);
            unset($response['SPOUSEWORKTEL']);
            unset($response['OtherTelephone']);
            $response = array_merge($response, array('InputUserID' => $this->_order_obj->_model->sa_id, 'Salesexecutive' => $this->_order_obj->_model->sa_id, 'ContactTime' => $this->_order_obj->_model->contact_time));
            $response["PurposeRemark"] = $this->_order_obj->_model->remark_descript;
            foreach($response as $key=>$value){
                if($value == ''){
                    Logger::error($key."字段不能为空");
                    return array('status'=>false,'message'=>array('data'=>$key.'字段为空'));
                }else{
                    continue;
                }
            }
            return array('status'=>true,'message'=>array('data'=>$response));
        }
    }

}