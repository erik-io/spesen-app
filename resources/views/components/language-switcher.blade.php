@php
    $locale = app()->getLocale();
    $flags = config('locales.flags');
    $localeNames = config('locales.names');
    $availableLocales = config('locales.available_locales');
    $currentFlag = $flags[$locale] ?? 'fi-xx';
@endphp

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open"
            type="button"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
        {{-- Language Icon --}}
        <i class="fas fa-language w-5 h-5 me-1"></i>
        {{-- Current Locale --}}
        <span class="uppercase">
        <i class="fi {{ $currentFlag }} text-sm ms-1 me-1"></i>
            {{ $locale }}
            </span>
        {{-- Down Arrow --}}
        <i class="fas fa-chevron-down ms-1 -me-0.5 h-4 w-4"></i>
    </button>

    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
         style="display: none;">
        <div class="py-1">
            @foreach($availableLocales as $availableLocale)
                <a href="{{ route('locale.switch', $availableLocale) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $locale === $availableLocale ? 'bg-gray-50 dark:bg-gray-900 font-semibold' : '' }}">
                    <span class="fi {{ $flags[$availableLocale] ?? 'fi-xx' }} text-lg me-2"></span>
                    {{ $localeNames[$availableLocale] ?? strtoupper($availableLocale) }}
                    @if($locale === $availableLocale)
                        {{-- Green checkmark --}}
                        <i class="fas fa-check ms-auto text-green-600 dark:text-green-400"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
