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

        Expense::factory(10)->for($employee)->create();
    }
}
