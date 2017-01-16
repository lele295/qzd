<?php
namespace App\Service\SysInterface;
use App\Model\Base\LoanModel;
use App\Service\mobile\Service;

class CaService extends Service
{
    public function __construct(){

    }

    public function get_data_to_ca_interface($loan_id){
        $loanModel = new LoanModel();
        $info = $loanModel->get_loan_by_id($loan_id);
        return $info;
    }

    public function gte_loan_id_to_ca_interface($constuctno){
        $loanModel = new LoanModel();
        $info = $loanModel->get_loan_by_pact_number($constuctno);
        return $info;
    }

    public function get_loan_id_list_to_ca_interface($constructno){
        $loanModel = new LoanModel();
        $info = $loanModel->get_loan_list_by_pact_number($constructno);
        return $info;
    }
}