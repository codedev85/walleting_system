<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::create([
                            'name'             => config('admin.name'),
                            'email'            => config('admin.email'),
                            'password'         => Hash::make(config('admin.password')),
                            'email_verified_at' => now(),
                        ]);
        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $admin->assignRole($role);

        echo "super admin password =====================>>>> " .' Email: '. config('admin.email').' Password: '. config('admin.password') . "\n";
    }
}
