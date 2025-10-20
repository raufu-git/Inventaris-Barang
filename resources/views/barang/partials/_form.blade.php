@csrf
<div class="row mb-3">
    <div class="col-md-6">
        <x-form-input label="Kode Barang" name="kode_barang" :value="$barang->kode_barang" />
    </div>
    <div class="col-md-6">
        <x-form-input label="Nama Barang" name="nama_barang" :value="$barang->nama_barang" />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <x-form-select 
            label="Kategori" 
            name="kategori_id" 
            :value="$barang->kategori_id"
            :option-data="$kategori" 
            option-label="nama_kategori" 
            option-value="id" 
        />
    </div>
    <div class="col-md-6">
        <x-form-select 
            label="Lokasi" 
            name="lokasi_id" 
            :value="$barang->lokasi_id"
            :option-data="$lokasi" 
            option-label="nama_lokasi" 
            option-value="id" 
        />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <x-form-input label="Satuan" name="satuan" :value="$barang->satuan" />
    </div>
    <div class="col-md-6">
        @php
            $tanggal = $barang->tanggal_pengadaan 
                ? date('Y-m-d', strtotime($barang->tanggal_pengadaan)) 
                : null;
        @endphp
        <x-form-input 
            label="Tanggal Pengadaan" 
            name="tanggal_pengadaan" 
            type="date" 
            id="tanggal_pengadaan"
            :value="$tanggal" 
            max="{{ date('Y-m-d') }}"
        />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <x-form-input 
            label="Jumlah Kondisi Baik" 
            name="jumlah_baik" 
            type="number" 
            min="0" 
            :value="old('jumlah_baik', $jumlahBaik ?? 0)" 
        />
    </div>
    <div class="col-md-4">
        <x-form-input 
            label="Jumlah Rusak Ringan" 
            name="jumlah_rusak_ringan" 
            type="number" 
            min="0" 
            :value="old('jumlah_rusak_ringan', $jumlahRusakRingan ?? 0)" 
        />
    </div>
    <div class="col-md-4">
        <x-form-input 
            label="Jumlah Rusak Berat" 
            name="jumlah_rusak_berat" 
            type="number" 
            min="0" 
            :value="old('jumlah_rusak_berat', $jumlahRusakBerat ?? 0)" 
        />
    </div>
</div>

@php
    $frekuensiOptions = [
        ['value' => '', 'label' => '- Tidak Butuh Perawatan -'],
        ['value' => '1 bulan sekali', 'label' => '1 Bulan Sekali'],
        ['value' => '2 bulan sekali', 'label' => '2 Bulan Sekali'],
        ['value' => '3 bulan sekali', 'label' => '3 Bulan Sekali'],
        ['value' => '6 bulan sekali', 'label' => '6 Bulan Sekali'],
        ['value' => '12 bulan sekali', 'label' => '1 Tahun Sekali'],
        ['value' => 'lainnya', 'label' => 'Lainnya'],
    ];
@endphp

<div class="row mb-3">
    <div class="col-md-6">
        <x-form-select 
            label="Frekuensi Perawatan" 
            name="frekuensi_perawatan" 
            :value="$barang->frekuensi_perawatan"
            :option-data="$frekuensiOptions"
            option-label="label"
            option-value="value"
            id="frekuensi_perawatan"
        />
    </div>
    <div class="col-md-6">
        <x-form-input 
            label="Tanggal Perawatan Selanjutnya" 
            name="tanggal_perawatan_selanjutnya" 
            type="date" 
            :value="old('tanggal_perawatan_selanjutnya', $barang->tanggal_perawatan_selanjutnya)" 
            readonly
            id="tanggal_perawatan_selanjutnya"
        />
        <div id="status-perawatan" class="mt-2"></div>
    </div>
</div>

<div class="row mb-3" id="custom-frekuensi-container" style="display: none;">
    <div class="col-md-6">
        <x-form-input 
            label="Tulis Frekuensi Sendiri (misal: setiap 10 hari, 2 minggu, dst)" 
            name="custom_frekuensi"
            id="custom_frekuensi" 
            :value="old('custom_frekuensi', $barang->frekuensi_perawatan)" 
            placeholder="Opsional"
        />
    </div>
</div>

@php
    $sumberDana = [
        ['value' => 'Pemerintah', 'label' => 'Pemerintah'],
        ['value' => 'Swadaya', 'label' => 'Swadaya'],
        ['value' => 'Donatur', 'label' => 'Donatur'],
    ];
@endphp

<div class="row mb-3">
    <div class="col-md-6">
        <x-form-select 
            label="Sumber Dana" 
            name="sumber_dana" 
            :value="old('sumber_dana', $barang->sumber_dana ?? '')"
            :option-data="$sumberDana"
            option-label="label" 
            option-value="value" 
        />
    </div>
    <div class="col-md-6">
        <x-form-input label="Gambar Barang" name="gambar" type="file" />
    </div>
</div>

