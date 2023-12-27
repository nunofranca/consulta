<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersAdmin = [
                'name' => 'Nuno França',
                'email' => 'contato@nunotech.com.br',
                'password' => '123mudar#' ];

        $user = [
            'name' => 'Ordachson Gonçalves',
            'email' => 'ordachson@gmail.com',
            'password' => 'ordachson123mudar#'
        ];
        $userAdmin = User::create($usersAdmin );
        $userAdmin->assignRole(Role::create(['name'=>'super admin']));
        $user = User::create($user);
        $user->assignRole(Role::create(['name'=>'user']));

    }
}
