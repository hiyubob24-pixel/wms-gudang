<button {{ $attributes->merge(['type' => 'submit', 'class' => 'app-primary-button focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
