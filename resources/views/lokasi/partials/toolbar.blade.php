<div class="row align-items-center mb-3">
    <div class="col-md-7 d-flex align-items-center gap-2 flex-wrap">
        @can('manage lokasi')
            <x-tombol-tambah label="Tambah Lokasi" href="{{ route('lokasi.create') }}" />
        @endcan

        <form id="sortForm" action="{{ route('lokasi.index') }}" method="GET" class="d-inline-block">
            <input type="hidden" name="sort" id="sortInput" value="{{ request('sort', 'desc') }}">
            <button type="button" id="sortToggle" class="sort-toggle">
                {{ request('sort') == 'asc' ? '⬆️' : '⬇️' }}
            </button>
        </form>
    </div>

    <div class="col-md-5 d-flex justify-content-md-end mt-2 mt-md-0">
        <div style="width: 100%; max-width: 350px;">
            <x-form-search placeholder="Cari nama lokasi..." />
        </div>
    </div>
</div>

<script>
document.getElementById('sortToggle').addEventListener('click', function() {
    const sortInput = document.getElementById('sortInput');
    sortInput.value = sortInput.value === 'asc' ? 'desc' : 'asc';
    document.getElementById('sortForm').submit();
});
</script>
