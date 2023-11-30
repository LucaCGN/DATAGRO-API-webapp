<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Markets',
            'email' => 'markets.dev@datagro.com.br',
            'password' => Hash::make('api@2023v0'),
        ]);
    }
}
