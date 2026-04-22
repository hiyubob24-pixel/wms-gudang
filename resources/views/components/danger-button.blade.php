<button {{ $attributes->merge(['type' => 'submit', 'class' => 'app-danger-button focus:outline-none focus:ring-2 focus:ring-rose-300 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
