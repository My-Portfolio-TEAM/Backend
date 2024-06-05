<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Muhamad Rizki Aditya',
            'email' => 'muhamadrizkiaditya32@gmail.com',
            'password' => bcrypt('12345'),
        ]);
    }
}
