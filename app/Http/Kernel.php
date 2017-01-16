<?php

namespace App\Http;

use App\Http\Middleware\admin\EnterpriseAuth;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        //\App\Http\Middleware\VerifyCsrfToken::class,
        //\App\Http\Middleware\PrivilegeCheck::class,//用户权限控制中间件
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.mobile'=>\App\Http\Middleware\AuthenticateMobile::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'admin.auth' => \App\Http\Middleware\admin\AdminAuthenticate::class,
        'csrf' => \App\Http\Middleware\VerifyCsrfToken::class,
        'userLoggerRecord' => \App\Http\Middleware\UserLoggerRecord::class,
        'adminLoggerRecord' => \App\Http\Middleware\AdminLoggerRecord::class,
        'loan.car' => \App\Http\Middleware\CarAuthenticate::class,
        'register.mobile' => \App\Http\Middleware\RegisterMobile::class,
        'fqg'=>\App\Http\Middleware\Fqg::class,
        'auth.pc' => \App\Http\Middleware\AuthenticatePc::class,
        'admin.enterprise' => \App\Http\Middleware\agent\EnterpriseAuth::class,
    ];
}
