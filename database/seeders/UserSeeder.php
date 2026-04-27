<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@canteen.com',
                'password' => Hash::make('password'),
                'role_id' => 1
            ],
            [
                'name' => 'Student',
                'email' => 'student@canteen.com',
                'password' => Hash::make('password'),
                'role_id' => 2
            ],
            [
                'name' => 'Kitchen Staff',
                'email' => 'kitchen@canteen.com',
                'password' => Hash::make('password'),
                'role_id' => 3
            ]
        ]);
    }
}
