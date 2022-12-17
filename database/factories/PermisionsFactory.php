<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Permission;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermisionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
           [ 'guard_name' => 'admin','name' => 'make_payment'],
            ['guard_name' => 'admin','name' => 'onboard-user'],
            ['guard_name' => 'admin','name' => 'bulk-import'],
            ['guard_name' => 'admin','name' => 'bulk-export'],
            ['guard_name' => 'admin','name' => 'suspend-user'],
            ['guard_name' => 'admin','name' => 'activate-user'],
            ['guard_name' => 'admin','name' => 'create_role'],
            ['guard_name' => 'admin','name' => 'assign_permission_role'],
            ['guard_name' => 'admin','name' => 'fetch_role'],
            ['guard_name' => 'admin','name' => 'revoke_permission_role'],
            ['guard_name' => 'admin','name' => 'admin_create_account'],
            ['guard_name' => 'admin','name' => 'fetch_permissions']
        ];
    }
}
