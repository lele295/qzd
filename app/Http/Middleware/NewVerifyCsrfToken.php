<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/1/18
 * Time: 11:21
 */

namespace App\Http\Middleware;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Support\Facades\Log;

class NewVerifyCsrfToken extends BaseVerifier{
    public function handle($request, Closure $next){
        try{
            parent::handle($request,$next);
        }catch (\Exception $e){
            Log::error('csrftoken超时');
        }
    }
}