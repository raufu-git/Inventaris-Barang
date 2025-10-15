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
            <th>Sumber Dana</th>
            <td>{{ $peminjaman->barang->sumber_dana }}</td>
        </tr>
        <tr>
            <th>Kondisi Saat Dipinjam</th>
            <td>
                @php
                    $kondisiAwal = $peminjaman->kondisi_awal ?? '-';
                @endphp

                @if ($kondisiAwal === 'Baik')
                    <span class="badge bg-info">{{ $kondisiAwal }}</span>
                @elseif ($kondisiAwal === 'Rusak Ringan')
                    <span class="badge bg-warning text-dark">{{ $kondisiAwal }}</span>
                @elseif ($kondisiAwal === 'Rusak Berat')
                    <span class="badge bg-danger">{{ $kondisiAwal }}</span>
                @elseif ($kondisiAwal === 'Hilang')
                    <span class="badge bg-dark">{{ $kondisiAwal }}</span>
                @else
                    <span class="badge bg-secondary">{{ $kondisiAwal }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Jumlah Dipinjam</th>
            <td>{{ $peminjaman->jumlah_pinjam }} {{ $peminjaman->barang->satuan }}</td>
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
        <tr>
            <th>Status</th>
            <td>
                <span class="badge {{ $peminjaman->status == 'Dipinjam' ? 'bg-warning text-dark' : 'bg-success' }}">
                    {{ $peminjaman->status }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Kondisi Saat Dikembalikan</th>
            <td>
                @if ($peminjaman->kondisi_pengembalian)
                    @if ($peminjaman->kondisi_pengembalian == 'Hilang')
                        <span class="badge bg-danger">{{ $peminjaman->kondisi_pengembalian }}</span>
                    @elseif ($peminjaman->kondisi_pengembalian == 'Rusak Ringan')
                        <span class="badge bg-warning text-dark">{{ $peminjaman->kondisi_pengembalian }}</span>
                    @elseif ($peminjaman->kondisi_pengembalian == 'Rusak Berat')
                        <span class="badge bg-dark">{{ $peminjaman->kondisi_pengembalian }}</span>
                    @else
                        <span class="badge bg-success">{{ $peminjaman->kondisi_pengembalian }}</span>
                    @endif
                @else
                    <span>-</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>{{ $peminjaman->keterangan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Catatan Pengembalian</th>
            <td>{{ $peminjaman->catatan_pengembalian ?? '-' }}</td>
        </tr>
        <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ $peminjaman->updated_at->translatedFormat('d F Y, H:i:s') }}</td>
        </tr>
    </tbody>
</table>