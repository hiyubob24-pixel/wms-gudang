<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Rak</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('raks.create') }}" class="bg-green-500 text-white px-4 py-2 rounded inline-block mb-4">+ Tambah Rak</a>

                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-2 mb-4">{{ session('success') }}</div>
                @endif

                <table class="min-w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">No. Rak</th>
                            <th class="border px-4 py-2">Tingkat</th>
                            <th class="border px-4 py-2">Bagian/Zona</th>
                            <th class="border px-4 py-2">Nama Alternatif</th>
                            <th class="border px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($raks as $rak)
                        <tr>
                            <td class="border px-4 py-2">{{ $rak->nomor_rak }}</td>
                            <td class="border px-4 py-2">{{ $rak->tingkat }}</td>
                            <td class="border px-4 py-2">{{ $rak->bagian }}</td>
                            <td class="border px-4 py-2">{{ $rak->name }}</td>
                            <td class="border px-4 py-2">
                                <a href="{{ route('raks.edit', $rak) }}" class="text-blue-500">Edit</a>
                                <form method="POST" action="{{ route('raks.destroy', $rak) }}" class="inline" onsubmit="return confirm('Yakin hapus rak ini?')">
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
    </div>
</x-app-layout>