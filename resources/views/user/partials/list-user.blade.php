<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>&nbsp;</th>
        </tr>
    </x-slot>

    @forelse ($users as $index => $user)
        @php
            [$role] = $user->getRoleNames();
        @endphp
        <tr>
            <td>{{ $users->firstItem() + $index }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <span class="badge bg-primary">{{ ucwords($role) }}</span>
            </td>
            <td>
                <x-tombol-aksi href="{{ route('user.edit', $user->id) }}" type="edit" />
                <x-tombol-aksi href="{{ route('user.destroy', $user->id) }}" type="delete" />
            </td> 
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">
                <div class="alert alert-danger">
                    Data user belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>

<style>
 /* 🌿 Soft Sort Toggle Button */
.sort-toggle {
    background: linear-gradient(145deg, #f2fdf2, #d9f7d9);
    color: #155724;
    border: 1px solid #a3d2a1;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.sort-toggle:hover {
    background: linear-gradient(145deg, #e3f9e3, #c9f1c9);
    box-shadow: 0 0 6px rgba(25, 135, 84, 0.25);
    border-color: #198754;
    transform: scale(1.02);
}

.sort-toggle:active {
    background: #eafcea;
    transform: scale(0.98);
}
</style>