<?php

use Illuminate\Database\Seeder;
use Modules\Admin\Entities\Module; 

class ModulesTableSeeder extends Seeder
{
    protected $modules = [
        [
            "module_name"     => "Dashboard",
            "module_link"     => "dashboard",
            "module_icon"     => "fas fa-home",
            "module_sequence" => 1000,
            "parent_id"       => "0",
        ],
        [
            "module_name"     => "Manage User",
            "module_link"     => "user",
            "module_icon"     => "fas fa-users",
            "module_sequence" => 18000,
            "parent_id"       => "0",
        ],
        [
            "module_name"     => "Role Permission",
            "module_link"     => "role-permission",
            "module_icon"     => "fas fa-tasks",
            "module_sequence" => 19000,
            "parent_id"       => "0",
        ],
        [
            "module_name"     => "Software Settings",
            "module_link"     => "javascript:void(0);",
            "module_icon"     => "fas fa-cogs",
            "module_sequence" => 20000,
            "parent_id"       => "0",
        ],
        [
            "module_name"     => "Setting",
            "module_link"     => "setting",
            "module_icon"     => "fas fa-tools",
            "module_sequence" => 20001,
            "parent_id"       => "4",
        ],
        
        [
            "module_name"     => "Role",
            "module_link"     => "role",
            "module_icon"     => "fas fa-user-cog",
            "module_sequence" => 20002,
            "parent_id"       => "4",
        ],
        
        [
            "module_name"     => "Module",
            "module_link"     => "module",
            "module_icon"     => "fas fa-align-left",
            "module_sequence" => 20003,
            "parent_id"       => "4",
        ],
        [
            "module_name"     => "Method",
            "module_link"     => "method",
            "module_icon"     => "fas fa-list-ol",
            "module_sequence" => 20004,
            "parent_id"       => "4",
        ],
        
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Module::insert($this->modules);
    }
}
