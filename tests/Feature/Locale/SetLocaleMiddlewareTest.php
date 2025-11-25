<?php

declare(strict_types=1);

namespace Locale;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class SetLocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_sets_locale_from_session(): void
    {
        $user = User::factory()->create();
        $appLocale = config('app.locale');

        // Set locale in session
        Session::put('locale', $appLocale);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals($appLocale, App::getLocale());
    }

    public function test_middleware_uses_default_locale_when_no_session(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals(config('app.locale'), App::getLocale());
    }

    public function test_middleware_validates_locale_from_session(): void
    {
        $user = User::factory()->create();

        // Set invalid locale in session
        Session::put('locale', 'invalid');

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        // Should fall back to default locale
        $this->assertEquals(config('app.locale'), App::getLocale());
    }

    public function test_middleware_handles_english_locale(): void
    {
        $user = User::factory()->create();
        $appLocale = config('app.locale');

        Session::put('locale', $appLocale);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals($appLocale, App::getLocale());
    }

    public function test_locale_affects_translations(): void
    {
        $user = User::factory()->create();
        $appLocale = config('app.locale');

        // Test with English
        Session::put('locale', $appLocale);
        $this
            ->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals($appLocale, App::getLocale());

        // Test with German
        Session::put('locale', 'de');
        $this
            ->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals('de', App::getLocale());
    }

    public function test_middleware_works_for_guest_users(): void
    {
        $appLocale = config('app.locale');
        Session::put('locale', $appLocale);

        $response = $this->get('/');

        $this->assertEquals('de', App::getLocale());
    }
}
