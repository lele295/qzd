<?php
namespace App\Service\admin;
use App\Model\Admin\LoanAdminModel;
use App\Model\Admin\UserAdminModel;

class SearchService extends Service{

    public function get_loan_index_search($condition,$type=''){
        $loanAdminModel = new LoanAdminModel();
        $info = $loanAdminModel->get_search_loan_list($condition,$type);
        return $info;
    }

    public function get_sale_loan_condition($condition,$admin_id){
        $loanAdminModel = new LoanAdminModel();
        $info = $loanAdminModel->get_sale_search_loan_list($condition,$admin_id);
        return $info;
    }

    public function get_user_index_search($condition,$type =''){
        $userAdminModel = new UserAdminModel();
        $info = $userAdminModel->get_search_user_list($condition,$type);
        return $info;
    }
}