<table class="table table-bordered table-striped">
  <tbody>
    <tr>
      <th style="width: 30%;">Kode Barang</th>
      <td>
        {{ $barang->kode_barang }}
        <button class="btn btn-sm btn-outline-primary ms-2  align-items-center gap-1"
                data-bs-toggle="modal"
                data-bs-target="#unitModal-{{ $barang->id }}">
          <i data-lucide="eye"></i> Lihat Unit
        </button>
      </td>
    </tr>
    <tr>
      <th>Nama Barang</th>
      <td>{{ $barang->nama_barang }}</td>
    </tr>
    <tr>
      <th>Kategori</th>
      <td>{{ $barang->kategori->nama_kategori }}</td>
    </tr>
    <tr>
      <th>Lokasi</th>
      <td>{{ $barang->lokasi->nama_lokasi }}</td>
    </tr>
    <tr>
      <th>Jumlah</th>
      <td>{{ $barang->jumlah_barang }} {{ $barang->satuan }}</td>
    </tr>
    <tr>
      <th>Sumber Dana</th>
      <td>{{ $barang->sumber_dana }}</td>
    </tr>
    <tr>
      <th>Kondisi</th>
        <td>
            @php
              $unitCounts = \App\Models\Unit::where('barang_id', $barang->id)
              ->select('kondisi', \DB::raw('COUNT(*) as total'))
              ->groupBy('kondisi')
              ->pluck('total', 'kondisi')
              ->toArray();

            $baik = $unitCounts['Baik'] ?? 0;
            $ringan = $unitCounts['Rusak Ringan'] ?? 0;
            $berat = $unitCounts['Rusak Berat'] ?? 0;
          @endphp

          <div class="d-flex flex-column gap-1">
            <span class="badge badge-mini bg-info text-dark">Baik: {{ $baik }}</span>
            <span class="badge badge-mini bg-warning text-dark">Rusak Ringan: {{ $ringan }}</span>
            <span class="badge badge-mini bg-danger text-white">Rusak Berat: {{ $berat }}</span>
          </div>
        </td>
    </tr>
    @if($barang->frekuensi_perawatan)
      <tr>
        <th>Frekuensi Perawatan</th>
        <td>{{ $barang->frekuensi_perawatan }}</td>
      </tr>
    @endif
    <tr>
      <th>Tanggal Pengadaan</th>
      <td>{{ \Carbon\Carbon::parse($barang->tanggal_pengadaan)->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
      <th>Terakhir Diperbarui</th>
      <td>{{ \Carbon\Carbon::parse($barang->updated_at)->translatedFormat('d F Y') }}</td>
    </tr>
  </tbody>
</table>

{{-- ===== Modal Daftar Unit ===== --}}
<div class="modal fade" id="unitModal-{{ $barang->id }}" tabindex="-1" aria-labelledby="unitModalLabel-{{ $barang->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i data-lucide="boxes"></i> Daftar Unit – {{ $barang->nama_barang }} ({{ $barang->kode_barang }})
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <input type="text" class="form-control w-50 searchUnitInput"
                 placeholder="Cari nomor belakang unit (misal: 4)"
                 data-barang="{{ $barang->id }}">
          <button class="btn btn-warning btn-sm d-flex align-items-center gap-1 ms-2"
                  data-bs-toggle="modal"
                  data-bs-target="#editBarangModal-{{ $barang->id }}">
            <i data-lucide="edit-3"></i> Edit / Hapus Unit
          </button>
        </div>

        {{-- tabel unit --}}
        <div style="max-height: 400px; overflow-y: auto;">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Kode Unit</th>
                <th>Kondisi</th>
                <th>Frekuensi Perawatan</th>
                <th>Tanggal Perawatan Selanjutnya</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="unitTableBody-{{ $barang->id }}">
              @forelse($barang->units as $i => $unit)
                @php
                  $badgeClass = match($unit->kondisi) {
                      'Rusak Ringan' => 'bg-warning text-dark',
                      'Rusak Berat' => 'bg-danger',
                      default => 'bg-success'
                  };
                  $next = $unit->tanggal_perawatan_selanjutnya
                      ? \Carbon\Carbon::parse($unit->tanggal_perawatan_selanjutnya)
                      : null;
                @endphp
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $unit->kode_unit }}</td>
                  <td><span class="badge {{ $badgeClass }}">{{ $unit->kondisi }}</span></td>
                  <td>{{ $unit->frekuensi_perawatan ?? $barang->frekuensi_perawatan ?? '-' }}</td>
                  <td>{{ $next ? $next->translatedFormat('d F Y') : '-' }}</td>
                  <td>
                    <form action="{{ route('unit.konfirmasiPerawatan', $unit->id) }}" method="POST">
                      @csrf
                      @if ($next && ($next->isPast() || $next->isToday()))
                        <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                          <i data-lucide="check-circle"></i> Konfirmasi
                        </button>
                      @else
                        <button class="btn btn-secondary btn-sm d-flex align-items-center gap-1" disabled>
                          <i data-lucide="clock"></i> Menunggu
                        </button>
                      @endif
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted">Belum ada unit untuk barang ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== Modal Edit Frekuensi & Kondisi Unit ===== --}}
<div class="modal fade" id="editBarangModal-{{ $barang->id }}" tabindex="-1" aria-labelledby="editBarangLabel-{{ $barang->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editForm-{{ $barang->id }}" action="{{ route('barang.updateFrekuensiKondisi', $barang->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center gap-2">
            <i data-lucide="settings"></i> Edit Frekuensi, Kondisi & Unit
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
        {{-- Edit Frekuensi --}}
        <div class="mb-3">
        <label class="form-label fw-semibold">Frekuensi Perawatan</label>
        <div class="input-group mb-2">
            <input type="text" 
                name="frekuensi_perawatan" 
                class="form-control"
                placeholder="contoh: 3 bulan">
            <select id="modeFrekuensi-{{ $unit->id }}" 
                    name="mode_frekuensi" 
                    class="form-select" 
                    style="max-width: 150px;">
            <option value="semua" 
                {{ old('mode_frekuensi') == 'semua' ? 'selected' : '' }}>
                Ubah Semua
            </option>
            <option value="custom" 
                {{ old('mode_frekuensi') == 'custom' ? 'selected' : '' }}>
                Custom
            </option>
            </select>
        </div>
        <small class="text-muted">Pilih mode ubah frekuensi perawatan.</small>
        </div>

        {{-- Custom nomor unit (hanya muncul jika mode custom dipilih) --}}
        <div class="mb-3" 
            id="customFrekuensiWrapper-{{ $unit->id }}" 
            style="display: none;">
        <label class="form-label fw-semibold">Nomor Unit (Custom)</label>
        <input type="text" 
                name="unit_nomor" 
                class="form-control"
                placeholder="Masukkan nomor unit (misal: 1, 4, 8)">
        <small class="text-muted">Pisahkan dengan koma, misal: 1, 4, 8, 26</small>
        </div>

          {{-- Edit Kondisi Unit --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Ubah Kondisi Unit Tertentu</label>
            <div class="input-group mb-2">
              <input type="text" name="unit_nomor" class="form-control"
                     placeholder="Masukkan nomor belakang unit (misal: 4, 5, 7)">
              <select name="kondisi" class="form-select">
                <option value="">-- Pilih Kondisi --</option>
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
              </select>
            </div>
            <small class="text-muted">Pisahkan beberapa nomor dengan koma, misal: 2, 3, 7</small>
          </div>

          {{-- Hapus Unit --}}
          <div class="mb-3">
            <label class="form-label fw-semibold text-danger d-flex align-items-center gap-1">
              <i data-lucide="trash-2"></i> Hapus Unit
            </label>
            <input type="text" name="hapus_nomor" class="form-control"
                   placeholder="Masukkan nomor belakang unit (misal: 10, 11)">
            <small class="text-muted">Unit yang dihapus tidak bisa dikembalikan.</small>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary d-flex align-items-center gap-1" data-bs-dismiss="modal">
            <i data-lucide="x-circle"></i> Batal
          </button>
          <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-lucide="save"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Search unit untuk semua barang
    document.querySelectorAll('.searchUnitInput').forEach(input => {
        input.addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            const barangId = this.dataset.barang;
            const rows = document.querySelectorAll(`#unitTableBody-${barangId} tr`);
            let found = false;

            rows.forEach(row => {
                const kodeUnit = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const belakang = kodeUnit.split('-').pop();
                if (belakang.includes(keyword) || keyword === '') {
                    row.style.display = '';
                    found = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Jika tidak ada yang ketemu
            const tbody = document.querySelector(`#unitTableBody-${barangId}`);
            const existingMsgRow = tbody.querySelector('.no-result-row');
            if (!found) {
                if (!existingMsgRow) {
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr class="no-result-row">
                            <td colspan="6" class="text-center text-muted">
                                Unit dengan nomor belakang <strong>${keyword}</strong> tidak ditemukan
                            </td>
                        </tr>
                    `);
                }
            } else {
                if (existingMsgRow) existingMsgRow.remove();
            }
        });
    });

    // AJAX edit form untuk semua barang
    document.querySelectorAll('form[id^="editForm-"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formEl = this;
            $.ajax({
                url: formEl.getAttribute('action'),
                method: 'POST',
                data: $(formEl).serialize(),
                success: function(res) {
                    // Tutup modal edit
                    $(formEl).closest('.modal').modal('hide');
                },
                error: function(err) {
                    console.log(err);
                }
            });
        });
    });

    // Fungsi reload unit table untuk barang tertentu
    function reloadUnitTable(barangId) {
        $.get(`/barang/${barangId}/units-json`, function(units) {
            const tbody = $(`#unitTableBody-${barangId}`);
            tbody.empty();

            if(units.length === 0){
                tbody.append('<tr><td colspan="6" class="text-center text-muted">Belum ada unit untuk barang ini.</td></tr>');
            } else {
                units.forEach((unit, i) => {
                    const badgeClass = unit.kondisi === 'Rusak Ringan' ? 'bg-warning text-dark' :
                                       unit.kondisi === 'Rusak Berat' ? 'bg-danger' : 'bg-success';

                    const nextText = unit.tanggal_perawatan_selanjutnya
                        ? new Date(unit.tanggal_perawatan_selanjutnya)
                            .toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})
                        : '-';

                    // Bandingkan hanya tanggal
                    const today = new Date(); today.setHours(0,0,0,0);
                    const nextDate = unit.tanggal_perawatan_selanjutnya
                        ? new Date(unit.tanggal_perawatan_selanjutnya) 
                        : null;
                    if(nextDate) nextDate.setHours(0,0,0,0);
                    const isDue = nextDate ? nextDate <= today : false;

                    const buttonHTML = isDue
                        ? `<button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                             <i data-lucide="check-circle"></i> Konfirmasi
                           </button>`
                        : `<button class="btn btn-secondary btn-sm d-flex align-items-center gap-1" disabled>
                             <i data-lucide="clock"></i> Menunggu
                           </button>`;

                    tbody.append(`
                        <tr>
                            <td>${i+1}</td>
                            <td>${unit.kode_unit}</td>
                            <td><span class="badge ${badgeClass}">${unit.kondisi}</span></td>
                            <td>${unit.frekuensi_perawatan || unit.barang_frekuensi || "-"}</td>
                            <td>${nextText}</td>
                            <td>
                                <form action="/unit/konfirmasi/${unit.id}" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    ${buttonHTML}
                                </form>
                            </td>
                        </tr>
                    `);
                });
            }

            // Render ulang Lucide icon
            setTimeout(() => {
                lucide.createIcons({ scope: tbody[0] });
            }, 0.4);
        });
    }

    // Event: setelah modal edit ditutup, reload tabel barang terkait
    document.querySelectorAll('.modal[id^="editBarangModal-"]').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');

            const barangId = this.id.replace('editBarangModal-', '');
            const unitModalEl = document.getElementById(`unitModal-${barangId}`);
            if(unitModalEl){
                const unitModal = new bootstrap.Modal(unitModalEl);
                unitModal.show();
            }

            reloadUnitTable(barangId);
        });
    });

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const select = document.getElementById('modeFrekuensi-{{ $unit->id }}');
  const customWrapper = document.getElementById('customFrekuensiWrapper-{{ $unit->id }}');

  function toggleCustomFrekuensi() {
    if (select.value === 'custom') {
      customWrapper.style.display = 'block';
    } else {
      customWrapper.style.display = 'none';
    }
  }

  select.addEventListener('change', toggleCustomFrekuensi);
  toggleCustomFrekuensi(); // biar sinkron pas awal
});
</script>

