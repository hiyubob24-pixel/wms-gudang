<x-app-layout>
    <x-slot name="header">Edit Rak</x-slot>
    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-slate-900 p-4 shadow sm:rounded-lg sm:p-6">
                <form method="POST" action="{{ route('raks.update', $rak) }}">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Nomor Rak</label>
                        <input type="text" name="nomor_rak" value="{{ old('nomor_rak', $rak->nomor_rak) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Tingkat</label>
                        <input type="text" name="tingkat" value="{{ old('tingkat', $rak->tingkat) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Bagian / Zona</label>
                        <input type="text" name="bagian" value="{{ old('bagian', $rak->bagian) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Kapasitas Maksimal (Qty)</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $rak->capacity) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required min="1">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Nama Alternatif</label>
                        <input type="text" name="name" value="{{ old('name', $rak->name) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200">
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2 text-sm font-bold text-white shadow transition hover:bg-blue-700">Update Rak</button>
                        <a href="{{ route('raks.index') }}" class="text-center text-sm text-gray-600 dark:text-slate-400 hover:underline sm:text-left">Kembali ke daftar rak</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
