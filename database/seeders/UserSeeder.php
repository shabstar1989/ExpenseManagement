<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  

    public function run()
{
    DB::table('users')->insert([
        [
            'name' => 'Shahab',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'national_code' => '1234567890',
            'phone' => '09123456789',
            'role' => 'approver',
        ],
        [
            'name' => 'Reza',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'national_code' => '0987654321',
            'phone' => '09198765432',
            'role' => 'user',
        ],
    ]);
}
}
