<?php
/**
 * Created by PhpStorm.
 * User: Karol
 * Date: 23.02.2019
 * Time: 10:55
 */
use Illuminate\Database\Seeder;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $admins = [
            [
                'name' => 'adm-qwerty',
                'email' => 'qwerty@example.com',
                'password' => Hash::make('pass123'),
            ],
            [
                'name' => 'typo',
                'email' => 'typo123@exp.com',
                'password' => Hash::make('porobiony')
            ]

        ];
        $users = [
            [
                'name' => 'adm-qwerty',
                'email' => 'qwerty123@example.com',
                'password' => Hash::make('pass123'),
            ],
            [
                'name' => 'nobody',
                'email' => 'nobody@example.pl',
                'password' => Hash::make('dupa'),
            ]

        ];
        foreach($admins as $admin){
            $role = Role::where('name','admin')->first();
            $u = User::create($admin);
            $u->attachRole($role);
        }
        foreach($users as $user){
            $role = Role::where('name','regular-user')->first();
            $u = User::create($user);
            $u->attachRole($role);
        }
    }
}