<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_check_if_admin_can_login()
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $user = [
            'email' =>  config('admin.email'),
            'password'=> config('admin.password')
        ];
        $response =  $this->postJson(route('authenticate_admin'),$user)->assertStatus(201)->json();
        $this->assertArrayHasKey('token', $response['data']);
    }

    public function test_check_admin_can_create_user(){

         $this->withExceptionHandling();
         $this->seed(PermissionSeeder::class);
         $this->seed(AdminSeeder::class);
         $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

         $admin =  Admin::factory()->make();



         $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

         $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

         foreach($permissions as $permission){
            $role->givePermissionTo($permission);
         }

         $admin->assignRole($role);
         $admins = Admin::first();

        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

         $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);


        Sanctum::actingAs($admins);

        $user = [
            'name' => 'Jon',
            'email' => 'jon@gmail.co',
            'phone_number' => '08109302177',
            'password'=> 'password',
            'password_confirmation'=>'password'
        ];

        $response =  $this->postJson(route('onboard-user'),$user)->assertStatus(201)->json();

        $this->assertEquals($user['email'], $response['data']['user']['email']);

    }

    public function test_check_if_admin_can_create_role(){
        $this->withExceptionHandling();
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

        $admin =  Admin::factory()->make();

        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

        foreach($permissions as $permission){
            $role->givePermissionTo($permission);
        }

        $admin->assignRole($role);
        $admins = Admin::first();
        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

        $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);
        Sanctum::actingAs($admins);

        $role = [
            'guard_name' => 'admin',
            'name' => 'Customer Service',
        ];
        $response =  $this->postJson(route('create_role'),$role)->assertStatus(201)->json();

        $this->assertEquals($role['name'], $response['data']['role']['name']);
    }


    public function test_check_if_admin_can_fetch_roles(){
        $this->withExceptionHandling();
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

        $admin =  Admin::factory()->make();

        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

        foreach($permissions as $permission){
            $role->givePermissionTo($permission);
        }

        $admin->assignRole($role);
        $admins = Admin::first();
        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

        $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);
        Sanctum::actingAs($admins);

        $response =  $this->getJson(route('fetch_role'))->assertStatus(201)->json();

        $this->assertGreaterThan(0,count($response['data']['roles']));
    }


    public function test_check_if_admin_can_fetch_permissions(){
        $this->withExceptionHandling();
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

        $admin =  Admin::factory()->make();

        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

        foreach($permissions as $permission){
            $role->givePermissionTo($permission);
        }

        $admin->assignRole($role);
        $admins = Admin::first();
        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

        $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);
        Sanctum::actingAs($admins);

        $response =  $this->getJson(route('fetch_permissions'))->assertStatus(201)->json();

        $this->assertGreaterThan(0,count($response['data']['permissions']));
    }


    public function test_check_if_admin_can_suspend_user(){
        $this->withExceptionHandling();
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

        $admin =  Admin::factory()->make();

        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

        foreach($permissions as $permission){
            $role->givePermissionTo($permission);
        }

        $admin->assignRole($role);
        $admins = Admin::first();
        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

        $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);
        Sanctum::actingAs($admins);

        $user = [
            'name' => 'Jon',
            'email' => 'jon@gmail.co',
            'phone_number' => '08109302177',
            'password'=> 'password',
            'password_confirmation'=>'password'
        ];

        $response =  $this->postJson(route('onboard-user'),$user)->assertStatus(201)->json();

        $suspend = [
            'isBanned' => "true"
        ];

        $response =  $this->getJson(route('suspend-user',[$response['data']['user']['id']]), $suspend)->assertStatus(201)->json();

        $this->assertEquals($suspend['isBanned'],$response['data']['user']['isBanned']);
    }


    public function test_check_if_admin_can_activate_user(){
        $this->withExceptionHandling();
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

        $admin =  Admin::factory()->make();

        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

        foreach($permissions as $permission){
            $role->givePermissionTo($permission);
        }

        $admin->assignRole($role);
        $admins = Admin::first();
        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

        $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);
        Sanctum::actingAs($admins);

        $user = [
            'name' => 'Jon',
            'email' => 'jon@gmail.co',
            'phone_number' => '08109302177',
            'password'=> 'password',
            'password_confirmation'=>'password'
        ];

        $response =  $this->postJson(route('onboard-user'),$user)->assertStatus(201)->json();


        $suspend = [
            'isBanned' => "true"
        ];

        $response =  $this->getJson(route('suspend-user',[$response['data']['user']['id']]), $suspend)->assertStatus(201)->json();

        $activate = [
            'isBanned' => "false"
        ];
        $response =  $this->getJson(route('activate-user',[$response['data']['user']['id']]), $activate)->assertStatus(201)->json();
        $this->assertEquals($activate['isBanned'],$response['data']['user']['isBanned']);
    }



    public function test_check_if_admin_can_assign_permission_to_role(){

        $this->withExceptionHandling();
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

        $admin =  Admin::factory()->make();

        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

        foreach($permissions as $permission){
            $role->givePermissionTo($permission);
        }

        $admin->assignRole($role);
        $admins = Admin::first();
        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

        $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);
        Sanctum::actingAs($admins);


        $role = [
            'guard_name' => 'admin',
            'name' => 'Accounting',
        ];
        $response =  $this->postJson(route('create_role'),$role)->assertStatus(201)->json();

        $permissionId = [
            'permission_id' =>  $permissions[0]['id']
        ];
        $response =  $this->postJson(route('assign_permission_role',[ $response['data']['role']['id']]), $permissionId)->assertStatus(201)->json();

        $this->assertEquals( $permissions[0]['name'],$response['data']['permission']['name']);
    }

    public function test_check_if_admin_can_revoke_permission_to_role(){

        $this->withExceptionHandling();
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

        $admin =  Admin::factory()->make();

        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

        foreach($permissions as $permission){
            $role->givePermissionTo($permission);
        }

        $admin->assignRole($role);
        $admins = Admin::first();
        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

        $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);
        Sanctum::actingAs($admins);


        $role = [
            'guard_name' => 'admin',
            'name' => 'Accounting',
        ];
        $response =  $this->postJson(route('create_role'),$role)->assertStatus(201)->json();

        $permissionId = [
            'permission_id' =>  $permissions[0]['id']
        ];
        $response =  $this->postJson(route('revoke_permission_role',[ $response['data']['role']['id']]), $permissionId)->assertStatus(201)->json();

        $this->assertEquals( $permissions[0]['name'],$response['data']['permission']['name']);
    }

    public function test_check_if_admin_can_initialize_transaction(){

        $this->withExceptionHandling();
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

        $admin =  Admin::factory()->make();

        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

        foreach($permissions as $permission){
            $role->givePermissionTo($permission);
        }

        $admin->assignRole($role);
        $admins = Admin::first();
        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

        $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);

        Sanctum::actingAs($admins);

        $user = [
            'name' => 'Jon',
            'email' => 'jon@gmail.co',
            'phone_number' => '08109302177',
            'password'=> 'password',
            'password_confirmation'=>'password'
        ];

        $response =  $this->postJson(route('onboard-user'),$user)->assertStatus(201)->json();

        $pay = [
            'amount' => 2000,
            'walletID' => $response['data']['wallet']['account_number'],
        ];
        $response =  $this->postJson(route('make_payment'),$pay)->assertStatus(201)->json();

        $this->assertArrayHasKey('payment_url',$response['data']);
    }

    public function test_check_admin_can_create_other_admin(){

        $this->withExceptionHandling();
        $this->seed(PermissionSeeder::class);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseHas('admins',['email' => config('admin.email')]);

        $admin =  Admin::factory()->make();



        $role =  Role::where(['guard_name' => 'admin', 'name' => 'Administrator'])->first();

        $permissions  =  Permission::where(['guard_name' => 'admin'])->get();

        foreach($permissions as $permission){
            $role->givePermissionTo($permission);
        }

        $admin->assignRole($role);
        $admins = Admin::first();

        $adminCred = [
            'email' =>   $admin['email'],
            'password'=>  $admin['password']
        ];

        $this->postJson(route('authenticate_admin'),$adminCred)->assertStatus(201);


        Sanctum::actingAs($admins);

        $role = [
            'guard_name' => 'admin',
            'name' => 'Accounting',
        ];
        $response =  $this->postJson(route('create_role'),$role)->assertStatus(201)->json();


        $user = [
            'name' => 'Jon',
            'email' => 'jon@gmail.co',
            'role_id' =>  $response['data']['role']['id']
        ];

        $response =  $this->postJson(route('admin_create_account'),$user)->assertStatus(201)->json();

        $this->assertEquals($user['email'], $response['data']['admin']['email']);

    }








}
