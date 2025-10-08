<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Nama Peminjam</th>
            <th>Nama Barang</th>
            <th>Status</th>
            <th>&nbsp;</th>
        </tr>
    </x-slot>

    @forelse ($peminjamans as $index => $peminjaman)
        <tr>
            <td>{{ $peminjamans->firstItem() + $index }}</td>
            <td>{{ $peminjaman->nama_peminjam }}</td>
            <td>{{ $peminjaman->barang->nama_barang }}</td>
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
                    :kondisi="$peminjaman->kondisi ?? 'Baik'"
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
    .modern-select {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #ccc;
    background: #f9f9f9;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    font-size: 14px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.modern-select:hover {
    border-color: #198754;
    box-shadow: 0 0 5px rgba(25, 135, 84, 0.3);
}

.modern-select:focus {
    outline: none;
    border-color: #198754;
    box-shadow: 0 0 8px rgba(25, 135, 84, 0.5);
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
                <p><strong>Kondisi:</strong> ${kondisi}</p>
                <p><strong>Jumlah Dipinjam:</strong> ${jumlah}</p>
                <p><strong>Keterangan:</strong> ${keterangan}</p>
                <hr>
                <label for="kondisi_pengembalian" class="fw-bold">Kondisi Barang Saat Dikembalikan:</label>
                <select id="kondisi_pengembalian" class="modern-select mt-2">
                    <option value="">Sama seperti sebelumnya</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                    <option value="Hilang">Hilang</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Ya, Kembalikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#d33',
        preConfirm: () => {
            return {
                kondisi: document.getElementById('kondisi_pengembalian').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const kondisi = result.value.kondisi || '';
            let form = document.getElementById('form-kembalikan-' + id);
            let inputKondisi = form.querySelector('input[name="kondisi_pengembalian"]');

            if (!inputKondisi) {
                inputKondisi = document.createElement('input');
                inputKondisi.type = 'hidden';
                inputKondisi.name = 'kondisi_pengembalian';
                form.appendChild(inputKondisi);
            }

            inputKondisi.value = kondisi;
            form.submit();
        }
    });
}

</script>
