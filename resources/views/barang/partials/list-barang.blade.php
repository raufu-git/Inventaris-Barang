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
            role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4500"
            style="animation-delay: {{ $index * 0.7 }}s;">
            <div class="toast-header {{ $warna }}">
                <strong class="me-auto">🔧 Pengingat Perawatan</strong>
                <small>{{ now()->translatedFormat('H:i') }}</small>
            </div>
            <div class="toast-body">
                {!! $pesan !!}
            </div>
        </div>
    @endforeach
</div>

<style>

.reminder-toast {
    opacity: 0;
    transform: translateX(30px);
    animation: slideIn 0.6s ease-out forwards;
    cursor: pointer; /* biar tahu bisa diklik */
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

.nama-barang {
    font-weight: 700;
    color: #0d6efd;
    background: rgba(13, 110, 253, 0.08);
    padding: 0 4px;
    border-radius: 4px;
}

/* ===== STYLE KHUSUS UNTUK SORT SELECT ===== */
.sort-select {
    height: 34px;
    padding: 6px 12px;
    font-size: 13.5px;
    border-radius: 8px;
    border: 1px solid #a3d2a1;
    background: linear-gradient(145deg, #f2fdf2, #d9f7d9);
    color: #155724;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg fill='%23155724' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    padding-right: 32px;
}

.sort-select:hover {
    background: linear-gradient(145deg, #e3f9e3, #c9f1c9);
    box-shadow: 0 0 6px rgba(25, 135, 84, 0.25);
    border-color: #198754;
}

.sort-select:focus {
    background: #eafcea;
    box-shadow: 0 0 8px rgba(25, 135, 84, 0.4);
    border-color: #198754;
}

/* Style dropdown list (Firefox & Chrome yang support) */
.sort-select option {
    background: #f0fff0;
    color: #155724;
    padding: 10px;
    font-size: 13.5px;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastElList = document.querySelectorAll('.toast');
        toastElList.forEach((toastEl, i) => {
            const delay = i * 400; 
            setTimeout(() => {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();

                // Tutup toast kalau diklik
                toastEl.addEventListener('click', () => toast.hide());
            }, delay);
        });
    });
</script>
