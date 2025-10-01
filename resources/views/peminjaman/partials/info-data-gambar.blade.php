<table class="table table-bordered table-striped">
    <tbody>
        <tr>
            <th style="width: 30%;">Nama Peminjam</th>
            <td>{{ $peminjaman->nama_peminjam }}</td>
        </tr>
        <tr>
            <th>Nama Barang</th>
            <td>{{ $peminjaman->barang->nama_barang }}</td>
        </tr>
        <tr>
            <th>Jumlah Dipinjam</th>
            <td>{{ $peminjaman->jumlah_pinjam }}</td>
        </tr>
        <tr>
            <th>Tanggal Pinjam</th>
            <td>{{ $peminjaman->tanggal_pinjam }}</td>
        </tr>
        <tr>
            <th>Tanggal Dikembalikan</th>
            <td>{{ $peminjaman->tanggal_kembali ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
            <span class="badge {{ $peminjaman->status == 'Dipinjam' ? 'bg-warning' : 'bg-success' }}">
            {{ $peminjaman->status }}
            </span>
            </td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>{{ $peminjaman->keterangan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ $peminjaman->updated_at->translatedFormat('d F Y, H:i:s') }}</td>
        </tr>
    </tbody>
</table>