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


Route::group(['middleware' => ['auth','XSS']], function () {

    Route::get('select-branch', 'DashboardController@select_branch')->name('select.branch');
    Route::post('branch-store-session', 'DashboardController@branch_store_session')->name('branch.store.session');


    Route::get('dashboard', 'DashboardController@index')->name('dashboard')->middleware('CheckBranch');
    Route::get('pos-invoice', 'DashboardController@invoice')->name('pos.invoice')->middleware('CheckBranch');
    Route::get('setting', 'SettingController@index')->name('setting')->middleware('CheckBranch');
    Route::post('setting-update', 'SettingController@update')->name('setting.update');

     //Role Related All Routes
    Route::prefix('role')->group(function () {
        Route::get('/', 'RoleController@index')->name('role')->middleware('CheckBranch');
        Route::post('list', 'RoleController@getList')->name('role.list');
        Route::post('store', 'RoleController@store')->name('role.store');
        Route::post('edit', 'RoleController@edit')->name('role.edit');
        Route::post('update', 'RoleController@update')->name('role.update');
        Route::post('delete', 'RoleController@destroy')->name('role.delete');
        Route::post('bulk-action-delete', 'RoleController@bulk_action_delete')->name('role.bulkaction');
    });

    //Role Permission Related All Routes
    Route::get('role-permission','CompanyRolePermissionController@index')->name('role.permission');
    Route::post('role-permission-store','CompanyRolePermissionController@store')->name('role.permission.store');
    Route::post('role-permission-get', 'CompanyRolePermissionController@get_role_permission')->name('role.permission.get');

    //User Related All Routes
    Route::prefix('user')->group(function () {
        Route::get('/', 'UserController@index')->name('user');
        Route::post('list', 'UserController@getList')->name('user.list');
        Route::get('create', 'UserController@create')->name('user.create');
        Route::post('store', 'UserController@store')->name('user.store');
        Route::get('edit/{id}', 'UserController@edit')->name('user.edit');
        Route::post('update', 'UserController@update')->name('user.update');
        Route::post('view', 'UserController@show')->name('user.view');
        Route::post('delete', 'UserController@destroy')->name('user.delete');
        Route::post('bulk-action-delete', 'UserController@bulk_action_delete')->name('user.bulkaction');
        Route::post('password/update', 'UserController@change_password')->name('user.password.update');
        Route::post('change-status', 'UserController@change_status')->name('user.changestatus');
    });

    //Supplier Related All Routes
    Route::prefix('supplier')->group(function () {
        Route::get('/', 'SupplierController@index')->name('supplier')->middleware('CheckBranch');
        Route::post('list', 'SupplierController@getList')->name('supplier.list');
        Route::post('store', 'SupplierController@store')->name('supplier.store');
        Route::post('view', 'SupplierController@show')->name('supplier.view');
        Route::post('edit', 'SupplierController@edit')->name('supplier.edit');
        Route::post('update', 'SupplierController@update')->name('supplier.update');
        Route::post('delete', 'SupplierController@destroy')->name('supplier.delete');
        Route::post('bulk-action-delete', 'SupplierController@bulk_action_delete')->name('supplier.bulkaction');
    });

    //Customer Related All Routes
    Route::prefix('customer')->group(function () {
        Route::get('/', 'CustomerController@index')->name('customer')->middleware('CheckBranch');
        Route::post('list', 'CustomerController@getList')->name('customer.list');
        Route::post('list-pos', 'CustomerController@getListForPos')->name('customer.list.pos');
        Route::post('store', 'CustomerController@store')->name('customer.store');
        Route::post('view', 'CustomerController@show')->name('customer.view');
        Route::post('edit', 'CustomerController@edit')->name('customer.edit');
        Route::post('update', 'CustomerController@update')->name('customer.update');
        Route::post('delete', 'CustomerController@destroy')->name('customer.delete');
        Route::post('bulk-action-delete', 'CustomerController@bulk_action_delete')->name('customer.bulkaction');
    });

    //Table Related All Routes
    Route::prefix('table')->group(function () {
        Route::get('/', 'TableController@index')->name('table')->middleware('CheckBranch');
        Route::post('list', 'TableController@getList')->name('table.list');
        Route::post('store', 'TableController@store')->name('table.store');
        Route::post('edit', 'TableController@edit')->name('table.edit');
        Route::post('update', 'TableController@update')->name('table.update');
        Route::post('delete', 'TableController@destroy')->name('table.delete');
        Route::post('bulk-action-delete', 'TableController@bulk_action_delete')->name('table.bulkaction');
    });

    //Category Related All Routes
    Route::prefix('category')->group(function () {
        Route::get('/', 'Product\CategoryController@index')->name('category')->middleware('CheckBranch');
        Route::post('list', 'Product\CategoryController@getList')->name('category.list');
        Route::post('store', 'Product\CategoryController@store')->name('category.store');
        Route::post('view', 'Product\CategoryController@show')->name('category.view');
        Route::post('edit', 'Product\CategoryController@edit')->name('category.edit');
        Route::post('update', 'Product\CategoryController@update')->name('category.update');
        Route::post('change-status', 'Product\CategoryController@change_status')->name('category.change.status');
        Route::post('delete', 'Product\CategoryController@destroy')->name('category.delete');
        Route::post('bulk-action-delete', 'Product\CategoryController@bulk_action_delete')->name('category.bulkaction');
        Route::post('category-list', 'Product\CategoryController@category_list')->name('category.category.list');
    });

    //Product Related All Routes
    Route::prefix('product')->group(function () {
        Route::get('/', 'Product\ProductController@index')->name('product')->middleware('CheckBranch');
        Route::post('list', 'Product\ProductController@getList')->name('product.list');
        Route::post('store', 'Product\ProductController@store')->name('product.store');
        Route::post('view', 'Product\ProductController@show')->name('product.view');
        Route::post('edit', 'Product\ProductController@edit')->name('product.edit');
        Route::post('update', 'Product\ProductController@update')->name('product.update');
        Route::post('change-status', 'Product\ProductController@change_status')->name('product.change.status');
        Route::post('delete', 'Product\ProductController@destroy')->name('product.delete');
        Route::post('bulk-action-delete', 'Product\ProductController@bulk_action_delete')->name('product.bulkaction');
        Route::get('autocomplete-search-product', 'Product\ProductController@autocomplete_search_product')->name('autocomplete.search.product');
        Route::post('variation-product', 'Product\ProductController@variation_product')->name('variation.product');
        Route::post('generate-barcode', 'Product\ProductController@generate_barcode')->name('generate.barcode');
    });
    
    Route::get('pos', 'OrderController@create')->name('pos')->middleware('CheckBranch');
    Route::post('pos-product', 'OrderController@productList')->name('pos.product')->middleware('CheckBranch');
    Route::prefix('sale')->group(function () {
        Route::post('store-cart', 'OrderCartController@store')->name('sale.store.cart');
        Route::post('update-cart', 'OrderCartController@update')->name('sale.update.cart');
        Route::post('delete-cart', 'OrderCartController@destroy')->name('sale.delete.cart');
        Route::post('delete-item', 'OrderCartController@removeItem')->name('sale.delete.item');

        Route::get('/', 'OrderController@index')->name('sale')->middleware('CheckBranch');
        Route::post('list', 'OrderController@getList')->name('sale.list');
        Route::post('edit', 'OrderController@edit')->name('sale.edit');
        Route::post('delete', 'OrderController@destroy')->name('sale.delete');
        Route::post('change-status', 'OrderController@change_status')->name('sale.change.status');
        Route::post('store', 'OrderController@store')->name('sale.store');
        Route::get('sale-{type}/{id}', 'OrderController@invoice')->name('sale.invoice')->middleware('CheckBranch');
        Route::post('bulk-action-delete', 'OrderController@bulk_action_delete')->name('sale.bulkaction');
    });

    //Account Type Related All Routes
    Route::prefix('account-type')->group(function () {
        Route::get('/', 'Accounts\AccountTypeController@index')->name('account.type')->middleware('CheckBranch');
        Route::post('list', 'Accounts\AccountTypeController@getList')->name('account.type.list');
        Route::post('store', 'Accounts\AccountTypeController@store')->name('account.type.store');
        Route::post('view', 'Accounts\AccountTypeController@show')->name('account.type.view');
        Route::post('edit', 'Accounts\AccountTypeController@edit')->name('account.type.edit');
        Route::post('update', 'Accounts\AccountTypeController@update')->name('account.type.update');
        Route::post('delete', 'Accounts\AccountTypeController@destroy')->name('account.type.delete');
        Route::post('bulk-action-delete', 'Accounts\AccountTypeController@bulk_action_delete')->name('account.type.bulkaction');
    });

    //Chart Of Account Related All Routes
    Route::prefix('chart-of-accounts')->group(function () {
        Route::get('/', 'Accounts\ChartOfAccountsController@index')->name('account.head')->middleware('CheckBranch');
        Route::post('list', 'Accounts\ChartOfAccountsController@getList')->name('account.head.list');
        Route::post('store', 'Accounts\ChartOfAccountsController@store')->name('account.head.store');
        Route::post('view', 'Accounts\ChartOfAccountsController@show')->name('account.head.view');
        Route::post('edit', 'Accounts\ChartOfAccountsController@edit')->name('account.head.edit');
        Route::post('update', 'Accounts\ChartOfAccountsController@update')->name('account.head.update');
        Route::post('delete', 'Accounts\ChartOfAccountsController@destroy')->name('account.head.delete');
        Route::post('bulk-action-delete', 'Accounts\ChartOfAccountsController@bulk_action_delete')->name('account.head.bulkaction');
    });

    //Account Category Related All Routes
    Route::prefix('account-category')->group(function () {
        Route::get('{category}', 'Accounts\TransactionCategoryController@index')->middleware('CheckBranch');
        Route::post('list', 'Accounts\TransactionCategoryController@getList')->name('account.category.list');
        Route::post('store', 'Accounts\TransactionCategoryController@store')->name('account.category.store');
        Route::post('view', 'Accounts\TransactionCategoryController@show')->name('account.category.view');
        Route::post('edit', 'Accounts\TransactionCategoryController@edit')->name('account.category.edit');
        Route::post('update', 'Accounts\TransactionCategoryController@update')->name('account.category.update');
        Route::post('delete', 'Accounts\TransactionCategoryController@destroy')->name('account.category.delete');
        Route::post('bulk-action-delete', 'Accounts\TransactionCategoryController@bulk_action_delete')->name('account.category.bulkaction');
    });

    //Transaction Related All Routes
    Route::prefix('transaction')->group(function () {
        Route::get('/', 'Accounts\TransactionController@index')->middleware('CheckBranch');
        Route::post('list', 'Accounts\TransactionController@getList')->name('transaction.list');
        Route::post('store', 'Accounts\TransactionController@store')->name('transaction.store');
        Route::post('view', 'Accounts\TransactionController@show')->name('transaction.view');
        Route::post('edit', 'Accounts\TransactionController@edit')->name('transaction.edit');
        Route::post('update', 'Accounts\TransactionController@update')->name('transaction.update');
        Route::post('delete', 'Accounts\TransactionController@destroy')->name('transaction.delete');
        Route::post('bulk-action-delete', 'Accounts\TransactionController@bulk_action_delete')->name('transaction.bulkaction');
        Route::post('transaction-category-list', 'Accounts\TransactionController@category_list')->name('transaction.category.list');
    });

    //Purchase Related All Routes
    Route::prefix('purchase')->group(function () {
        Route::get('/', 'PurchaseController@index')->middleware('CheckBranch');
        Route::post('list', 'PurchaseController@getList')->name('purchase.list');
        Route::get('add', 'PurchaseController@create')->name('purchase.add');
        Route::post('store', 'PurchaseController@store')->name('purchase.store');
        Route::get('view/{id}', 'PurchaseController@show')->name('purchase.view');
        Route::get('edit/{id}', 'PurchaseController@edit')->name('purchase.edit');
        Route::post('update', 'PurchaseController@update')->name('purchase.update');
        Route::post('delete', 'PurchaseController@destroy')->name('purchase.delete');
        Route::post('bulk-action-delete', 'PurchaseController@bulk_action_delete')->name('purchase.bulkaction');
        Route::post('payment-list', 'PurchaseController@payment_list')->name('purchase.payment.list');
        Route::post('payment-add', 'PurchaseController@add_payment')->name('purchase.payment.add');
        Route::post('payment-edit', 'PurchaseController@edit_payment')->name('purchase.payment.edit');
        Route::post('payment-update', 'PurchaseController@update_payment')->name('purchase.payment.update');
        Route::post('payment-delete', 'PurchaseController@delete_payment')->name('purchase.payment.delete');
    });

    Route::get('purchase-cart-content','PurchaseCartController@index');
    Route::post('purchase-cart-product-add','PurchaseCartController@store');
    Route::post('purchase-cart-product-update','PurchaseCartController@update');
    Route::post('purchase-cart-product-remove','PurchaseCartController@destroy');
    Route::post('purchase-cart-clear','PurchaseCartController@clear');
});
