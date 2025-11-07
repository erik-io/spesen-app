<?php

namespace Tests\Feature\Expenses;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_expenses_list(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $response = $this->actingAs($employee)->get(route('expenses.index'));
        $response->assertOk();
        $response->assertViewIs('expenses.index');
        $response->assertViewHas('expenses');
    }

    public function test_user_cannot_view_expenses_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('expenses.index'));
        $response->assertForbidden();
    }

    public function test_supervisor_cannot_view_expense_list(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $response = $this->actingAs($user)->get(route('expenses.index'));
        $response->assertForbidden();
    }

    public function test_user_cannot_view_expenses_list_without_auth(): void
    {
        $response = $this->get(route('expenses.index'));
        $response->assertRedirect('/login');
    }

    public function test_employee_only_sees_their_expenses(): void
    {
        $employee1 = User::factory()->create();
        $employee1->assignRole('employee');
        $expenseForEmployee1 = Expense::factory()->for($employee1)->create();

        $employee2 = User::factory()->create();
        $employee2->assignRole('employee');
        $expenseForEmployee2 = Expense::factory()->for($employee2)->create();

        $response = $this->actingAs($employee1)->get(route('expenses.index'));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($expenses) use ($expenseForEmployee1, $expenseForEmployee2) {
            return $expenses->contains($expenseForEmployee1)
                && !$expenses->contains($expenseForEmployee2);
        });
    }

    public function test_employee_only_sees_their_expenses_paginated(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $other = User::factory()->create();
        $other->assignRole('employee');

        // Create 15 expenses for the employee
        Expense::factory(15)->for($employee)->create();
        // Create some expenses for another employee that should not be visible
        Expense::factory(5)->for($other)->create();

        $response = $this->actingAs($employee)->get(route('expenses.index'));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($paginator) use ($employee) {
            // Default per page is 10
            $this->assertCount(10, $paginator->items());
            $this->assertEquals(15, $paginator->total());
            // Ensure all expenses belong to the correct employee
            return collect($paginator->items())->every(fn($expense) => $expense->user_id === $employee->id);
        });
    }

    public function test_employee_sees_only_their_expenses_paginated_and_sorted_by_default(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $other = User::factory()->create();
        $other->assignRole('employee');

        // Create expenses with different created_at to test sorting default desc
        Expense::factory()->for($employee)->pending()->create(['created_at' => now()->subDays(3)]);
        Expense::factory()->for($employee)->pending()->create(['created_at' => now()->subDay()]);
        Expense::factory()->for($employee)->pending()->create(['created_at' => now()->subDays(2)]);

        // Other user's expenses should not appear
        Expense::factory(2)->for($other)->pending()->create();

        $response = $this->actingAs($employee)
            ->get(route('expenses.index'));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($paginator) use ($employee) {
            $this->assertSame(3, $paginator->total());
            // Default sort is created_at desc (newest first)
            $items = $paginator->items();
            $this->assertGreaterThanOrEqual($items[1]['created_at'], $items[0]['created_at']);
            return collect($items)->every(fn($e) => $e->user_id === $employee->id);
        });
    }

    public function test_invalid_query_params_fall_back_to_defaults(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        Expense::factory(15)->for($employee)->pending()->create();

        $response = $this->actingAs($employee)
            ->get(route('expenses.index', [
                'sort_by' => 'invalid_column',
                'sort_direction' => 'weird',
                'per_page' => 9999,
            ]));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($paginator) {
            // Default per page is 10
            $this->assertCount(10, $paginator->items());
            return true;
        });
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get(route('expenses.index'))->assertRedirect(route('login'));
    }

    public function test_employee_can_sort_expenses_by_amount(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $expense1 = Expense::factory()->for($employee)->create(['amount' => 100]);
        $expense2 = Expense::factory()->for($employee)->create(['amount' => 300]);
        $expense3 = Expense::factory()->for($employee)->create(['amount' => 200]);

        $response = $this->actingAs($employee)
            ->get(route('expenses.index', ['sort_by' => 'amount', 'sort_direction' => 'asc']));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($paginator) use ($expense1, $expense2) {
            $items = $paginator->items();
            $this->assertEquals($expense1->id, $items[0]->id);
            $this->assertEquals($expense2->id, $items[2]->id);
            return true;
        });
    }

    public function test_employee_can_sort_expenses_by_date_ascending(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $oldExpense = Expense::factory()->for($employee)->create([
            'expense_date' => now()->subDays(5)->toDateString()
        ]);

        $newExpense = Expense::factory()->for($employee)->create([
            'expense_date' => now()->subDays(1)->toDateString()
        ]);

        $response = $this->actingAs($employee)->get(route('expenses.index', [
            'sort_by' => 'expense_date',
            'sort_direction' => 'asc'
        ]));

        $response->assertOk();

        // Assert that the items in the view are in the correct order
        $response->assertViewHas('expenses', function ($paginator) use ($oldExpense, $newExpense) {
            $items = $paginator->items();
            return $items[0]->id === $oldExpense->id && $items[1]->id === $newExpense->id;
        });
    }

    public function test_employee_can_set_pagination_per_page(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        Expense::factory(100)->for($employee)->create();

        $response = $this->actingAs($employee)
            ->get(route('expenses.index', ['per_page' => 25]));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($paginator) {
            // Check items on page
            $this->assertCount(25, $paginator->items());
            // Check total items found
            $this->assertSame(100, $paginator->total());
            return true;
        });
    }
}
