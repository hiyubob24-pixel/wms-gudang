<x-app-layout>
    <x-slot name="header">Tambah Produk</x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6">
                <form method="POST" action="{{ route('products.store') }}">
                    @csrf
                    <div class="mb-4"><label>Nama Produk</label><input type="text" name="name" class="w-full border p-2" required></div>
                    <div class="mb-4"><label>SKU (opsional)</label><input type="text" name="sku" class="w-full border p-2"></div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>