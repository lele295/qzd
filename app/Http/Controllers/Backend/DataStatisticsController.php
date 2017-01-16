<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Redirect;

/**
 * Description of MainController
 *
 * @author lenovo
 */
class DataStatisticsController extends BaseController {
    public function getIndex() {
        return view('backend.statistics.index');
    }

}
