@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'app-error-list space-y-1 text-sm text-red-600 dark:text-rose-400']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
