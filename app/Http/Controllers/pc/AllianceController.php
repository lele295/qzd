<?php

namespace App\Http\Controllers\pc;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AllianceController extends Controller
{
   //
    public function getIndex()
    {
        return view('pc.alliance');
    }

}
