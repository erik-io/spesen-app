<?php

namespace Tests\Feature\Expenses;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_view_pending_expenses_of_all_employees(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $employee1 = User::factory()->create();
        $employee1->assignRole('employee');
        $expenseForEmployee1 = Expense::factory()->for($employee1)->pending()->create();

        $employee2 = User::factory()->create();
        $employee2->assignRole('employee');
        $expenseForEmployee2 = Expense::factory()->for($employee1)->pending()->create();

        $response = $this->actingAs($supervisor)->get(route('expenses.management.index'));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($expenses) use ($expenseForEmployee1, $expenseForEmployee2) {
            return $expenses->contains($expenseForEmployee1)
                && $expenses->contains($expenseForEmployee2);
        });
    }

    public function test_supervisor_can_view_approved_expenses_of_all_employees_in_history(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $employee1 = User::factory()->create();
        $employee1->assignRole('employee');
        $expenseForEmployee1 = Expense::factory()->for($employee1)->approved()->create();

        $employee2 = User::factory()->create();
        $employee2->assignRole('employee');
        $expenseForEmployee2 = Expense::factory()->for($employee2)->approved()->create();

        $response = $this->actingAs($supervisor)->get(route('expenses.management.history'));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($expenses) use ($expenseForEmployee1, $expenseForEmployee2) {
            return $expenses->contains($expenseForEmployee1)
                && $expenses->contains($expenseForEmployee2);
        });
    }

    public function test_supervisor_can_view_rejected_expenses_of_all_employees_in_history(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $employee1 = User::factory()->create();
        $employee1->assignRole('employee');
        $expenseForEmployee1 = Expense::factory()->for($employee1)->rejected()->create();

        $employee2 = User::factory()->create();
        $employee2->assignRole('employee');
        $expenseForEmployee2 = Expense::factory()->for($employee2)->rejected()->create();

        $response = $this->actingAs($supervisor)->get(route('expenses.management.history'));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($expenses) use ($expenseForEmployee1, $expenseForEmployee2) {
            return $expenses->contains($expenseForEmployee1)
                && $expenses->contains($expenseForEmployee2);
        });
    }

    public function test_supervisor_sees_expenses_of_all_employees_in_history(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $employee1 = User::factory()->create();
        $employee1->assignRole('employee');
        $expenseForEmployee1 = Expense::factory()->for($employee1)->create();

        $employee2 = User::factory()->create();
        $employee2->assignRole('employee');
        $expenseForEmployee2 = Expense::factory()->for($employee2)->create();

        $response = $this->actingAs($supervisor)->get(route('expenses.management.history'));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($expenses) use ($expenseForEmployee1, $expenseForEmployee2) {
            return $expenses->contains($expenseForEmployee1)
                && $expenses->contains($expenseForEmployee2);
        });
    }

    public function test_supervisor_index_shows_pending_expenses_default_sorted_oldest_first(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $e1 = Expense::factory()->pending()->create(['created_at' => now()->subDays(3)]);
        $e2 = Expense::factory()->pending()->create(['created_at' => now()->subDays(2)]);
        $e3 = Expense::factory()->pending()->create(['created_at' => now()->subDay()]);

        // Non-pending should not appear
        Expense::factory()->approved()->create();
        Expense::factory()->rejected()->create();

        $response = $this->actingAs($supervisor)
            ->get(route('expenses.management.index'));

        $response->assertOk();
        $response->assertViewHas('expenses', function ($paginator) use ($e1, $e3) {
            $items = array_values($paginator->items());
            // Default for management pending is asc (oldest first)
            return $items[0]->id === $e1->id && end($items)->id === $e3->id;
        });
    }

    public function test_supervisor_history_lists_all_with_default_desc_and_can_filter_status(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $p = Expense::factory()->pending()->create(['created_at' => now()->subDays(3)]);
        $a = Expense::factory()->approved()->create(['created_at' => now()->subDays(2)]);
        $r = Expense::factory()->rejected()->create(['created_at' => now()->subDay()]);

        // Default desc
        $this->actingAs($supervisor)
            ->get(route('expenses.management.history'))
            ->assertOk()
            ->assertViewHas('expenses', function ($paginator) use ($r) {
                $items = $paginator->items();
                return $items[0]->id === $r->id; // newest first
            });

        // Filter only approved
        $this->actingAs($supervisor)
            ->get(route('expenses.management.history', ['status' => 'approved']))
            ->assertOk()
            ->assertViewHas('expenses', function ($paginator) {
                return collect($paginator->items())->every(fn($e) => $e->status === 'approved');
            });
    }

    public function test_supervisor_can_view_expense_details(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $expense = Expense::factory()->for($employee)->create();

        $response = $this->actingAs($supervisor)
            ->get(route('expenses.management.show', $expense));

        $response->assertOk();
        $response->assertViewIs('expenses.management.show');
        // Check if the specific expense is passed to the view
        $response->assertViewHas('expense', $expense);
    }

    public function test_supervisor_can_approve_and_clears_previous_rejection_comment(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');
        $expense = Expense::factory()->rejected('Too expensive')->create();

        $this->actingAs($supervisor)
            ->patch(route('expenses.management.approve', $expense))
            ->assertRedirect(route('expenses.management.index'));

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'status' => 'approved',
            'rejection_comment' => null,
        ]);
    }

    public function test_supervisor_can_reject_with_comment_and_validation_enforced(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');
        $expense = Expense::factory()->pending()->create();

        // Missing comment -> validation error
        $this->actingAs($supervisor)
            ->from(route('expenses.management.show', $expense))
            ->patch(route('expenses.management.reject', $expense), [])
            ->assertRedirect(route('expenses.management.show', $expense))
            ->assertSessionHasErrors(['rejection_comment']);

        // Proper rejection
        $this->actingAs($supervisor)
            ->patch(route('expenses.management.reject', $expense), ['rejection_comment' => 'Invalid receipt'])
            ->assertRedirect(route('expenses.management.index'));

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'status' => 'rejected',
            'rejection_comment' => 'Invalid receipt',
        ]);
    }

    public function test_guest_is_redirected_and_non_supervisor_forbidden_for_management_routes(): void
    {
        // Guest
        $this->get(route('expenses.management.index'))
            ->assertRedirect(route('login'));

        $this->get(route('expenses.management.history'))
            ->assertRedirect(route('login'));

        // Authenticated but no supervisor role
        $user = User::factory()->create();
        $user->assignRole('employee');

        $this->actingAs($user)
            ->get(route('expenses.management.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('expenses.management.history'))
            ->assertForbidden();
    }

    public function test_employee_is_forbidden_from_management_actions(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $expense = Expense::factory()->for($employee)->pending()->create();

        $this->actingAs($employee)
            ->get(route('expenses.management.show', $expense))
            ->assertForbidden();

        $this->actingAs($employee)
            ->patch(route('expenses.management.approve', $expense))
            ->assertForbidden();

        $this->actingAs($employee)
            ->patch(route('expenses.management.reject', $expense), ['rejection_comment' => 'test'])
            ->assertForbidden();
    }

    public function test_supervisor_can_approve_a_pending_expense(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');
        $expense = Expense::factory()->pending()->create();

        $this->actingAs($supervisor)
            ->patch(route('expenses.management.approve', $expense))
            ->assertRedirect(route('expenses.management.index'));

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'status' => 'approved',
        ]);
    }

    public function test_approving_an_already_approved_expense_does_not_change_status(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');
        $expense = Expense::factory()->approved()->create();

        $this->actingAs($supervisor)
            ->patch(route('expenses.management.approve', $expense))
            ->assertRedirect(route('expenses.management.index'));

        $this->assertEquals('approved', $expense->fresh()->status);
    }

    public function test_management_index_falls_back_to_defaults_with_invalid_query_params(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        Expense::factory(15)->pending()->create();

        $response = $this->actingAs($supervisor)
            ->get(route('expenses.management.index', [
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

    public function test_employee_cannot_view_supervisor_show_route(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $expense = Expense::factory()->for($employee)->create();

        $response = $this->actingAs($employee)
            ->get(route('expenses.management.show', $expense));

        // Should be forbidden by 'role:supervisor' middleware
        $response->assertStatus(403);
    }

    public function test_employee_cannot_approve_route(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $expense = Expense::factory()->for($employee)->create();

        $response = $this->actingAs($employee)
            ->patch(route('expenses.management.approve', $expense));

        $response->assertStatus(403);
    }

    public function test_employee_cannot_reject_route(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $expense = Expense::factory()->for($employee)->create();

        $response = $this->actingAs($employee)
            ->patch(route('expenses.management.reject', $expense), ['rejection_comment' => 'test']);

        $response->assertStatus(403);
    }
}
