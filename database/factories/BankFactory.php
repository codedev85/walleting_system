<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bank>
 */
class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'bank_name' => 'Hasal Microfinance Bank',
            'bank_code' => '50383',
            'country'  => 'Nigeria',
            'currency' => 'NGN',
            'type' => 'nuban',
        ];
    }
}
