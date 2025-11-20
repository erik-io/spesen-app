<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testEmployee = User::where('email', 'employee@example.com')->first();

        if ($testEmployee) {
            $this->createExpensesForUsers($testEmployee);
        }

        $randomEmployees = User::factory(10)->create();
        foreach ($randomEmployees as $employee) {
            $employee->assignRole('employee');
            $this->createExpensesForUsers($employee);
        }
    }

    /**
     * Helper to create a mix of expenses for a given user.
     *
     * @param $user
     * @return void
     */
    private function createExpensesForUsers($user)
    {
        // Create pending expenses
        Expense::factory()
            ->count(rand(3, 7))
            ->pending()
            ->create(['user_id' => $user->id]);

        // Create approved expenses (history)
        Expense::factory()
            ->count(rand(5, 15))
            ->approved()
            ->create(['user_id' => $user->id]);

        // Create rejected expenses (history)
        Expense::factory()
            ->count(rand(3, 7))
            ->rejected()
            ->create(['user_id' => $user->id]);
    }
}
