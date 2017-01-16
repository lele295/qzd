<?php
Route::get('/', 'pc\IndexController@index');
Route::get('imagestorage/{one?}/{two?}/{three?}/{four?}/{five?}/{six?}/{seven?}/{eight?}/{nine?}', function () {
    \App\Util\ImageRoute::imageStorageRoute();
});

// 微信页面路由配置
Route::group([
    'prefix' => 'wx'
], function () {
    Route::controller('loan', 'wx\LoanController');
    Route::controller('wechat', 'wx\WechatController');
    Route::controller('order', 'wx\OrderController');
    Route::controller('conlist', 'wx\ConlistController');
});

// PC页面路由配置
Route::group([
    'prefix' => 'pc'
], function () {
    Route::controller('company', 'pc\CompanyController');
    Route::controller('pro', 'pc\ProController');
    Route::controller('alliance', 'pc\AllianceController');
});

// 商户二维码生成页面
Route::group([
    'prefix' => 'admin'
], function () {
    Route::controller('qrcode', 'admin\QrcodeController');
});

// 后台路由
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend'
], function () {
    Route::controller('main', 'MainController');
    Route::controller('login', 'LoginController');
    Route::controller('inquiry-management', 'InquiryManagementController');
    Route::controller('order', 'OrderManagementController');
    Route::controller('statistics', 'StatisticsController');
    
});
Route::resource('privilege','Backend\PrivilegeController');//后台权限控制器路由
Route::resource('role','Backend\RoleController');//后台角色控制器路由
Route::resource('user','Backend\UserController');//后台添加管理员路由
Route::post('user/list','Backend\UserController@search');//管理员查询路由
Route::post('user/update','Backend\UserController@update');//管理员更新
Route::resource('log','Backend\LogController');//后台日志路由
Route::get('test','TestController@index');//接口测试路由

Route::controller('sign', 'wx\SignController');
Route::controller('sendtpl', 'wx\ContractController');

// 安硕接口
Route::group([
    'prefix' => 'api'
], function () {
    Route::controller('asapi', 'api\AsapiController');
});

//公用接口
Route::group([
    'prefix' => 'common'
], function () {
    Route::controller('city', 'Common\CityController');
});


