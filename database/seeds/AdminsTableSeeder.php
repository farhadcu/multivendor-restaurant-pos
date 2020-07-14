<?php

use Illuminate\Database\Seeder;
use Modules\Admin\Entities\Admin;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            [
                'role_id'           => 1,
                'name'              => 'Mohammad Arman',
                'email'             => 'arman@admin.com',
                'mobile'            => '01521225987',
                'gender'            => 1,
                'password'          => Hash::make('Admin@100%'),
            ],
            [
                'role_id'           => 1,
                'name'              => 'Rifat Hasan',
                'email'             => 'rifat@admin.com',
                'mobile'            => '01521486302',
                'gender'            => 1,
                'password'          => Hash::make('Admin@100%'),
            ]
        );
        Admin::insert($users);
    }
}
