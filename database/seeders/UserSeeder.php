<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $createMultipleUsers = [
            [
                'first_name'=>'Admin',
                'last_name' => 'admin',
                'username'  =>'admin',
                'email' => 'admin@gmail.com',
                'mobile_number' => '9727883495',
                'password' => Hash::make('123456'),
                'profilepic' => '',
                'role_id' => '1',
                'status' => '1',
                'remember_token'=>'',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'first_name'=>'Admin',
                'last_name' => 'admin',
                'username'  =>'admin',
                'email' => 'admin@gmail.com',
                'mobile_number' => '9727883495',
                'password' => Hash::make('123456'),
                'profilepic' => '',
                'role_id' => '1',
                'status' => '1',
                'remember_token'=>'',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        DB::table('users')->insert($createMultipleUsers);
    }
}
