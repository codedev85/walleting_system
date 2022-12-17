<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class OnboardUserFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
//        $pass = $this->faker->password;
        return [
            "name" => $this->faker->name,
            "email" => $this->faker->email,
            "phone_number" => $this->faker->phone,
            "password" => 'password' ,
            "password_confirmation" =>'password',
        ];
    }
}
