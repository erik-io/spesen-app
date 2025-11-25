<?php

declare(strict_types=1);

namespace Locale;

use Tests\TestCase;

class LocalesConfigTest extends TestCase
{
    public function test_locales_config_file_exists(): void
    {
        $config = include base_path('config/locales.php');

        $this->assertIsArray($config);
    }

    public function test_available_locales_are_defined(): void
    {
        $config = include base_path('config/locales.php');

        $this->assertArrayHasKey('available_locales', $config);
        $this->assertIsArray($config['available_locales']);
        $this->assertNotEmpty($config['available_locales']);
    }

    public function test_flags_are_defined(): void
    {
        $config = include base_path('config/locales.php');

        $this->assertArrayHasKey('flags', $config);
        $this->assertIsArray($config['flags']);
    }

    public function test_flags_match_available_locales(): void
    {
        $config = include base_path('config/locales.php');

        foreach ($config['available_locales'] as $locale) {
            $this->assertArrayHasKey($locale, $config['flags'], "Flag missing for locale: $locale");
        }
    }

    public function test_locale_names_are_defined(): void
    {
        $config = include base_path('config/locales.php');

        $this->assertArrayHasKey('names', $config);
        $this->assertIsArray($config['names']);
    }

    public function test_names_match_available_locales(): void
    {
        $config = include base_path('config/locales.php');

        foreach ($config['available_locales'] as $locale) {
            $this->assertArrayHasKey($locale, $config['names'], "Name missing for locale: $locale");
        }
    }

    public function test_all_config_keys_are_present(): void
    {
        $config = include base_path('config/locales.php');

        $expectedKeys = ['available_locales', 'flags', 'names'];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $config, "Config key missing: $key");
        }
    }

    public function test_app_locale_and_fallback_locales_are_defined(): void
    {
        $config = include base_path('config/locales.php');

        $appLocale = config('app.locale');
        $this->assertContains($appLocale, $config['available_locales'], "Locale missing in available_locales: $appLocale");

        $fallbackLocale = config('app.fallback_locale');
        $this->assertContains($fallbackLocale, $config['available_locales'], "Locale missing in available_locales: $fallbackLocale");
    }
}
