<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_redirects_to_management_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('expenses.management.index'));
    }

    public function test_employee_redirects_to_expenses_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('expenses.index'));
    }
}
