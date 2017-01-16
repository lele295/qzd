<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/8
 * Time: 14:12
 */

namespace App\Console\Commands;


use App\Service\datamigrate\AuthmigrateService;
use App\Service\datamigrate\BankService;
use App\Service\datamigrate\LoanService;
use App\Service\datamigrate\UserService;
use Illuminate\Console\Command;

class DataMigrate extends Command
{
    protected $name = 'sync:datamigrate';

    protected $description = '数据迁移';

    public function fire()
    {
          $userService = new UserService();
          $userService->get_user_migrate();
          $loanService = new LoanService();
          $loanService->get_loan_message();
          $bankService = new BankService();
          $bankService->get_bank_message();
          $auth = new AuthmigrateService();
          $auth->get_auth_message();
          $loanService->update_except_loan();

    }
}