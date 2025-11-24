<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testEmployee = User::factory()->create([
            'email' => 'employee@example.com',
        ]);

        $testEmployee->assignRole('employee');

        $testSupervisor = User::factory()->create([
            'email' => 'supervisor@example.com',
        ]);

        $testSupervisor->assignRole('supervisor');
    }
}
