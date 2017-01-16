<?php

namespace App\Http\Controllers\pc;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProController extends Controller
{
   //
    public function getIndex()
    {
        return view('pc.proIntro');
    }

}
