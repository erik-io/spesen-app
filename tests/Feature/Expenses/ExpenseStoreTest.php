<?php

namespace Tests\Feature\Expenses;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_store_expense_with_valid_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 123.45,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->post(route('expenses.index'), $payload);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'amount' => 123.45,
            'expense_date' => $payload['expense_date'],
            'cost_center' => 'CC-101',
            'status' => 'pending',
        ]);
    }
}
