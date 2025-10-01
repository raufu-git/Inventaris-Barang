<div class="row">
    {{-- 📋 Form Input (kiri) --}}
    <div class="col-md-8">
        {{-- 🔍 Pencarian Barang --}}
        <div class="row g-3 align-items-center mb-3">
            <div class="col-md-12">
                <label for="barang_id" class="form-label fw-bold">Cari Barang</label>
                <select id="barang_id" name="barang_id" class="form-select select2-ajax" required>
                    @if(isset($peminjaman) && $peminjaman->barang)
                        <option value="{{ $peminjaman->barang->id }}" selected>
                            {{ $peminjaman->barang->nama_barang }}
                        </option>
                    @endif
                </select>
            </div>
        </div>

        {{-- 👤 Nama, Jumlah, Tanggal --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <x-form-input 
                    label="Nama Peminjam" 
                    name="nama_peminjam" 
                    :value="old('nama_peminjam', $peminjaman->nama_peminjam ?? '')" 
                />
            </div>

            <div class="col-md-6">
                <x-form-input 
                    label="Jumlah Dipinjam" 
                    name="jumlah_pinjam" 
                    type="number" 
                    min="1" 
                    :value="old('jumlah_pinjam', $peminjaman->jumlah_pinjam ?? 1)" 
                />
            </div>

            <div class="col-md-15">
                <x-form-input 
                    label="Tanggal Pinjam" 
                    name="tanggal_pinjam" 
                    type="date" 
                    :value="old('tanggal_pinjam', $peminjaman->tanggal_pinjam ?? '')" 
                />
            </div>
        </div>

        {{-- 📅 Tanggal Kembali & Keterangan --}}
        <div class="row mb-3 mt-2">
            <div class="col-md-15">
                <x-form-input 
                    label="Tanggal Kembali" 
                    name="tanggal_kembali" 
                    type="date" 
                    :value="old('tanggal_kembali', $peminjaman->tanggal_kembali ?? '')" 
                />
            </div>

            <div class="col-md-12">
                <x-form-input 
                    label="Keterangan" 
                    name="keterangan" 
                    :value="old('keterangan', $peminjaman->keterangan ?? '')" 
                />
            </div>
        </div>

        {{-- 🟦 Tombol --}}
        <div class="mt-4">
            <x-primary-button>
                {{ isset($update) ? __('Update') : __('Simpan') }}
            </x-primary-button>
            <x-tombol-kembali :href="route('peminjaman.index')" />
        </div>
    </div>

    {{-- 🧾 Info Barang (kanan) --}}
    <div class="col-md-4">
        <div id="info-barang" class="border rounded p-3 bg-light shadow-sm h-100" style="display:none;">
            <h6 class="fw-bold mb-3 text-primary">
                <i class="bi bi-box-seam"></i> Info Barang
            </h6>
            <p class="mb-2"><b>Nama:</b> <span id="info_nama">-</span></p>
            <p class="mb-2"><b>Stok:</b> <span id="info_stok">-</span></p>
            <p class="mb-0"><b>Kondisi:</b> <span id="info_kondisi"></span></p>
        </div>
    </div>
</div>


{{-- 🔽 Scripts --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('#barang_id').select2({
        placeholder: 'Ketik nama barang...',
        allowClear: true,
        ajax: {
            url: '{{ route("barang.search") }}',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.nama_barang,
                        stok: item.jumlah_barang,
                        kondisi: item.kondisi
                    }))
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });

    // Kalau ada value sebelumnya (mode edit), tampilkan info-nya langsung
    @if(isset($peminjaman) && $peminjaman->barang)
        const existing = {
            text: '{{ $peminjaman->barang->nama_barang }}',
            stok: '{{ $peminjaman->barang->jumlah_barang }}',
            kondisi: '{{ $peminjaman->barang->kondisi }}'
        };

        $('#info_nama').text(existing.text);
        $('#info_stok').text(existing.stok);

        let badgeHTML = '';
        if (existing.kondisi === 'Baik') {
            badgeHTML = '<span class="badge bg-info">'+existing.kondisi+'</span>';
        } else if (existing.kondisi === 'Rusak Ringan') {
            badgeHTML = '<span class="badge bg-warning text-dark">'+existing.kondisi+'</span>';
        } else if (existing.kondisi === 'Rusak Berat') {
            badgeHTML = '<span class="badge bg-danger">'+existing.kondisi+'</span>';
        } else {
            badgeHTML = '<span class="badge bg-secondary">'+existing.kondisi+'</span>';
        }

        $('#info_kondisi').html(badgeHTML);
        $('#info-barang').show();
    @endif

    // 🧠 Saat barang dipilih
    $('#barang_id').on('select2:select', function (e) {
        const data = e.params.data;
        $('#info_nama').text(data.text);
        $('#info_stok').text(data.stok);

        // 🟩 Badge warna seperti di tabel
        let badgeHTML = '';
        if (data.kondisi === 'Baik') {
            badgeHTML = '<span class="badge bg-info">'+data.kondisi+'</span>';
        } else if (data.kondisi === 'Rusak Ringan') {
            badgeHTML = '<span class="badge bg-warning text-dark">'+data.kondisi+'</span>';
        } else if (data.kondisi === 'Rusak Berat') {
            badgeHTML = '<span class="badge bg-danger">'+data.kondisi+'</span>';
        } else {
            badgeHTML = '<span class="badge bg-secondary">'+data.kondisi+'</span>';
        }

        $('#info_kondisi').html(badgeHTML);
        $('#info-barang').fadeIn();
    });

    // ❌ Saat barang dihapus dari select2
    $('#barang_id').on('select2:clear', function() {
        $('#info-barang').fadeOut();
    });
});
</script>