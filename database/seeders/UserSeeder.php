<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Nuno França',
                'email' => 'contato@nunotech.com.br',
                'password' => '123mudar#'
            ],

            [
                'name' => 'Ordachson Gonçalves',
                'email' => 'ordachson@gmail.com',
                'password' => 'ordachson123mudar#'
            ]
        ];

        collect($users)->map(fn($user) => User::create($user));
    }
}
