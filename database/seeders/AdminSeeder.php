<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'name'             => config('admin.name'),
            'email'            => config('admin.email'),
            'password'         => Hash::make(config('admin.password')),
            'email_verified_at' => now(),
        ]);
    }
}
