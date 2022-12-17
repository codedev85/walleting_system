<?php

namespace Tests\Feature;

//use App\Models\User;
use App\Helper\Otp;
use App\Jobs\SendMailJob;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class OnboardUserTest extends TestCase
{
    use RefreshDatabase;

    private $jobPid;

    public function setUp():void
    {
        parent::setUp();
        // Setup Queue Listener
        exec('php artisan queue:listen --env=testing > /tmp/output.txt 2>&1 & echo $!', $pid);
        $this->jobPid=(int)$pid[0];
    }


    protected function tearDown():void
    {
        parent::tearDown();
        // ShutDown Queue Listener
        exec("kill -9 {$this->jobPid}");
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_create_account()
    {

//        $user =  User::factory()->make();
        $user = [
            'name' => 'Jon',
            'email' => 'jon@gmail.co',
            'phone_number' => '08109302177',
            'password'=> 'password',
            'password_confirmation'=>'password'
        ];


        $response =  $this->postJson(route('create_account'),$user)->assertStatus(201)->json();

        $this->assertEquals('Jon', $response['data']['user']['name']);

        $this->assertDatabaseHas('users',['name' => $response['data']['user']['name']]);

    }

    public function test_while_storing_name_is_required(){
        $this->withExceptionHandling();
        $response = $this->postJson(route('create_account'))->assertStatus(422);
        $response->assertJsonMissingValidationErrors($response->json()['data']['name']);
    }
    public function test_while_storing_email_is_required(){
        $this->withExceptionHandling();
        $response = $this->postJson(route('create_account'))->assertStatus(422);
        $response->assertJsonMissingValidationErrors($response->json()['data']['email']);
    }
    public function test_while_storing_phone_is_required(){
        $this->withExceptionHandling();
        $response = $this->postJson(route('create_account'))->assertStatus(422);
        $response->assertJsonMissingValidationErrors($response->json()['data']['phone_number']);
    }

    public function test_while_storing_password_is_required(){
        $this->withExceptionHandling();
        $response = $this->postJson(route('create_account'))->assertStatus(422);
        $response->assertJsonMissingValidationErrors($response->json()['data']['password']);
    }


    public function test_if_account_number_is_generated_at_the_point_of_registration(){
        $user = [
            'name' => 'Jon',
            'email' => 'jon@gmail.co',
            'phone_number' => '08109302177',
            'password'=> 'password',
            'password_confirmation'=>'password'
        ];

        $response =  $this->postJson(route('create_account'),$user)->assertStatus(201)->json();

        $this->assertEquals(10,Str::length($response['data']['wallet']['account_number']));
    }

    public function test_if_verification_otp_is_generated(){
        $token =  Otp::generate();
        $this->assertEquals(4,Str::length($token));

    }

    public function test_fetch_user_profile(){
        $this->withExceptionHandling();
        $user =  User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user['id']]);
        Sanctum::actingAs($user);
        $response =  $this->getJson(route('profile'))->assertOk()->json();
        $this->assertEquals($user['name'],$response['data']['user']['name'] );
    }

    public function test_if_verification_email_is_sent(){
//        Mail::fake();
////        $user =  User::factory()->make();
//        $user = [
//            'name' => 'Jon',
//            'email' => 'jon@gmail.co',
//            'phone_number' => '08109302177',
//            'password'=> 'password',
//            'password_confirmation'=>'password'
//        ];
//        $response =  $this->postJson(route('create_account'),$user)->assertStatus(201)->json();
//        $token =  Otp::generate();
//        dispatch(new SendMailJob($response['data']['user']['email'] , $token));
//        sleep(5);
//        Mail::assertSent(VerifyEmail::class, function($mail){
//            return $mail->subject == "Hello";
//        });


    }
}
