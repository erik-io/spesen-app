<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is stored in a session
        if (Session::has('locale')) {
            $locale = Session::get('locale');

            // Validate that the locale is supported
            if (in_array($locale, config('locales.available_locales'))) {
                App::setLocale($locale);

                // Set Carbon locale for date formatting
                Carbon::setLocale($locale);
            }
        }

        return $next($request);
    }
}
