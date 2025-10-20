<table border="1" cellspacing="0" cellpadding="6" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Jumlah</th>
            <th>Kondisi</th>
            <th>Sumber Dana</th>
            <th>Tgl. Pengadaan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($barangs as $index => $barang)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $barang->kode_barang }}</td>
                <td>{{ $barang->nama_barang }}</td>
                <td>{{ $barang->kategori->nama_kategori }}</td>
                <td>{{ $barang->lokasi->nama_lokasi }}</td>
                <td>{{ $barang->jumlah_barang }}</td>
                <td>
                    Baik: {{ $barang->jumlah_baik }}<br>
                    Rusak Ringan: {{ $barang->jumlah_rusak }} <br>
                    Rusak Berat: {{ $barang->jumlah_berat }}
                </td>
                <td>{{ $barang->sumber_dana }}</td>
                <td>{{ date('d-m-Y', strtotime($barang->tanggal_pengadaan)) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align: center;">Tidak ada data untuk tahun {{ $tahun }}.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<p style="margin-top: 20px; text-align: right;">
    <strong>Tanggal Cetak:</strong> {{ $date }}
</p>
