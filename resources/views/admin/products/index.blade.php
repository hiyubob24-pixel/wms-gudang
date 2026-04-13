<x-app-layout>
    <x-slot name="header">Daftar Produk</x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('products.create') }}" class="bg-green-500 text-white px-4 py-2 rounded inline-block mb-4">+ Tambah Produk</a>
            @if(session('success')) <div class="bg-green-100 p-2 mb-2">{{ session('success') }}</div> @endif
            <table class="min-w-full bg-white border">
                <thead><tr><th>Nama</th><th>SKU</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="text-blue-500">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>