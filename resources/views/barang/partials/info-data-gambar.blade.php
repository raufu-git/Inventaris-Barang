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
                    <form action="{{ route('barang.konfirmasiPerawatan', $unit->id) }}" method="POST">
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
            <input type="text" name="frekuensi_perawatan" class="form-control"
                   value="{{ old('frekuensi_perawatan', $barang->frekuensi_perawatan) }}"
                   placeholder="contoh: 3 bulan">
            <small class="text-muted">Semua unit akan mengikuti frekuensi ini kecuali yang diatur manual.</small>
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

    // Search unit
    document.querySelectorAll('.searchUnitInput').forEach(input => {
        input.addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            const barangId = this.dataset.barang;
            const rows = document.querySelectorAll(`#unitTableBody-${barangId} tr`);
            rows.forEach(row => {
                const kodeUnit = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const belakang = kodeUnit.split('-').pop();
                row.style.display = belakang.includes(keyword) || keyword === '' ? '' : 'none';
            });
        });
    });

    // AJAX edit form
    const form = document.getElementById('editForm-{{ $barang->id }}');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: form.getAttribute('action'),
            method: 'POST',
            data: $(form).serialize(),
            success: function(res) {
                // Tutup modal edit
                $('#editBarangModal-{{ $barang->id }}').modal('hide');
                // tabel unit akan di-reload saat modal edit benar-benar tertutup
            },
            error: function(err) {
                console.log(err);
            }
        });
    });

    // Setelah modal edit benar-benar tertutup
    $('#editBarangModal-{{ $barang->id }}').on('hidden.bs.modal', function () {
        // hapus backdrop lama
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');

        // Buka modal unit
        const unitModalEl = document.getElementById('unitModal-{{ $barang->id }}');
        const unitModal = new bootstrap.Modal(unitModalEl);
        unitModal.show();

        // Reload tabel unit via AJAX JSON
        $.get('/barang/{{ $barang->id }}/units-json', function(units) {
            let tbody = $('#unitTableBody-{{ $barang->id }}');
            tbody.empty();

            if(units.length === 0){
                tbody.append('<tr><td colspan="6" class="text-center text-muted">Belum ada unit untuk barang ini.</td></tr>');
            } else {
                units.forEach((unit, i) => {
                    let badgeClass = 'bg-success';
                    if(unit.kondisi === 'Rusak Ringan') badgeClass = 'bg-warning text-dark';
                    else if(unit.kondisi === 'Rusak Berat') badgeClass = 'bg-danger';

                    let nextText = unit.tanggal_perawatan_selanjutnya
                        ? new Date(unit.tanggal_perawatan_selanjutnya)
                            .toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})
                        : '-';

                    tbody.append(`
                        <tr>
                            <td>${i+1}</td>
                            <td>${unit.kode_unit}</td>
                            <td><span class="badge ${badgeClass}">${unit.kondisi}</span></td>
                            <td>${unit.frekuensi_perawatan || '{{ $barang->frekuensi_perawatan ?? "-" }}'}</td>
                            <td>${nextText}</td>
                            <td>
                                <form action="/barang/konfirmasi/${unit.id}" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button class="btn btn-secondary btn-sm" disabled>Menunggu</button>
                                </form>
                            </td>
                        </tr>
                    `);
                });
            }
        });
    });

});
</script>
