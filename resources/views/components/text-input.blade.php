@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'app-field-input shadow-sm dark:shadow-none']) }}>
