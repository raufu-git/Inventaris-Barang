<div class="row align-items-center no-wrap-toolbar mb-3">
    <div class="col-md-8 d-flex align-items-center gap-2 flex-wrap">
        <x-tombol-tambah label="Tambah Peminjaman" href="{{ route('peminjaman.create') }}" />
        <x-tombol-cetak label="Cetak Laporan Peminjaman" href="{{ route('peminjaman.laporan') }}" />

        <!-- Tombol Sort -->
        <form id="sortForm" action="{{ route('peminjaman.index') }}" method="GET" class="d-inline-block">
            <input type="hidden" name="sort" id="sortInput" value="{{ request('sort', 'desc') }}">
            <button type="button" id="sortToggle" class="btn btn-light border btn-sm sort-toggle">
                @if(request('sort') == 'asc')
                    <i class="bi bi-sort-down-alt"></i> Lama
                @else
                    <i class="bi bi-sort-down"></i> Baru
                @endif
            </button>
        </form>

        <!-- Filter Status -->
        <form action="{{ route('peminjaman.index') }}" method="GET" class="d-inline-block">
            <select name="status" class="filter-status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                <option value="Hilang" {{ request('status') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
            </select>

            {{-- biar filter status tetep sinkron sama sort & search --}}
            <input type="hidden" name="sort" value="{{ request('sort', 'desc') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
        </form>
    </div>

    <div class="col-md-4 d-flex justify-content-md-end">
        <div style="width: 100%; max-width: 350px;">
            <x-form-search placeholder="Cari nama peminjam / Barang..." />
        </div>
    </div>
</div>

<!-- JS Sort -->
<script>
document.getElementById('sortToggle').addEventListener('click', function() {
    const sortInput = document.getElementById('sortInput');
    sortInput.value = sortInput.value === 'asc' ? 'desc' : 'asc';
    document.getElementById('sortForm').submit();
});
</script>
