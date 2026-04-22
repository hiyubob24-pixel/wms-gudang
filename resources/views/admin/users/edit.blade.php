<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">Edit User: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="app-form-card p-6 sm:p-8">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <label class="app-field-label" for="name">Nama Lengkap</label>
                            <input type="text" name="name" id="name"
                                class="app-field-input {{ $errors->has('name') ? 'app-field-error' : '' }}"
                                required value="{{ old('name', $user->name) }}"
                                placeholder="Masukkan nama lengkap">
                            @error('name') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="app-field-label" for="email">Alamat Email</label>
                            <input type="email" name="email" id="email"
                                class="app-field-input {{ $errors->has('email') ? 'app-field-error' : '' }}"
                                required value="{{ old('email', $user->email) }}"
                                placeholder="contoh@email.com">
                            @error('email') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="app-field-label" for="password">Password <span class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span></label>
                            <input type="password" name="password" id="password"
                                class="app-field-input {{ $errors->has('password') ? 'app-field-error' : '' }}"
                                placeholder="Isi hanya jika ingin mengganti password">
                            @error('password') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="app-field-label" for="role">Role / Hak Akses</label>
                            <div class="app-field-select-wrap">
                                <select name="role" id="role" class="app-field-select" required>
                                    <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff Gudang</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                            </div>
                            @error('role') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('users.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            Kembali
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-700 active:scale-95">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
