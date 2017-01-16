<?php

namespace App\Http\Controllers\pc;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
   //
    public function getIndex()
    {
        return view('pc.companyIntro');
    }

}
