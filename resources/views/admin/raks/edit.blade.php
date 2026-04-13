<x-app-layout>
    <x-slot name="header">Edit Rak</x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('raks.update', $rak) }}">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nomor Rak</label>
                        <input type="text" name="nomor_rak" value="{{ old('nomor_rak', $rak->nomor_rak) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Tingkat</label>
                        <input type="text" name="tingkat" value="{{ old('tingkat', $rak->tingkat) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Bagian / Zona</label>
                        <input type="text" name="bagian" value="{{ old('bagian', $rak->bagian) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Kapasitas Maksimal (Qty)</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $rak->capacity) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required min="1">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Alternatif</label>
                        <input type="text" name="name" value="{{ old('name', $rak->name) }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded shadow">Update Rak</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>