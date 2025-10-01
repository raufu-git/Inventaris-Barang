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
                <span class="badge {{ $peminjaman->status == 'Dipinjam' ? 'bg-warning' : 'bg-success' }}">
                    {{ $peminjaman->status }}
                </span>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmKembalikan(id, nama, barang, jumlah, keterangan) {
    Swal.fire({
        title: 'Konfirmasi Pengembalian',
        html: `
            <div style="text-align:left">
                <p><b>Nama Peminjam:</b> ${nama}</p>
                <p><b>Barang:</b> ${barang}</p>
                <p><b>Jumlah Dipinjam:</b> ${jumlah}</p>
                <p><b>Keterangan:</b> ${keterangan}</p>
                <hr>
                <p>Apakah barang ini sudah dikembalikan?</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kembalikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-kembalikan-' + id).submit();
        }
    });
}
</script>