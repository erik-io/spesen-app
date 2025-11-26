@props(['amount'])
@use('Illuminate\Support\Number')

<span {{ $attributes }}>
    {{ Number::currency($amount, in: 'EUR', locale: app()->getLocale()) }}
</span>
