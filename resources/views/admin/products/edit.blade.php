<x-app-layout>
    <x-slot name="header">Edit Produk</x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6">
                <form method="POST" action="{{ route('products.update', $product) }}">
                    @csrf @method('PUT')
                    <div class="mb-4"><label>Nama Produk</label><input type="text" name="name" value="{{ $product->name }}" class="w-full border p-2" required></div>
                    <div class="mb-4"><label>SKU</label><input type="text" name="sku" value="{{ $product->sku }}" class="w-full border p-2"></div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2">Update</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>