<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RolesAndPermissionsSeeder::class);

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
