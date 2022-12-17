<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\MyBank;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BankTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_fetch_banks_from_database()
    {
        $this->withExceptionHandling();
        $user =  User::factory()->create();
        $bank =  Bank::factory()->create();
        Sanctum::actingAs($user);
        $response =  $this->getJson(route('fetch_banks'))->assertStatus(201)->json();
        $this->assertEquals($bank['bank_name'],$response['data']['bank_list'][0]['bank_name'] );
    }

    public function test_user_can_add_bank_details(){
        $this->withExceptionHandling();
        $user =  User::factory()->create();
        $bank =  Bank::factory()->create();
//        $usersBank = MyBank::factory()->create(['user_id' => $user['id'] , 'bank_id' => $bank['id']]);
        $createBank = [
             'user_id' => $user['id'] ,
            'bank_id' => $bank['id'],
            'account_name' => 'jane Doe',
            'account_number' => '2233333333'
        ];
        Sanctum::actingAs($user);
        $response =  $this->postJson(route('add_bank'), $createBank)->assertStatus(201)->json();

        $this->assertEquals($createBank['account_number'],$response['data']['bank_list']['account_number'] );
    }

    public function test_check_if_user_has_already_added_bank_details(){
        $this->withExceptionHandling();
        $user =  User::factory()->create();
        $bank =  Bank::factory()->create();
        $createBank = [
            'user_id' => $user['id'] ,
            'bank_id' => $bank['id'],
            'account_name' => 'jane Doe',
            'account_number' => '2233333333'
        ];
         Sanctum::actingAs($user);
         $this->postJson(route('add_bank'), $createBank)->assertStatus(201)->json();
        $response =  $this->getJson(route('fetch_my_banks'))->assertStatus(201)->json();
        $this->assertGreaterThan(0 ,count($response['data']['my_bank_list']) );
    }

    public function test__check_if_user_does_has_not_added_bank_details(){
        $this->withExceptionHandling();
        $user =  User::factory()->create();
        Sanctum::actingAs($user);
        $response =  $this->getJson(route('fetch_my_banks'))->assertStatus(201)->json();
        $this->assertEquals([],$response['data']['my_bank_list']);
    }


    public function test_check_if_user_can_generate_withdrawal_pin(){
        $this->withExceptionHandling();
        $user =  User::factory()->create();
        Sanctum::actingAs($user);
        $response =  $this->getJson(route('withdrawal_pin'))->assertStatus(201)->json();
        $this->assertEquals('Otp generated  successfully',$response['data']['otp']);
    }
}
