<?php
namespace App\Util;

class IntersetRate{

    private $month_rate = ''; //月利率
    private $peroid = ''; //期数
    private $amount = '';//货款本金
    private $cust_rate = ''; //客户服务费率
    private $man_rate = '';//财务管理费率
    private $month_pay  = '';  //月还款额
    private $baixoan_pay = ''; //保险
    private $origin_pay = '';//除了保险后应还

    public function __construct($business,$flag = true){
        $this->month_rate = $business->MONTHLYINTERESTRATE/100;
        $this->peroid = $business->TERM;
        $this->amount = $business->LOWPRINCIPAL;
        $this->cust_rate = $business->CUSTOMERSERVICERATES/100;
        $this->man_rate = $business->MANAGEMENTFEESRATE/100;
        if($flag == true){
            $this->baixoan_pay = $this->amount*7/1000;
        }
    }
    public function get_month_pay_construct(){
        $fenzi = $this->amount*$this->month_rate*pow((1+$this->month_rate),$this->peroid);
        $fenmu = pow(($this->month_rate+1),$this->peroid)-1;
        $this->origin_pay = $fenzi/$fenmu+$this->amount*($this->cust_rate+$this->man_rate);
        $this->month_pay = $this->origin_pay + $this->baixoan_pay;
    }
    //获取月还款额
    public function get_month_pay()
    {
        $this->get_month_pay_construct();
        return array('month_pay'=>round($this->month_pay,2),'origin_pay'=>$this->origin_pay);
    }





}