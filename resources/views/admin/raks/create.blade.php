<x-app-layout>
    <x-slot name="header">Tambah Rak Baru</x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('raks.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nomor Rak (wajib)</label>
                        <input type="text" name="nomor_rak" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Tingkat (wajib)</label>
                        <input type="text" name="tingkat" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required placeholder="Contoh: Tingkat 1, Level A">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Bagian / Zona (wajib)</label>
                        <input type="text" name="bagian" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required placeholder="Contoh: Gudang Utara, Zona B">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Kapasitas Maksimal (Qty)</label>
                        <input type="number" name="capacity" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required min="1" placeholder="Contoh: 500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Alternatif (opsional)</label>
                        <input type="text" name="name" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" placeholder="Isi jika ingin nama berbeda">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded shadow">Simpan Rak</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>