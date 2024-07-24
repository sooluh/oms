<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'login' => 'admin',
                'email' => 'admin@sekolah.ac.id',
                'password' => Hash::make('admin'),
                'name' => 'Admin',
                'created_at' => now(),
            ],
        ]);
    }
}
