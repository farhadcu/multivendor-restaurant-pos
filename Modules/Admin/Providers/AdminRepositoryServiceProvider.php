<?php

namespace Modules\Admin\Providers;

use Modules\Admin\Contracts\RoleContract;
use Modules\Admin\Contracts\MethodContract;
use Modules\Admin\Contracts\ModuleContract;
use Modules\Admin\Contracts\RolePermissionContract;
use Modules\Admin\Contracts\SubscriptionContract;
use Modules\Admin\Contracts\UnitContract;
use Modules\Admin\Contracts\Company\CompanyContract;
use Modules\Admin\Contracts\Company\RoleContract AS CompanyRoleContract;
use Modules\Admin\Contracts\Company\ModuleContract AS CompanyModuleContract;
use Modules\Admin\Contracts\Company\MethodContract AS CompanyMethodContract;
use Modules\Admin\Contracts\Company\BranchContract;
use Modules\Admin\Contracts\Company\UserContract;

use Illuminate\Support\ServiceProvider;

use Modules\Admin\Repositories\RoleRepository;
use Modules\Admin\Repositories\MethodRepository;
use Modules\Admin\Repositories\ModuleRepository;
use Modules\Admin\Repositories\RolePermissionRepository;
use Modules\Admin\Repositories\SubscriptionRepository;
use Modules\Admin\Repositories\UnitRepository;
use Modules\Admin\Repositories\Company\CompanyRepository;
use Modules\Admin\Repositories\Company\RoleRepository AS CompanyRoleRepository;
use Modules\Admin\Repositories\Company\ModuleRepository AS CompanyModuleRepository;
use Modules\Admin\Repositories\Company\MethodRepository AS CompanyMethodRepository;
use Modules\Admin\Repositories\Company\BranchRepository;
use Modules\Admin\Repositories\Company\UserRepository;

class AdminRepositoryServiceProvider extends ServiceProvider
{
    protected $repositories = [
        RoleContract::class               => RoleRepository::class,
        ModuleContract::class             => ModuleRepository::class,
        MethodContract::class             => MethodRepository::class,
        RolePermissionContract::class     => RolePermissionRepository::class,
        SubscriptionContract::class       => SubscriptionRepository::class,
        CompanyContract::class            => CompanyRepository::class,
        CompanyRoleContract::class        => CompanyRoleRepository::class,
        CompanyModuleContract::class      => CompanyModuleRepository::class,
        CompanyMethodContract::class      => CompanyMethodRepository::class,
        BranchContract::class             => BranchRepository::class,
        UserContract::class               => UserRepository::class,
        UnitContract::class               => UnitRepository::class,
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
