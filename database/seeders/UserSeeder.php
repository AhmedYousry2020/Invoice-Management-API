<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $this->command->info("Test User Created:");
        $this->command->info("Email: {$user->email}");
        $this->command->info("Password: password");
        $this->command->info("Sanctum Token: {$token}");
    }
}