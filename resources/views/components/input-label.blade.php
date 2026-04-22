@props(['value'])

<label {{ $attributes->merge(['class' => 'app-field-label']) }}>
    {{ $value ?? $slot }}
</label>
