<button {{ $attributes->merge(['type' => 'button', 'class' => 'app-secondary-button focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
