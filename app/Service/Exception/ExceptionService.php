<?php
namespace App\Service\Exception;

use App\Service\mobile\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Session\TokenMismatchException;

class ExceptionService extends Handler{

    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    static public function exception(\Exception $e){
        if($e instanceof TokenMismatchException){
            return false;
        }
        throw $e;
    }
}