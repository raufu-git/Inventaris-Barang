<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Jumlah</th>
            <th>Kondisi</th>
            <th>&nbsp;</th>
        </tr>
    </x-slot>

    @forelse ($barangs as $index => $barang)
        <tr>
            <td>{{ $barangs->firstItem() + $index }}</td>
            <td>{{ $barang->kode_barang }}</td>
            <td>{{ $barang->nama_barang }}</td>
            <td>{{ $barang->kategori->nama_kategori }}</td>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
                        <td>{{ $barang->jumlah_barang }}  {{ $barang->satuan }}</td>
            <td>
                @if ($barang->kondisi === 'Baik')
                    <span class="badge bg-info">{{ $barang->kondisi }}</span>
                @elseif ($barang->kondisi === 'Rusak Ringan')
                    <span class="badge bg-warning text-dark">{{ $barang->kondisi }}</span>
                @elseif ($barang->kondisi === 'Rusak Berat')
                    <span class="badge bg-danger">{{ $barang->kondisi }}</span>
                @else
                    <span class="badge bg-secondary">{{ $barang->kondisi }}</span>
                @endif
            </td>
            <td class="text-end">
                @can('manage barang')
                    <x-tombol-aksi href="{{ route('barang.show', $barang->id) }}" type="show" />
                    <x-tombol-aksi href="{{ route('barang.edit', $barang->id) }}" type="edit" />
                @endcan

                @can('delete barang')
                    <x-tombol-aksi href="{{ route('barang.destroy', $barang->id) }}" type="delete" />
                @endcan
            </td> 
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">
                <div class="alert alert-danger">
                    Data barang belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>
<!-- Tempat munculnya semua toast -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    @foreach ($reminders as $index => $barang)
        @php
            $next = \Carbon\Carbon::parse($barang->tanggal_perawatan_selanjutnya);
            $isToday = $next->isToday();
            $isPast = $next->isPast() && !$next->isToday();
            $warna = $isPast ? 'bg-danger text-white' : 'bg-warning text-dark';
            $pesan = $isPast
                ? "⏰ Jadwal perawatan <strong class='nama-barang'>{$barang->nama_barang}</strong> sudah lewat (<em>{$next->translatedFormat('d F Y')}</em>)"
                : "⚠️ Jadwal perawatan <strong class='nama-barang'>{$barang->nama_barang}</strong> hari ini!";
        @endphp

        <div class="toast align-items-center text-bg-light border-0 mb-2 shadow reminder-toast"
            role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="8000"
            style="animation-delay: {{ $index * 0.7 }}s;">
            <div class="toast-header {{ $warna }}">
                <strong class="me-auto">🔧 Pengingat Perawatan</strong>
                <small>{{ now()->translatedFormat('H:i') }}</small>
                <button type="button" class="btn-close ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {!! $pesan !!}
            </div>
        </div>
    @endforeach
</div>

<style>
/* Animasi lembut */
.reminder-toast {
    opacity: 0;
    transform: translateX(30px);
    animation: slideIn 0.6s ease-out forwards;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Gaya nama barang biar lebih mencolok */
.nama-barang {
    font-weight: 700;
    color: #0d6efd; /* biru Bootstrap */
    background: rgba(13, 110, 253, 0.08);
    padding: 0 4px;
    border-radius: 4px;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastElList = document.querySelectorAll('.toast');
        toastElList.forEach((toastEl, i) => {
            const delay = i * 400; // 0.7 detik per jeda
            setTimeout(() => {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }, delay);
        });
    });
</script>
