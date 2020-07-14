<?php

use Illuminate\Database\Seeder;
use Modules\Admin\Entities\Method;

class MethodsTableSeeder extends Seeder
{
    protected $methods = [
        [
            "module_id"   => 1,
            "method_name" => "Dashboard Manage",
            "method_slug" => "dashboard-manage",
        ],
        [
            "module_id"   => 1,
            "method_name" => "Dashboard Add",
            "method_slug" => "dashboard-add",
        ],
        [
            "module_id"   => 1,
            "method_name" => "Dashboard Edit",
            "method_slug" => "dashboard-edit",
        ],
        [
            "module_id"   => 1,
            "method_name" => "Dashboard View",
            "method_slug" => "dashboard-view",
        ],
        [
            "module_id"   => 1,
            "method_name" => "Dashboard Report",
            "method_slug" => "dashboard-report",
        ],

        [
            "module_id"   => 5,
            "method_name" => "Setting General",
            "method_slug" => "setting-general",
        ],
        [
            "module_id"   => 5,
            "method_name" => "Setting SMTP",
            "method_slug" => "setting-smtp",
        ],
        [
            "module_id"   => 5,
            "method_name" => "Setting SMS",
            "method_slug" => "setting-sms",
        ],
        [
            "module_id"   => 5,
            "method_name" => "Setting API",
            "method_slug" => "setting-api",
        ],
        [
            "module_id"   => 3,
            "method_name" => "Role Permission",
            "method_slug" => "role-permission",
        ],
        //Role
        [
            "module_id"   => 6,
            "method_name" => "Role Manage",
            "method_slug" => "role-manage",
        ],
        [
            "module_id"   => 6,
            "method_name" => "Role Add",
            "method_slug" => "role-add",
        ],
        [
            "module_id"   => 6,
            "method_name" => "Role Edit",
            "method_slug" => "role-edit",
        ],
        [
            "module_id"   => 6,
            "method_name" => "Role View",
            "method_slug" => "role-view",
        ],
        [
            "module_id"   => 6,
            "method_name" => "Role Delete",
            "method_slug" => "role-delete",
        ],
        [
            "module_id"   => 6,
            "method_name" => "Role Bulk Action Delete",
            "method_slug" => "role-bulk-action-delete",
        ],
        [
            "module_id"   => 6,
            "method_name" => "Role Report",
            "method_slug" => "role-report",
        ],
        //Module
        [
            "module_id"   => 7,
            "method_name" => "Module Manage",
            "method_slug" => "module-manage",
        ],
        [
            "module_id"   => 7,
            "method_name" => "Module Add",
            "method_slug" => "module-add",
        ],
        [
            "module_id"   => 7,
            "method_name" => "Module Edit",
            "method_slug" => "module-edit",
        ],
        [
            "module_id"   => 7,
            "method_name" => "Module View",
            "method_slug" => "module-view",
        ],
        [
            "module_id"   => 7,
            "method_name" => "Module Delete",
            "method_slug" => "module-delete",
        ],
        [
            "module_id"   => 7,
            "method_name" => "Module Bulk Action Delete",
            "method_slug" => "module-bulk-action-delete",
        ],
        [
            "module_id"   => 7,
            "method_name" => "Module Report",
            "method_slug" => "module-report",
        ],
        //Method
        [
            "module_id"   => 8,
            "method_name" => "Method Manage",
            "method_slug" => "method-manage",
        ],
        [
            "module_id"   => 8,
            "method_name" => "Method Add",
            "method_slug" => "method-add",
        ],
        [
            "module_id"   => 8,
            "method_name" => "Method Edit",
            "method_slug" => "method-edit",
        ],
        [
            "module_id"   => 8,
            "method_name" => "Method View",
            "method_slug" => "method-view",
        ],
        [
            "module_id"   => 8,
            "method_name" => "Method Delete",
            "method_slug" => "method-delete",
        ],
        [
            "module_id"   => 8,
            "method_name" => "Method Bulk Action Delete",
            "method_slug" => "method-bulk-action-delete",
        ],
        [
            "module_id"   => 8,
            "method_name" => "Method Report",
            "method_slug" => "method-report",
        ],
        //User
        [
            "module_id"   => 2,
            "method_name" => "User Manage",
            "method_slug" => "user-manage",
        ],
        [
            "module_id"   => 2,
            "method_name" => "User Add",
            "method_slug" => "user-add",
        ],
        [
            "module_id"   => 2,
            "method_name" => "User Edit",
            "method_slug" => "user-edit",
        ],
        [
            "module_id"   => 2,
            "method_name" => "User View",
            "method_slug" => "user-view",
        ],
        [
            "module_id"   => 2,
            "method_name" => "User Delete",
            "method_slug" => "user-delete",
        ],
        [
            "module_id"   => 2,
            "method_name" => "User Bulk Action Delete",
            "method_slug" => "user-bulk-action-delete",
        ],
        [
            "module_id"   => 2,
            "method_name" => "User Report",
            "method_slug" => "user-report",
        ],
        [
            "module_id"   => 2,
            "method_name" => "User Password Change",
            "method_slug" => "user-password-change",
        ],
        [
            "module_id"   => 2,
            "method_name" => "User Status Change",
            "method_slug" => "user-status-change",
        ],
        [
            "module_id"   => 2,
            "method_name" => "User Permission",
            "method_slug" => "user-permission",
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Method::insert($this->methods);
    }
}
