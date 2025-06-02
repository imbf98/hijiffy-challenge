<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Hijiffy Challenge',
            'email' => 'hijiffy.challenge@test.com',
            'password' => Hash::make('d8r^0vw&N7u1')
        ]);
    }
}
