<div class="row">
    {{-- 📋 Form Input (kiri) --}}
    <div class="col-md-6">
        {{-- 🔍 Pencarian Barang --}}
        <div class="col-md-12 position-relative mb-3">
            <label for="barang_nama" class="form-label fw-bold">Cari Barang</label>
            <div class="position-relative">
                <input type="text" id="barang_nama" class="form-control pe-4" placeholder="Ketik nama barang..." autocomplete="off" required>
                <input type="hidden" id="barang_id" name="barang_id">
                <input type="hidden" id="kondisi_awal" name="kondisi_awal">
                <button type="button" id="clear-barang" class="btn btn-sm btn-light position-absolute top-50 end-0 translate-middle-y me-2" style="display:none;">&times;</button>
                <div id="loading-spinner" class="position-absolute top-50 end-0 translate-middle-y me-4" style="display:none;">
                    <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                </div>
            </div>
            <ul id="daftar-barang" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto;"></ul>
        </div>

        {{-- 👤 Nama, HP, Kelas, Jumlah --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <x-form-input label="Nama Peminjam" name="nama_peminjam" :value="old('nama_peminjam', $peminjaman->nama_peminjam ?? '')" />
            </div>
            <div class="col-md-6">
                <x-form-input 
                    label="Nomor HP" 
                    name="no_hp" 
                    type="tel" 
                    placeholder="Masukkan nomor HP" 
                    :value="old('nomor_hp', $peminjaman->nomor_hp ?? '')" 
                    />
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <x-form-input label="Kelas / Divisi" name="kelas_divisi" type="text" :value="old('kelas_divisi', $peminjaman->kelas_divisi ?? '')" placeholder="Misal: XII RPL 1 / Tata Usaha" />
            </div>
            <div class="col-md-6">
                <x-form-input label="Jumlah Dipinjam" name="jumlah_pinjam" type="number" min="1" :value="old('jumlah_pinjam', $peminjaman->jumlah_pinjam ?? 1)" />
            </div>
        </div>

        {{-- 📅 Tanggal --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <x-form-input label="Tanggal Pinjam" name="tanggal_pinjam" type="date" :value="old('tanggal_pinjam', $peminjaman->tanggal_pinjam ?? '')" />
            </div>
            <div class="col-md-6">
                <x-form-input label="Tanggal Kembali" name="tanggal_kembali" type="date" :value="old('tanggal_kembali', $peminjaman->tanggal_kembali ?? '')" />
            </div>
        </div>

        {{-- 📝 Keterangan --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <x-form-input label="Keterangan" name="keterangan" :value="old('keterangan', $peminjaman->keterangan ?? '')" />
            </div>
        </div>

        {{-- 🟦 Tombol --}}
        <div class="mt-4">
            <x-primary-button>{{ isset($update) ? __('Update') : __('Simpan') }}</x-primary-button>
            <x-tombol-kembali :href="route('peminjaman.index')" />
        </div>
    </div>

    {{-- 🧾 Info Barang (kanan) --}}
    <div class="col-md-4">
        <div id="info-barang" class="border rounded p-3 bg-light shadow-sm h-100" style="display:none;">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-box-seam"></i> Info Barang</h6>
            <p class="mb-2"><b>Nama:</b> <span id="info_nama">-</span></p>
            <p class="mb-2"><b>Kategori:</b> <span id="info_kategori"></span></p>
            <p class="mb-2"><b>Lokasi:</b> <span id="info_lokasi"></span></p>
            <p class="mb-2"><b>Stok:</b> <span id="info_jumlah">-</span></p>
            <p class="mb-2"><b>Kondisi:</b> <span id="info_kondisi"></span></p>
            <p class="mb-2"><b>Sumber Dana:</b> <span id="info_sumber_dana"></span></p>
            <p class="mb-2"><b>Tanggal Pengadaan:</b> <span id="info_pengadaan"></span></p>
            <p class="mb-2"><b>Terakhir Diperbarui:</b> <span id="info_update"></span></p>
            <p class="mb-2"><b>Frekuensi Perawatan:</b> <span id="info_frekuensi"></span></p>
            <p class="mb-2"><b>Perawatan Selanjutnya:</b> <span id="info_selanjutnya"></span></p>
        </div>
    </div>
</div>


<!-- STYLEs -->
<style>
#info-barang {
    display: none;
    background-color: #f8f9fa;
    border-radius: 1rem;
    padding: 1rem;
    min-width: 320px;
    max-width: fit-content;
    white-space: normal;
    word-break: break-word;
    overflow: hidden; /* 🧩 Biar isi di dalam card gak keluar radius */
}
</style>

{{-- 🔽 Scripts --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    const $input = $('#barang_nama');
    const $hidden = $('#barang_id');
    const $list = $('#daftar-barang');
    const $clear = $('#clear-barang');
    const $spinner = $('#loading-spinner');
    

    // 🔍 Pencarian manual tanpa Select2
    $input.on('keyup', function() {
        const query = $(this).val().trim();
        if (query.length < 1) {
            $list.hide();
            return;
        }

        $spinner.show(); // ⏳ tampilkan loading

        $.ajax({
            url: '{{ route("barang.search") }}',
            data: { q: query },
            dataType: 'json',
            success: function(data) {
                $spinner.hide(); // sembunyikan spinner
                $list.empty();

                if (data.length > 0) {
                    data.forEach(item => {
                        $list.append(`
                            <li class="list-group-item list-group-item-action" data-id="${item.id}" style="cursor:pointer;">
                                <b>${item.nama_barang}</b>
                                <small class="d-block text-muted">${item.kategori?.nama_kategori || '-'}</small>
                            </li>
                        `);
                    });
                } else {
                    // ⚠️ kalau gak ada hasil
                    $list.append(`
                        <li class="list-group-item text-muted text-center fst-italic" style="cursor:default;">
                            Tidak ditemukan barang yang cocok 
                        </li>
                    `);
                }
                $list.show();
            },
            error: function() {
                $spinner.hide();
                $list.empty().append(`
                    <li class="list-group-item text-danger text-center fst-italic">
                        Terjadi kesalahan saat memuat data 
                    </li>
                `).show();
            }
        });
    });

    // 🖱 Klik hasil pencarian
    $list.on('click', 'li[data-id]', function() {
        const id = $(this).data('id');
        const nama = $(this).find('b').text().trim();

        $input.val(nama);
        $hidden.val(parseInt(id)); // biar dikirim sebagai integer  
        $list.hide();
        $clear.show();

        // 🔁 Ambil detail barang
        $.ajax({
            url: '{{ route("barang.search") }}',
            dataType: 'json',
            success: function(data) {
                const selected = data.find(x => x.id == id);
                if (selected) {
                    const e = { params: { data: {
                        id: selected.id,
                        text: selected.nama_barang,
                        kategori: selected.kategori?.nama_kategori,
                        lokasi: selected.lokasi?.nama_lokasi,
                        jumlah_barang: selected.jumlah_barang,
                        satuan: selected.satuan,
                        kondisi: selected.kondisi,
                        tanggal_pengadaan: selected.tanggal_pengadaan,
                        sumber_dana: selected.sumber_dana,
                        updated_at: selected.updated_at,
                        frekuensi_perawatan: selected.frekuensi_perawatan,
                        tanggal_perawatan_selanjutnya: selected.tanggal_perawatan_selanjutnya,
                    } }};

                    $('#barang_id').trigger({
                        type: 'select2:select',
                        params: e.params
                    });
                }
            }
        });
    });

    // ❌ Tombol clear
    $clear.on('click', function() {
        $input.val('');
        $hidden.val('');
        $clear.hide();
        $list.hide();
        $('#info-barang').fadeOut();
    });

    // Klik di luar daftar → tutup dropdown
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#barang_id, #daftar-barang').length) {
            $list.hide();
        }
    });

    // 📅 Fungsi bantu: format tanggal
    const formatTanggal = t => {
        if (!t) return '-';
        const date = new Date(t);
        return date.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
    };

    // 🧮 Fungsi bantu: hitung status perawatan
    const hitungStatusPerawatan = tanggal => {
        if (!tanggal) return '-';
        const now = new Date();
        const next = new Date(tanggal);
        const selisih = Math.floor((next - now) / (1000 * 60 * 60 * 24));
        let pesan = '', badgeClass = '';

        if (selisih === 0) {
            pesan = '⚠️ Jadwal perawatan hari ini!';
            badgeClass = 'bg-warning text-dark';
        } else if (selisih < 0) {
            const hari = Math.abs(selisih);
            const bulan = Math.floor(hari / 30);
            pesan = bulan > 0
                ? `⏰ Jadwal perawatan sudah lewat ${bulan} bulan (${hari} hari)!`
                : `⏰ Jadwal perawatan sudah lewat ${hari} hari!`;
            badgeClass = 'bg-danger';
        } else {
            const bulan = Math.floor(selisih / 30);
            pesan = bulan > 0
                ? `🕒 Jadwal perawatan akan tiba dalam ${bulan} bulan (${selisih} hari) lagi`
                : `🕒 Jadwal perawatan akan tiba dalam ${selisih} hari lagi`;
            badgeClass = 'bg-info text-dark';
        }
        return `<span class="badge ${badgeClass}">${pesan}</span>`;
    };

    // 🧠 Saat barang dipilih
    $('#barang_id').on('select2:select', function (e) {
        const data = e.params.data;

        // 🧾 Tampilkan semua info barang
        $('#info_nama').text(data.text || '-');
        $('#info_kategori').text(data.kategori || '-');
        $('#info_lokasi').text(data.lokasi || '-');
        $('#info_jumlah').text(`${data.jumlah_barang} ${data.satuan || ''}`);

        // 🟩 Kondisi + badge
        let badgeHTML = '';
        if (data.kondisi === 'Baik') {
            badgeHTML = '<span class="badge bg-info">'+data.kondisi+'</span>';
        } else if (data.kondisi === 'Rusak Ringan') {
            badgeHTML = '<span class="badge bg-warning text-dark">'+data.kondisi+'</span>';
        } else if (data.kondisi === 'Rusak Berat') {
            badgeHTML = '<span class="badge bg-danger">'+data.kondisi+'</span>';
        } else {
            badgeHTML = '<span class="badge bg-secondary">'+(data.kondisi || '-')+'</span>';
        }
        $('#info_kondisi').html(badgeHTML);
        $('#kondisi_awal').val(data.kondisi || '');
        $('#info_sumber_dana').text(data.sumber_dana || '-');
        $('#info_pengadaan').text(formatTanggal(data.tanggal_pengadaan));
        $('#info_update').text(formatTanggal(data.updated_at));
        $('#info_frekuensi').text(data.frekuensi_perawatan || '-');

        // 💡 Status perawatan dengan badge dinamis
        const perawatanHTML = hitungStatusPerawatan(data.tanggal_perawatan_selanjutnya);
        $('#info_selanjutnya').html(`${formatTanggal(data.tanggal_perawatan_selanjutnya)}<br>${perawatanHTML}`);

        $('#info-barang').fadeIn();
    });

    // ❌ Saat barang dihapus dari select2
    $('#barang_id').on('select2:clear', function() {
        $('#info-barang').fadeOut();
    });

    // 🧱 Kalau ada data existing (edit mode)
    @if(isset($peminjaman) && $peminjaman->barang)
        const existing = {
            id: '{{ $peminjaman->barang->id }}',
            text: '{{ $peminjaman->barang->nama_barang }}',
            kategori: '{{ $peminjaman->barang->kategori->nama_kategori ?? '-' }}',
            lokasi: '{{ $peminjaman->barang->lokasi->nama_lokasi ?? '-' }}',
            jumlah_barang: '{{ $peminjaman->barang->jumlah_barang }}',
            satuan: '{{ $peminjaman->barang->satuan ?? '' }}',
            kondisi: '{{ $peminjaman->barang->kondisi ?? '-' }}',
            tanggal_pengadaan: '{{ $peminjaman->barang->tanggal_pengadaan }}',
            sumber_dana:'{{ $peminjaman->barang->sumber_dana }}',
            updated_at: '{{ $peminjaman->barang->updated_at }}',
            frekuensi_perawatan: '{{ $peminjaman->barang->frekuensi_perawatan ?? '-' }}',
            tanggal_perawatan_selanjutnya: '{{ $peminjaman->barang->tanggal_perawatan_selanjutnya ?? '' }}',
        };

        $('#barang_nama').val(existing.text);
        $('#barang_id').val(existing.id);
        $('#clear-barang').show();

        $('#info_nama').text(existing.text);
        $('#info_kategori').text(existing.kategori);
        $('#info_lokasi').text(existing.lokasi);
        $('#info_jumlah').text(`${existing.jumlah_barang} ${existing.satuan}`);
        
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
        $('#info_sumber_dana').text(existing.sumber_dana || '-');
        $('#info_kondisi').html(badgeHTML);

        $('#info_pengadaan').text(formatTanggal(existing.tanggal_pengadaan));
        $('#info_update').text(formatTanggal(existing.updated_at));
        $('#info_frekuensi').text(existing.frekuensi_perawatan);
        $('#info_selanjutnya').html(`${formatTanggal(existing.tanggal_perawatan_selanjutnya)}<br>${hitungStatusPerawatan(existing.tanggal_perawatan_selanjutnya)}`);

        $('#info-barang').show();
    @endif
});
</script>
