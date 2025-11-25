<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(string $locale): RedirectResponse
    {
        // Validate locale
        if (!in_array($locale, config('locales.available_locales'))) {
            $locale = config('app.fallback_locale');
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Redirect back to previous page
        return redirect()->back();
    }
}
