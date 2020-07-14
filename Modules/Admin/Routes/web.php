<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix'  =>  'admin'], function () {

    Route::get('/', 'LoginController@showLoginForm')->name('admin')->middleware('XSS');
    Route::post('login', 'LoginController@login')->name('admin.login')->middleware('XSS');
    Route::post('logout', 'LoginController@logout')->name('admin.logout')->middleware('XSS');

    Route::group(['middleware' => ['auth:admin','XSS']], function () {
        
        Route::get('dashboard', 'DashboardController@index')->name('admin.dashboard');

        //Setting Related All Routes 
        Route::get('setting', 'SettingController@index')->name('admin.setting');
        Route::post('setting', 'SettingController@update')->name('admin.setting');
        Route::prefix('setting')->group(function () {
            Route::post('mail', 'SettingController@set_mail_info')->name('admin.setting.mail');
            Route::post('sms', 'SettingController@set_sms_info')->name('admin.setting.sms');
        });
        
        //Role Related All Routes
        Route::prefix('role')->group(function () {
            Route::get('/', 'RoleController@index')->name('admin.role');
            Route::post('list', 'RoleController@getList')->name('admin.role.list');
            Route::post('store', 'RoleController@store')->name('admin.role.store');
            Route::post('edit', 'RoleController@edit')->name('admin.role.edit');
            Route::post('update', 'RoleController@update')->name('admin.role.update');
            Route::post('delete', 'RoleController@destroy')->name('admin.role.delete');
            Route::post('bulk-action-delete', 'RoleController@bulk_action_delete')->name('admin.role.bulkaction');
        });

        //Role Permission Related All Routes
        Route::get('role-permission','RolePermissionController@index')->name('admin.role.permission');
        Route::post('role-permission-store','RolePermissionController@store')->name('admin.role.permission.store');
        Route::post('role-permission-get', 'RolePermissionController@get_role_permission')->name('admin.role.permission.get');

        //Module Related All Routes
        Route::prefix('module')->group(function () {
            Route::get('/', 'ModuleController@index')->name('admin.module');
            Route::post('list', 'ModuleController@getList')->name('admin.module.list');
            Route::post('store', 'ModuleController@store')->name('admin.module.store');
            Route::post('parent-module-list', 'ModuleController@parent_module_list')->name('admin.module.parentModuleList');
            Route::post('edit', 'ModuleController@edit')->name('admin.module.edit');
            Route::post('update', 'ModuleController@update')->name('admin.module.update');
            Route::post('delete', 'ModuleController@destroy')->name('admin.module.delete');
            Route::post('bulk-action-delete', 'ModuleController@bulk_action_delete')->name('admin.module.bulkaction');
        });

        //Method Related All Routes
        Route::prefix('method')->group(function () {
            Route::get('/', 'MethodController@index')->name('admin.method');
            Route::post('list', 'MethodController@getList')->name('admin.method.list');
            Route::post('store', 'MethodController@store')->name('admin.method.store');
            Route::post('edit', 'MethodController@edit')->name('admin.method.edit');
            Route::post('update', 'MethodController@update')->name('admin.method.update');
            Route::post('delete', 'MethodController@destroy')->name('admin.method.delete');
            Route::post('bulk-action-delete', 'MethodController@bulk_action_delete')->name('admin.method.bulkaction');
        });

        //Subscription Related All Routes
        Route::prefix('subscription')->group(function () {
            Route::get('/', 'SubscriptionController@index')->name('admin.subscription');
            Route::post('list', 'SubscriptionController@getList')->name('admin.subscription.list');
            Route::post('store', 'SubscriptionController@store')->name('admin.subscription.store');
            Route::post('edit', 'SubscriptionController@edit')->name('admin.subscription.edit');
            Route::post('update', 'SubscriptionController@update')->name('admin.subscription.update');
            Route::post('delete', 'SubscriptionController@destroy')->name('admin.subscription.delete');
            Route::post('bulk-action-delete', 'SubscriptionController@bulk_action_delete')->name('admin.subscription.bulkaction');
        });

        //Subscription Related All Routes
        Route::prefix('company')->group(function () {
            Route::get('/', 'Company\CompanyController@index')->name('admin.company');
            Route::post('list', 'Company\CompanyController@getList')->name('admin.company.list');
            Route::post('store', 'Company\CompanyController@store')->name('admin.company.store');
            Route::post('edit', 'Company\CompanyController@edit')->name('admin.company.edit');
            Route::post('update', 'Company\CompanyController@update')->name('admin.company.update');
            Route::post('delete', 'Company\CompanyController@destroy')->name('admin.company.delete');
            Route::post('bulk-action-delete', 'Company\CompanyController@bulk_action_delete')->name('admin.company.bulkaction');
        });

        //Company Role Related All Routes
        Route::prefix('company/role')->group(function () {
            Route::get('/', 'Company\RoleController@index')->name('admin.company.role');
            Route::post('list', 'Company\RoleController@getList')->name('admin.company.role.list');
            Route::post('store', 'Company\RoleController@store')->name('admin.company.role.store');
            Route::post('edit', 'Company\RoleController@edit')->name('admin.company.role.edit');
            Route::post('update', 'Company\RoleController@update')->name('admin.company.role.update');
            Route::post('delete', 'Company\RoleController@destroy')->name('admin.company.role.delete');
            Route::post('bulk-action-delete', 'Company\RoleController@bulk_action_delete')->name('admin.company.role.bulkaction');
        });

        //Company Module Related All Routes
        Route::prefix('company/module')->group(function () {
            Route::get('/', 'Company\ModuleController@index')->name('admin.company.module');
            Route::post('list', 'Company\ModuleController@getList')->name('admin.company.module.list');
            Route::post('store', 'Company\ModuleController@store')->name('admin.company.module.store');
            Route::post('parent-module-list', 'Company\ModuleController@parent_module_list')->name('admin.company.module.parentModuleList');
            Route::post('edit', 'Company\ModuleController@edit')->name('admin.company.module.edit');
            Route::post('update', 'Company\ModuleController@update')->name('admin.company.module.update');
            Route::post('change-status', 'Company\ModuleController@change_status')->name('admin.company.module.change-status');
            Route::post('delete', 'Company\ModuleController@destroy')->name('admin.company.module.delete');
            Route::post('bulk-action-delete', 'Company\ModuleController@bulk_action_delete')->name('admin.company.module.bulkaction');
        });

        //Company Method Related All Routes
        Route::prefix('company/method')->group(function () {
            Route::get('/', 'Company\MethodController@index')->name('admin.company.method');
            Route::post('list', 'Company\MethodController@getList')->name('admin.company.method.list');
            Route::post('store', 'Company\MethodController@store')->name('admin.company.method.store');
            Route::post('edit', 'Company\MethodController@edit')->name('admin.company.method.edit');
            Route::post('update', 'Company\MethodController@update')->name('admin.company.method.update');
            Route::post('delete', 'Company\MethodController@destroy')->name('admin.company.method.delete');
            Route::post('bulk-action-delete', 'Company\MethodController@bulk_action_delete')->name('admin.company.method.bulkaction');
        });

         //Company Branch Related All Routes
         Route::prefix('company/branch')->group(function () {
            Route::get('/', 'Company\BranchController@index')->name('admin.company.branch');
            Route::post('list', 'Company\BranchController@getList')->name('admin.company.branch.list');
            Route::post('store', 'Company\BranchController@store')->name('admin.company.branch.store');
            Route::post('edit', 'Company\BranchController@edit')->name('admin.company.branch.edit');
            Route::post('update', 'Company\BranchController@update')->name('admin.company.branch.update');
            Route::post('change-status', 'Company\BranchController@change_status')->name('admin.company.branch.change-status');
            Route::post('delete', 'Company\BranchController@destroy')->name('admin.company.branch.delete');
            Route::post('bulk-action-delete', 'Company\BranchController@bulk_action_delete')->name('admin.company.branch.bulkaction');
        });

        //Company User Related All Routes

        Route::prefix('company/user')->group(function () {
            Route::get('/', 'Company\UserController@index')->name('admin.company.user');
            Route::post('list', 'Company\UserController@getList')->name('admin.company.user.list');
            Route::get('create', 'Company\UserController@create')->name('admin.company.user.create');
            Route::post('store', 'Company\UserController@store')->name('admin.company.user.store');
            Route::get('edit/{id}', 'Company\UserController@edit')->name('admin.company.user.edit');
            Route::post('update', 'Company\UserController@update')->name('admin.company.user.update');
            Route::post('view', 'Company\UserController@show')->name('admin.company.user.view');
            Route::post('delete', 'Company\UserController@destroy')->name('admin.company.user.delete');
            Route::post('bulk-action-delete', 'Company\UserController@bulk_action_delete')->name('admin.company.user.bulkaction');
            Route::post('password/update', 'Company\UserController@change_password')->name('admin.company.user.password.update');
            Route::post('change-status', 'Company\UserController@change_status')->name('admin.company.user.changestatus');
            Route::post('get-role-branch', 'Company\UserController@get_role_branch')->name('admin.company.user.get-role-branch');
        
        });

        Route::prefix('unit')->group(function () {
            Route::get('/', 'UnitController@index')->name('admin.unit');
            Route::post('list', 'UnitController@getList')->name('admin.unit.list');
            Route::post('store', 'UnitController@store')->name('admin.unit.store');
            Route::post('edit', 'UnitController@edit')->name('admin.unit.edit');
            Route::post('update', 'UnitController@update')->name('admin.unit.update');
            Route::post('delete', 'UnitController@destroy')->name('admin.unit.delete');
            Route::post('bulk-action-delete', 'UnitController@bulk_action_delete')->name('admin.unit.bulkaction');
        });

    });
});