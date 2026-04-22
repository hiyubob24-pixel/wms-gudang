<x-app-layout>
    <x-slot name="header">Edit Produk</x-slot>
    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-slate-900 p-4 shadow-sm dark:shadow-none sm:p-6">
                <form method="POST" action="{{ route('products.update', $product) }}">
                    @csrf @method('PUT')
                    <div class="mb-4"><label class="mb-2 block text-sm font-medium text-gray-700 dark:text-slate-300">Nama Produk</label><input type="text" name="name" value="{{ $product->name }}" class="w-full rounded border p-2" required></div>
                    <div class="mb-4"><label class="mb-2 block text-sm font-medium text-gray-700 dark:text-slate-300">SKU</label><input type="text" name="sku" value="{{ $product->sku }}" class="w-full rounded border p-2"></div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white">Update</button>
                        <a href="{{ route('products.index') }}" class="text-center text-sm text-gray-600 dark:text-slate-400 hover:underline sm:text-left">Kembali ke daftar produk</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
