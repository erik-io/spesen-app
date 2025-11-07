<?php

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
            'name' => 'Employee',
            'email' => 'employee@example.com',
        ]);

        $testEmployee->assignRole('employee');

        $testSupervisor = User::factory()->create([
            'name' => 'Supervisor',
            'email' => 'supervisor@example.com'
        ]);

        $testSupervisor->assignRole('supervisor');
    }
}
