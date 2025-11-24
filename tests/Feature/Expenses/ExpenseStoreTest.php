<?php

declare(strict_types=1);

namespace Tests\Feature\Expenses;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_view_can_be_rendered_by_employee(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $response = $this->actingAs($user)->get(route('expenses.create'));

        $response->assertOk();
        $response->assertViewIs('expenses.create');
        $response->assertSee(__('Submit New Expense Report'));
    }

    public function test_create_view_contains_form_with_required_fields(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $response = $this->actingAs($user)->get(route('expenses.create'));

        $response->assertOk();
        $response->assertSee('name="amount"', false);
        $response->assertSee('name="expense_date"', false);
        $response->assertSee('name="cost_center"', false);
    }

    public function test_create_view_has_confirmation_modal(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $response = $this->actingAs($user)->get(route('expenses.create'));

        $response->assertOk();
        $response->assertSee('confirm-submission');
        $response->assertSee(__('modals.submission.title'));
    }

    public function test_create_view_cannot_be_accessed_by_supervisor(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $response = $this->actingAs($user)->get(route('expenses.create'));

        $response->assertForbidden();
    }

    public function test_create_view_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get(route('expenses.create'));

        $response->assertRedirect(route('login'));
    }

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
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'amount' => 123.45,
            'expense_date' => $payload['expense_date'],
            'cost_center' => 'CC-101',
            'status' => Expense::STATUS_PENDING,
        ]);
    }

    public function test_amount_string_with_comma_is_converted_to_decimal(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => '100,50',
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-202',
        ];

        $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload)
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'amount' => 100.50,
            'expense_date' => $payload['expense_date'],
            'cost_center' => 'CC-202',
        ]);
    }

    public function test_amount_string_with_one_decimal_is_converted_to_decimal(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => '100.5',
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-303',
        ];

        $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload)
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'amount' => 100.50,
            'expense_date' => $payload['expense_date'],
            'cost_center' => 'CC-303',
        ]);
    }

    public function test_employee_cannot_store_expense_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'expense_date' => now()->addDay()->toDateString(),
            'cost_center' => str_repeat('X', Expense::MAX_COST_CENTER_LENGTH + 1),
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['amount', 'expense_date', 'cost_center']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_guest_cannot_store_expense(): void
    {
        $payload = [
            'amount' => 10.00,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
        ];

        $this->post(route('expenses.store'), $payload)
            ->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_store_expense(): void
    {
        $user = User::factory()->create();
        // This user does not have the 'employee' role

        $payload = [
            'amount' => 10.00,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->post(route('expenses.store'), $payload);

        $response->assertForbidden();
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_user_cannot_store_expense_with_too_high_amount(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 100000000.00,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['amount']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_user_cannot_store_expense_with_invalid_date(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 10.00,
            'expense_date' => 'invalid-date',
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['expense_date']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_user_cannot_store_expense_with_invalid_cost_center(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 10.00,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => str_repeat('X', Expense::MAX_COST_CENTER_LENGTH + 1),
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['cost_center']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_user_cannot_store_expense_with_invalid_status(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 10.00,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
            'status' => 'invalid-status',
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_user_cannot_store_expense_with_invalid_user_id(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 10.00,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
            'user_id' => 999,
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['user_id']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_user_cannot_store_expense_with_negative_amount(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => -10.00,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['amount']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_user_cannot_store_expense_with_zero_amount(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 0.00,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['amount']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_user_cannot_store_expense_with_date_before_max_submission_age(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 10.00,
            'expense_date' => now()->subDays(Expense::MAX_SUBMISSION_AGE_DAYS + 1)->toDateString(),
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['expense_date']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_user_cannot_store_expense_with_date_after_today(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 10.00,
            'expense_date' => now()->addDay()->toDateString(),
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['expense_date']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_amount_string_without_decimal_is_converted_to_decimal(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => '100',
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'amount' => 100.00,
            'expense_date' => $payload['expense_date'],
            'cost_center' => 'CC-101',
            'status' => Expense::STATUS_PENDING,
        ]);
    }

    public function test_employee_cannot_store_expense_with_valid_data_and_status_approved(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 123.45,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
            'status' => Expense::STATUS_APPROVED,
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_employee_cannot_manually_set_status_to_rejected(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 123.45,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
            'status' => Expense::STATUS_REJECTED,
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_employee_cannot_manually_set_status_to_pending(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 123.45,
            'expense_date' => now()->subDay()->toDateString(),
            'cost_center' => 'CC-101',
            'status' => Expense::STATUS_PENDING,
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_employee_cannot_store_expense_without_cost_center(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 123.45,
            'expense_date' => now()->subDay()->toDateString(),
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['cost_center']);
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_employee_cannot_store_expense_without_expense_date(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $payload = [
            'amount' => 123.45,
            'cost_center' => 'CC-101',
        ];

        $response = $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $payload);

        $response->assertRedirect(route('expenses.create'));
        $response->assertSessionHasErrors(['expense_date']);
        $this->assertDatabaseCount('expenses', 0);
    }
}
