<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Nama Peminjam</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
            <th>&nbsp;</th>
        </tr>
    </x-slot>

    @forelse ($peminjamans as $index => $peminjaman)
        <tr>
            <td>{{ $peminjamans->firstItem() + $index }}</td>
            <td>{{ $peminjaman->nama_peminjam }}</td>
            <td>{{ $peminjaman->barang->nama_barang }}</td>
            <td>{{ $peminjaman->jumlah_pinjam }} {{ $peminjaman->barang->satuan }}</td>
            <td>{{ $peminjaman->tanggal_pinjam }}</td>
            <td>{{ $peminjaman->tanggal_kembali ?? '-' }}</td>
            <td>
                @if ($peminjaman->status == 'Dipinjam')
                    <span class="badge bg-warning text-dark">{{ $peminjaman->status }}</span>
                @elseif ($peminjaman->status == 'Dikembalikan' && $peminjaman->kondisi_pengembalian == 'Hilang')
                    <span class="badge bg-danger">Hilang</span>
                @elseif ($peminjaman->status == 'Dikembalikan')
                    <span class="badge bg-success">{{ $peminjaman->status }}</span>
                @else
                    <span class="badge bg-secondary">{{ $peminjaman->status }}</span>
                @endif
            </td>
            <td class="text-end">
                @can('manage peminjaman')
                    <x-tombol-aksi href="{{ route('peminjaman.show', $peminjaman->id) }}" type="show" />

                    {{-- tampilkan tombol edit hanya kalau masih dipinjam --}}
                @if($peminjaman->status == 'Dipinjam')
                    <x-tombol-aksi
                        type="edit"
                        href="{{ route('peminjaman.edit', $peminjaman->id) }}"
                    />

                <x-tombol-aksi
                    type="return"
                    :id="$peminjaman->id"
                    :nama_peminjam="$peminjaman->nama_peminjam"
                    :barang="$peminjaman->barang->nama_barang"
                    :kondisi="$peminjaman->kondisi_awal ?? '-'"
                    :jumlah="$peminjaman->jumlah_pinjam"
                    :keterangan="$peminjaman->keterangan ?? '-'"
                />

                @endif
                @endcan

                @can('delete peminjaman')
                    <x-tombol-aksi href="{{ route('peminjaman.destroy', $peminjaman->id) }}" type="delete" />
                @endcan
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">
                <div class="alert alert-danger">
                    Data peminjaman belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>

<style>
/* 🌈 Fresh Modern Blue Select for Swal */
.swal2-select-modern {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1px solid #b0c9ff;
    background: linear-gradient(145deg, #eaf1ff, #dce8ff);
    color: #1b2a4e;
    font-size: 14px;
    font-family: 'Segoe UI', sans-serif;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: none;
}

/* Hover dan Focus */
.swal2-select-modern:hover {
    background: linear-gradient(145deg, #dee8ff, #ecf3ff);
    border-color: #6c9eff;
    box-shadow: 0 0 6px rgba(108, 158, 255, 0.25);
}

.swal2-select-modern:focus {
    background: linear-gradient(145deg, #e3ecff, #f3f7ff);
    border-color: #6c9eff;
    box-shadow: 0 0 8px rgba(108, 158, 255, 0.35);
}

/* Dropdown Option Styling */
.swal2-select-modern option {
    background: #f6f9ff;
    color: #1b2a4e;
    padding: 10px;
    font-size: 14px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

/* Saat di-hover (dropdown list) */
.swal2-select-modern option:hover,
.swal2-select-modern option:checked {
    background: linear-gradient(145deg, #c7dbff, #e3ecff) !important;
    color: #0f1a38;
}

/* Untuk browser yang pakai highlight default (kayak di screenshot) */
.swal2-select-modern option:focus,
.swal2-select-modern option:active {
    background: linear-gradient(145deg, #c7dbff, #e3ecff) !important;
    color: #0f1a38 !important;
}

/* Pop animation biar halus pas muncul */
@keyframes popIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.swal2-select-modern {
    animation: popIn 0.25s ease-out;
}

/* ===== STYLE KHUSUS UNTUK SORT SELECT ===== */
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

/* biar baris gak pecah ke bawah */
.no-wrap-toolbar {
    flex-wrap: nowrap !important;
}

/* biar dropdown status keliatan lebih clean */
.filter-status {
    background: linear-gradient(145deg, #f2fdf2, #d9f7d9);
    color: #155724;
    border: 1px solid #a3d2a1;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    appearance: none; /* hilangin ikon dropdown */
    -webkit-appearance: none;
    -moz-appearance: none;
}

.filter-status:hover {
    background: linear-gradient(145deg, #e3f9e3, #c9f1c9);
    box-shadow: 0 0 6px rgba(25, 135, 84, 0.25);
    border-color: #198754;
    transform: scale(1.02);
}

</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmKembalikan(id, nama, barang, kondisi, jumlah, keterangan) {
    Swal.fire({
        title: 'Konfirmasi Pengembalian',
        icon: 'question',
        html: `
            <div style="text-align:left; font-family: Arial, sans-serif;">
                <p><strong>Nama Peminjam:</strong> ${nama}</p>
                <p><strong>Barang:</strong> ${barang}</p>
                <p><strong>Kondisi Awal:</strong> ${kondisi}</p>
                <p><strong>Jumlah Dipinjam:</strong> ${jumlah}</p>
                <p><strong>Keterangan:</strong> ${keterangan}</p>
                <hr>
                <label for="kondisi_pengembalian" class="fw-bold">Kondisi Barang Saat Dikembalikan:</label>
                <select id="kondisi_pengembalian" class="swal2-select-modern mt-2">
                    <option value="">Sama seperti sebelumnya</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                    <option value="Hilang">Hilang</option>
                </select>

                <label for="catatan_pengembalian" class="fw-bold mt-3 d-block">Catatan Pengembalian (Opsional):</label>
                <textarea id="catatan_pengembalian" rows="3" placeholder="Tulis catatan jika perlu..."   
                    style="
                        width:100%; 
                        border-radius:8px; 
                        border:1px solid #b0c9ff; 
                        padding:8px 10px; 
                        font-size:13px; 
                        resize: vertical; 
                        background: #f9fbff;
                        margin-top:5px;
                    "></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Ya, Kembalikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3a6dd8',
        cancelButtonColor: '#d33',
        preConfirm: () => {
            return {
                kondisi: document.getElementById('kondisi_pengembalian').value,
                catatan: document.getElementById('catatan_pengembalian').value.trim()
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const kondisi = result.value.kondisi || '';
            const catatan = result.value.catatan || '';

            let form = document.getElementById('form-kembalikan-' + id);

            // Input kondisi
            let inputKondisi = form.querySelector('input[name="kondisi_pengembalian"]');
            if (!inputKondisi) {
                inputKondisi = document.createElement('input');
                inputKondisi.type = 'hidden';
                inputKondisi.name = 'kondisi_pengembalian';
                form.appendChild(inputKondisi);
            }
            inputKondisi.value = kondisi;

            // Input catatan (baru)
            let inputCatatan = form.querySelector('input[name="catatan_pengembalian"]');
            if (!inputCatatan) {
                inputCatatan = document.createElement('input');
                inputCatatan.type = 'hidden';
                inputCatatan.name = 'catatan_pengembalian';
                form.appendChild(inputCatatan);
            }
            inputCatatan.value = catatan;

            form.submit();
        }
    });
}
</script>
