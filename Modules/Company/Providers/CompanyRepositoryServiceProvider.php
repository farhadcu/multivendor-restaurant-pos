<?php

namespace Modules\Company\Providers;

use Modules\Company\Contracts\RoleContract;
use Modules\Company\Contracts\CompanyRolePermissionContract;
use Modules\Company\Contracts\UserContract;
use Modules\Company\Contracts\SupplierContract;
use Modules\Company\Contracts\CustomerContract;
use Modules\Company\Contracts\TableContract;
use Modules\Company\Contracts\OrderContract;
use Modules\Company\Contracts\PurchaseContract;
use Modules\Company\Contracts\Product\CategoryContract;
use Modules\Company\Contracts\Product\ProductContract;
use Modules\Company\Contracts\Accounts\AccountTypeContract;
use Modules\Company\Contracts\Accounts\ChartOfAccountContract;
use Modules\Company\Contracts\Accounts\TransactionCategoryContract;
use Modules\Company\Contracts\Accounts\TransactionContract;
use Modules\Company\Contracts\Stock\StockInContract;
use Modules\Company\Contracts\Stock\StockOutContract;
use Illuminate\Support\ServiceProvider;
use Modules\Company\Repositories\RoleRepository;
use Modules\Company\Repositories\CompanyRolePermissionRepository;
use Modules\Company\Repositories\UserRepository;
use Modules\Company\Repositories\SupplierRepository;
use Modules\Company\Repositories\CustomerRepository;
use Modules\Company\Repositories\TableRepository;
use Modules\Company\Repositories\OrderRepository;
use Modules\Company\Repositories\PurchaseRepository;
use Modules\Company\Repositories\Product\CategoryRepository;
use Modules\Company\Repositories\Product\ProductRepository;
use Modules\Company\Repositories\Accounts\AccountTypeRepository;
use Modules\Company\Repositories\Accounts\ChartOfAccountRepository;
use Modules\Company\Repositories\Accounts\TransactionCategoryRepository;
use Modules\Company\Repositories\Accounts\TransactionRepository;
use Modules\Company\Repositories\Stock\StockInRepository;
use Modules\Company\Repositories\Stock\StockOutRepository;

class CompanyRepositoryServiceProvider extends ServiceProvider
{
    protected $repositories = [
        RoleContract::class                  => RoleRepository::class,
        CompanyRolePermissionContract::class => CompanyRolePermissionRepository::class,
        UserContract::class                  => UserRepository::class,
        SupplierContract::class              => SupplierRepository::class,
        CategoryContract::class              => CategoryRepository::class,
        ProductContract::class               => ProductRepository::class,
        AccountTypeContract::class           => AccountTypeRepository::class,
        OrderContract::class                 => OrderRepository::class,
        PurchaseContract::class              => PurchaseRepository::class,
        CustomerContract::class              => CustomerRepository::class,
        TableContract::class                 => TableRepository::class,
        ChartOfAccountContract::class        => ChartOfAccountRepository::class,
        TransactionCategoryContract::class   => TransactionCategoryRepository::class,
        TransactionContract::class           => TransactionRepository::class,
        StockInContract::class               => StockInRepository::class,
        StockOutContract::class              => StockOutRepository::class,
        
    ];
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->repositories as $interface => $implementation)
        {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
