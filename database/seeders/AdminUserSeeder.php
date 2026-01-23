<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Haku',
            'email' => 'haku@gmail.com',
            'password' => Hash::make('123456'),
            'phone' => '5218673160224',
            'role' => 'admin',
            'newsletter' => false,
        ]);

        User::create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@demo.com',
            'password' => Hash::make('123456'),
            'phone' => '888888888',
            'role' => 'cliente',
            'newsletter' => true,
        ]);
    }
}