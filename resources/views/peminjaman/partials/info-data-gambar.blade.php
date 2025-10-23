<table class="table table-bordered table-striped">
    <tbody>
        <tr>
            <th style="width: 30%;">Nama Peminjam</th>
            <td>{{ $peminjaman->nama_peminjam }}</td>
        </tr>
        <tr>
            <th>Nama Barang</th>
            <td>{{ $peminjaman->barang->nama_barang ?? '-' }}</td>
        </tr>
        <tr>
            <th>Sumber Dana</th>
            <td>{{ $peminjaman->barang->sumber_dana ?? '-' }}</td>
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
                @else
                    <span class="badge bg-secondary">{{ $kondisiAwal }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Jumlah Dipinjam</th>
            <td>{{ $peminjaman->jumlah_pinjam }} {{ $peminjaman->unit->barang->satuan ?? '' }}</td>
        </tr>
        <tr>
            <th>Tanggal Pinjam</th>
            <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y, H:i:s') }}</td>
        </tr>
        <tr>
            <th>Tanggal Dikembalikan</th>
            <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}</td>
        </tr>
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
                    @php $kondisi = $peminjaman->kondisi_pengembalian; @endphp
                    @if ($kondisi == 'Hilang')
                        <span class="badge bg-danger">{{ $kondisi }}</span>
                    @elseif ($kondisi == 'Rusak Ringan')
                        <span class="badge bg-warning text-dark">{{ $kondisi }}</span>
                    @elseif ($kondisi == 'Rusak Berat')
                        <span class="badge bg-dark">{{ $kondisi }}</span>
                    @else
                        <span class="badge bg-success">{{ $kondisi }}</span>
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
            <td>{{ \Carbon\Carbon::parse($peminjaman->updated_at)->translatedFormat('d F Y, H:i:s') }}</td>
        </tr>
    </tbody>
</table>
