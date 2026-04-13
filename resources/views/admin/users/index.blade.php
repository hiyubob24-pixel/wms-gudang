<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen User</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('users.create') }}" class="bg-green-500 text-white px-4 py-2 rounded inline-block mb-4">+ Tambah User</a>

                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-2 mb-4">{{ session('success') }}</div>
                @endif

                <table class="min-w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">Nama</th>
                            <th class="border px-4 py-2">Email</th>
                            <th class="border px-4 py-2">Role</th>
                            <th class="border px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="border px-4 py-2">{{ $user->name }}</td>
                            <td class="border px-4 py-2">{{ $user->email }}</td>
                            <td class="border px-4 py-2">
                                <span class="px-2 py-1 rounded text-white {{ $user->role == 'admin' ? 'bg-red-500' : 'bg-blue-500' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="border px-4 py-2">
                                <a href="{{ route('users.edit', $user) }}" class="text-blue-500 hover:underline">Edit</a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 ml-2 hover:underline">Hapus</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>