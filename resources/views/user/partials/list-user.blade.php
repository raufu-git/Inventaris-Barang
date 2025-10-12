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
    /* ===== STYLE KHUSUS UNTUK SORT SELECT ===== */
.sort-select {
    height: 34px;
    padding: 6px 12px;
    font-size: 13.5px;
    border-radius: 8px;
    border: 1px solid #a3d2a1;
    background: linear-gradient(145deg, #f2fdf2, #d9f7d9);
    color: #155724;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg fill='%23155724' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    padding-right: 32px;
}

.sort-select:hover {
    background: linear-gradient(145deg, #e3f9e3, #c9f1c9);
    box-shadow: 0 0 6px rgba(25, 135, 84, 0.25);
    border-color: #198754;
}

.sort-select:focus {
    background: #eafcea;
    box-shadow: 0 0 8px rgba(25, 135, 84, 0.4);
    border-color: #198754;
}

/* Style dropdown list (Firefox & Chrome yang support) */
.sort-select option {
    background: #f0fff0;
    color: #155724;
    padding: 10px;
    font-size: 13.5px;
}
</style>