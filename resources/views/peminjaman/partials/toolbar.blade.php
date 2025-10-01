<div class="row">
    <div class="col">
        <x-tombol-tambah label="Pinjam" href="{{ route('peminjaman.create') }}" />
        <x-tombol-cetak label="Cetak Laporan Peminjaman" href="{{ route('peminjaman.laporan') }}" />
    </div>
    <div class="col">
        <x-form-search placeholder="Cari nama peminjam/Barang..." />
    </div>
</div>