<div class="mt-4">
    <x-primary-button>
        {{ isset($update) ? __('Update') : __('Simpan') }}
    </x-primary-button>
    <x-tombol-kembali :href="route('barang.index')" />
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const jumlah = document.querySelector('input[name="jumlah_barang"]');
  const cBaik  = document.querySelector('input[name="count_baik"]');
  const cRing  = document.querySelector('input[name="count_ringan"]');
  const cBerat = document.querySelector('input[name="count_berat"]');
  const warnId = document.getElementById('distWarning-new') || document.getElementById('distWarning-{{ $barang->id ?? 'new' }}');
  const submitBtn = document.querySelector('button[type="submit"]');

  function checkSum() {
    const total = (Number(cBaik?.value || 0) + Number(cRing?.value || 0) + Number(cBerat?.value || 0));
    if (jumlah && jumlah.value && total !== 0 && Number(jumlah.value) !== total) {
      warnId.style.display = 'block';
      submitBtn.disabled = true;
    } else {
      warnId.style.display = 'none';
      submitBtn.disabled = false;
    }
  }

  [jumlah, cBaik, cRing, cBerat].forEach(e => e && e.addEventListener('input', checkSum));
  checkSum();
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById('frekuensi_perawatan');
    const customContainer = document.getElementById('custom-frekuensi-container');
    const tanggalPengadaanInput = document.getElementById('tanggal_pengadaan');
    const tanggalPerawatanInput = document.getElementById('tanggal_perawatan_selanjutnya');
    const customInput = document.getElementById('custom_frekuensi');
    const statusDiv = document.getElementById('status-perawatan');

    tanggalPerawatanInput.readOnly = true;

    function parseDate(str) {
        if (!str) return null;
        const [y, m, d] = str.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    function formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function tambahWaktu(date, jumlah, satuan) {
        switch (satuan) {
            case 'hari': date.setDate(date.getDate() + jumlah); break;
            case 'minggu': date.setDate(date.getDate() + jumlah * 7); break;
            case 'bulan': date.setMonth(date.getMonth() + jumlah); break;
            case 'tahun': date.setFullYear(date.getFullYear() + jumlah); break;
        }
    }

    function hitungTanggalPerawatan() {
        const tanggalPengadaan = parseDate(tanggalPengadaanInput.value);
        const tanggalPerawatanTersimpan = parseDate(tanggalPerawatanInput.value);
        const frekuensi = select.value;

        // ✅ Kalau tanggal perawatan tersimpan sudah ada, jangan hitung ulang
        //    kecuali user mengganti frekuensi atau tanggal pengadaan secara manual
        if (tanggalPerawatanTersimpan && 
            (!tanggalPengadaanInput.dataset.changed && !select.dataset.changed && !customInput.dataset.changed)) {
            tampilkanBadge(tanggalPerawatanTersimpan);
            return;
        }

        if (!tanggalPengadaan || !frekuensi) {
            tanggalPerawatanInput.value = '';
            statusDiv.innerHTML = '';
            return;
        }

        let nextDate = new Date(tanggalPengadaan);

        if (frekuensi === '1 bulan sekali') tambahWaktu(nextDate, 1, 'bulan');
        else if (frekuensi === '2 bulan sekali') tambahWaktu(nextDate, 2, 'bulan');
        else if (frekuensi === '3 bulan sekali') tambahWaktu(nextDate, 3, 'bulan');
        else if (frekuensi === '6 bulan sekali') tambahWaktu(nextDate, 6, 'bulan');
        else if (frekuensi === '12 bulan sekali') tambahWaktu(nextDate, 12, 'bulan');
        else if (frekuensi === 'lainnya') {
            const teks = customInput.value.trim();
            const match = teks.match(/(\d+)\s*(hari|minggu|bulan|tahun)/i);
            if (match) {
                tambahWaktu(nextDate, parseInt(match[1]), match[2].toLowerCase());
            } else {
                tanggalPerawatanInput.value = '';
                statusDiv.innerHTML = '';
                return;
            }
        } else {
            tanggalPerawatanInput.value = '';
            statusDiv.innerHTML = '';
            return;
        }

        tanggalPerawatanInput.value = formatDate(nextDate);
        tampilkanBadge(nextDate);
    }

    function tampilkanBadge(nextDate) {
        if (!nextDate) return;
        const sekarang = new Date();
        sekarang.setHours(0, 0, 0, 0);
        nextDate.setHours(0, 0, 0, 0);

        const selisihHari = Math.floor((nextDate - sekarang) / (1000 * 60 * 60 * 24));
        let badge = '';

        if (isNaN(selisihHari)) {
            statusDiv.innerHTML = '';
            return;
        }

        // 🔧 Hitung bulan tapi dibulatkan ke bawah (tanpa desimal)
        const selisihBulan = Math.floor(selisihHari / 30);
        let keterangan = '';

        if (selisihHari > 0) {
            if (selisihHari >= 30) {
                keterangan = `${selisihBulan} bulan`;
            } else {
                keterangan = `${selisihHari} hari`;
            }
            badge = `<span class="badge bg-info text-dark">
                🕒 Jadwal perawatan akan tiba dalam ${keterangan} lagi
            </span>`;
        } else if (selisihHari === 0) {
            badge = `<span class="badge bg-warning text-dark">
                ⚠️ Jadwal perawatan hari ini!
            </span>`;
        } else {
            const bulanTerlambat = Math.floor(Math.abs(selisihHari) / 30);
            if (Math.abs(selisihHari) >= 30) {
                keterangan = `${bulanTerlambat} bulan`;
            } else {
                keterangan = `${Math.abs(selisihHari)} hari`;
            }
            badge = `<span class="badge bg-danger">
                ⏰ Jadwal perawatan sudah lewat ${keterangan}!
            </span>`;
        }

        statusDiv.innerHTML = badge;
    }

    function toggleCustomInput() {
        if (select.value === 'lainnya') {
            customContainer.style.display = 'block';
        } else {
            customContainer.style.display = 'none';
            customInput.value = '';
        }
        hitungTanggalPerawatan();
    }

    select.addEventListener('change', toggleCustomInput);
    tanggalPengadaanInput.addEventListener('change', hitungTanggalPerawatan);
    customInput.addEventListener('input', hitungTanggalPerawatan);

    toggleCustomInput();
    hitungTanggalPerawatan();
});
</script>
@endpush