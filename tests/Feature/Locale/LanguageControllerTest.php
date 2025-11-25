<?php

declare(strict_types=1);

namespace Locale;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_switch_to_valid_locale(): void
    {
        $user = User::factory()->create();

        $appLocale = config('app.locale');

        $response = $this
            ->actingAs($user)
            ->get('/locale/' . $appLocale);

        $response->assertRedirect();
        $this->assertEquals($appLocale, Session::get('locale'));
    }

    public function test_can_switch_to_fallback_locale(): void
    {
        $user = User::factory()->create();

        $fallbackLocale = config('app.fallback_locale');

        $response = $this
            ->actingAs($user)
            ->get('/locale/' . $fallbackLocale);

        $response->assertRedirect();
        $this->assertEquals($fallbackLocale, Session::get('locale'));
    }

    public function test_invalid_locale_falls_back_to_default(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/locale/invalid');

        $response->assertRedirect();
        $this->assertEquals(config('app.fallback_locale'), Session::get('locale'));
    }

    public function test_switching_locale_redirects_back(): void
    {
        $user = User::factory()->create();

        $appLocale = config('app.locale');

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->get('/locale/' . $appLocale);

        $response->assertRedirect('/dashboard');
    }

    public function test_guest_can_switch_locale(): void
    {
        $appLocale = config('app.fallback_locale');

        $response = $this->get('/locale/' . $appLocale);

        $response->assertRedirect();
        $this->assertEquals($appLocale, Session::get('locale'));
    }

    public function test_locale_persists_across_requests(): void
    {
        $user = User::factory()->create();

        $appLocale = config('app.locale');

        // First request
        $this
            ->actingAs($user)
            ->get('/locale/' . $appLocale);

        $this->assertEquals($appLocale, Session::get('locale'));

        // Check if locale is still set
        $this
            ->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals($appLocale, Session::get('locale'));
    }

    public function test_can_switch_between_multiple_locales(): void
    {
        $user = User::factory()->create();

        $appLocale = config('app.locale');
        $fallbackLocale = config('app.fallback_locale');

        // Switch to German
        $this
            ->actingAs($user)
            ->get('/locale/' . $appLocale);

        $this->assertEquals($appLocale, Session::get('locale'));

        // Switch to English
        $this
            ->actingAs($user)
            ->get('/locale/' . $fallbackLocale);

        $this->assertEquals($fallbackLocale, Session::get('locale'));
    }

    public function test_locale_route_exists(): void
    {
        $this->assertTrue(
            collect(Route::getRoutes())
                ->contains(fn ($route) => $route->getName() === 'locale.switch')
        );
    }
}
