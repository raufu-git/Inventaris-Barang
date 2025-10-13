<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Nama Kategori</th>
            @can('manage kategori')
                <th>&nbsp;</th>
            @endcan
        </tr>
    </x-slot>

    @forelse ($kategoris as $index => $kategori)
        <tr>
            <td>{{ $kategoris->firstItem() + $index }}</td>
            <td>{{ $kategori->nama_kategori }}</td>
            @can('manage kategori')
                <td>
                    <x-tombol-aksi href="{{ route('kategori.edit', $kategori->id) }}" type="edit" />
                    <x-tombol-aksi href="{{ route('kategori.destroy', $kategori->id) }}" type="delete" />
                </td>
            @endcan
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center">
                <div class="alert alert-danger">
                    Data kategori belum tersedia.
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