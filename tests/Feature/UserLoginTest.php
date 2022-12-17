<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_login()
    {
        $reg = [
            'name' => 'Jon',
            'email' => 'jon@gmail.co',
            'phone_number' => '08109302177',
            'password'=> 'password',
            'password_confirmation'=>'password'
        ];

        $response =  $this->postJson(route('create_account'),$reg)->assertStatus(201)->json();

        $user = [
            'email' => 'jon@gmail.co',
            'password'=> 'password'
        ];

        $response =  $this->postJson(route('signin'),$user)->assertOk()->json();

        $this->assertArrayHasKey('token', $response['data']);

    }

    public function test_while_login_email_is_required(){
        $this->withExceptionHandling();
        $response = $this->postJson(route('signin'))->assertStatus(422);
        $response->assertJsonMissingValidationErrors($response->json()['data']['email']);
    }

    public function test_while_password_email_is_required(){
        $this->withExceptionHandling();
        $response = $this->postJson(route('signin'))->assertStatus(422);
        $response->assertJsonMissingValidationErrors($response->json()['data']['password']);
    }
}
