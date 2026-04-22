@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'app-inline-status app-fade-up text-sm']) }}>
        <div class="app-inline-status__icon" aria-hidden="true">i</div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-emerald-800">Informasi akun</p>
            <p class="mt-1 text-sm leading-6 text-emerald-700 dark:text-emerald-300">{{ $status }}</p>
        </div>
    </div>
@endif
