@if ($peminjaman->barang && $peminjaman->barang->gambar)
    <img src="{{ asset('gambar-barang/' . $peminjaman->barang->gambar) }}" 
         alt="{{ $peminjaman->barang->nama_barang }}" 
         class="img-fluid rounded border" 
         style="max-height: 300px;">
@else
    <div class="d-flex justify-content-center align-items-center bg-light rounded border" style="height: 300px;">
        <span class="text-muted">Tidak ada gambar tersedia</span>
    </div>
@endif
