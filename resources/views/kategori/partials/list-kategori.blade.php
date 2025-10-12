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