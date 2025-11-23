<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(Expense::STATUSES);
        return [
            'user_id' => User::factory(),
            'expense_date' => $this->faker->dateTimeBetween(now()->subDays(Expense::MAX_SUBMISSION_AGE_DAYS), 'now')->format('Y-m-d'),
            'cost_center' => $this->faker->bothify('CC-###'),
            'amount' => $this->faker->randomFloat(Expense::AMOUNT_SCALE, 10, 5000),
            'status' => $status,
            'rejection_comment' => $status === Expense::STATUS_REJECTED ? $this->faker->paragraph() : null,
        ];
    }

    /** Indicate that the expense is pending */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Expense::STATUS_PENDING,
            'rejection_comment' => null,
        ]);
    }

    /** Indicate that the expense is approved */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Expense::STATUS_APPROVED,
            'rejection_comment' => null,
        ]);
    }

    /** Indicate that the expense is rejected */
    public function rejected(string $comment = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Expense::STATUS_REJECTED,
            'rejection_comment' => $comment ?? $this->faker->paragraph(),
        ]);
    }
}
