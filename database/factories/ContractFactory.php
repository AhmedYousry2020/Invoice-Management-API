<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ContractStatus;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(), 
            'unit_name' => 'Unit ' . $this->faker->numberBetween(1, 100),
            'customer_name' => $this->faker->name(),
            'rent_amount' => $this->faker->randomFloat(2, 500, 5000),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'status' => ContractStatus::Active,
        ];
    }
}