<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'name' => 'Admin OMS',
            'email' => 'admin@oms.my.id',
            'password' => 'ohmyschool',
        ]);

        if (config('app.is_demo')) {
            $this->call(DemoServiceSeeder::class);
        }
    }
}
