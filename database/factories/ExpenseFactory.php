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
        $status = $this->faker->randomElement(['pending', 'approved', 'rejected']);
        return [
            'user_id' => User::factory(),
            'expense_date' => $this->faker->dateTimeBetween('-90 days', 'now')->format('Y-m-d'),
            'cost_center' => $this->faker->bothify('CC-###'),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $status,
            'rejection_comment' => $status === 'rejected' ? $this->faker->paragraph() : null,
        ];
    }

    /** Indicate that the expense is pending */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'rejection_comment' => null,
        ]);
    }

    /** Indicate that the expense is approved */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'rejection_comment' => null,
        ]);
    }

    /** Indicate that the expense is rejected */
    public function rejected(string $comment = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_comment' => $comment ?? $this->faker->paragraph(),
        ]);
    }
}
