<x-app-layout>
    <x-slot name="header">Tambah Rak Baru</x-slot>
    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-slate-900 p-4 shadow sm:rounded-lg sm:p-6">
                <form method="POST" action="{{ route('raks.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Nomor Rak (wajib)</label>
                        <input type="text" name="nomor_rak" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Tingkat (wajib)</label>
                        <input type="text" name="tingkat" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required placeholder="Contoh: Tingkat 1, Level A">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Bagian / Zona (wajib)</label>
                        <input type="text" name="bagian" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required placeholder="Contoh: Gudang Utara, Zona B">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Kapasitas Maksimal (Qty)</label>
                        <input type="number" name="capacity" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required min="1" placeholder="Contoh: 500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-slate-300 font-bold mb-2">Nama Alternatif (opsional)</label>
                        <input type="text" name="name" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" placeholder="Isi jika ingin nama berbeda">
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2 text-sm font-bold text-white shadow transition hover:bg-blue-700">Simpan Rak</button>
                        <a href="{{ route('raks.index') }}" class="text-center text-sm text-gray-600 dark:text-slate-400 hover:underline sm:text-left">Kembali ke daftar rak</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
