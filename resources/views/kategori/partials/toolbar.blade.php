<div class="row align-items-center mb-3">
    <div class="col-md-7 d-flex align-items-center gap-2 flex-wrap">
        @can('manage kategori')
            <x-tombol-tambah label="Tambah Kategori" href="{{ route('kategori.create') }}" />
        @endcan

        <form action="{{ route('kategori.index') }}" method="GET" class="d-inline-block">
            <select name="sort" class="sort-select" onchange="this.form.submit()">
                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>⬇️ Terbaru → Lama</option>
                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>⬆️ Lama → Terbaru</option>
            </select>
        </form>
    </div>

    <div class="col-md-5 d-flex justify-content-md-end mt-2 mt-md-0">
        <div style="width: 100%; max-width: 350px;">
            <x-form-search placeholder="Cari nama kategori..." />
        </div>
    </div>
</div>
