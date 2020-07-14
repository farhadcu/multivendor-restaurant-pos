<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    protected $role_name = [
        ['role' => 'Super Admin'],
        ['role' => 'Admin'],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert($this->role_name);
    }
}
