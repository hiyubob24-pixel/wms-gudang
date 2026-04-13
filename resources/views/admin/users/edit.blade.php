<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit User: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Nama</label>
                        <input type="text" name="name" class="w-full border rounded p-2" required value="{{ old('name', $user->name) }}">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Email</label>
                        <input type="email" name="email" class="w-full border rounded p-2" required value="{{ old('email', $user->email) }}">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="w-full border rounded p-2">
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Role</label>
                        <select name="role" class="w-full border rounded p-2" required>
                            <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff Gudang</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                        @error('role') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update User</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>