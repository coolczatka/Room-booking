<?php

use Illuminate\Database\Seeder;
use App\Permission;
use App\Role;


class AuthPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => 'admin-panel-view',
                'display_name' => 'Admin View',
                'description' => 'Panel to see every user\'s reservation'
            ],
            [
                'name' => 'particular-user-view',
                'display_name' => 'Regular User View',
                'description' => 'Panel to see particular user\'s reservation'
            ]
        ];

        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Can see every user\'s activity'
            ],
            [
                'name' => 'regular-user',
                'display_name' => 'User'
            ]
        ];
        foreach($roles as $r){
            Role::create($r);

        }
        foreach($permissions as $p){
            Permission::create($p);
        }
        $p1 = Permission::find(1);
        $p2 = Permission::find(2);
        $role = Role::find(1);
        $role->attachPermission($p1,$p2);

    }
}
