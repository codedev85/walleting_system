<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['guard_name' => 'admin', 'name' => 'Administrator']);

        Permission::create(['guard_name' => 'admin','name' => 'make_payment']);
        Permission::create(['guard_name' => 'admin','name' => 'onboard-user']);
        Permission::create(['guard_name' => 'admin','name' => 'bulk-import']);
        Permission::create(['guard_name' => 'admin','name' => 'bulk-export']);
        Permission::create(['guard_name' => 'admin','name' => 'suspend-user']);
        Permission::create(['guard_name' => 'admin','name' => 'activate-user']);
        Permission::create(['guard_name' => 'admin','name' => 'create_role']);
        Permission::create(['guard_name' => 'admin','name' => 'assign_permission_role']);
        Permission::create(['guard_name' => 'admin','name' => 'fetch_role']);
        Permission::create(['guard_name' => 'admin','name' => 'revoke_permission_role']);
        Permission::create(['guard_name' => 'admin','name' => 'admin_create_account']);
        Permission::create(['guard_name' => 'admin','name' => 'fetch_permissions']);
        
       $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();
       $permissions =  Permission::where(['guard_name' => 'admin'])->get();
       foreach( $permissions as $permission){
           $role->givePermissionTo($permission);
       }

    }
}
