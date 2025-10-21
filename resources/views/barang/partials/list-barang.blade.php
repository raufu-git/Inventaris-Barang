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
            <th>Sumber Dana</th>
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
                @php
                    $unitCounts = \App\Models\Unit::where('barang_id', $barang->id)
                        ->select('kondisi', \DB::raw('COUNT(*) as total'))
                        ->groupBy('kondisi')
                        ->pluck('total', 'kondisi')
                        ->toArray();

                    $baik = $unitCounts['Baik'] ?? 0;
                    $ringan = $unitCounts['Rusak Ringan'] ?? 0;
                    $berat = $unitCounts['Rusak Berat'] ?? 0;
                @endphp

                <div class="d-flex flex-column gap-1">
                    <span class="badge badge-mini bg-info text-dark">Baik: {{ $baik }}</span>
                    <span class="badge badge-mini bg-warning text-dark">Ringan: {{ $ringan }}</span>
                    <span class="badge badge-mini bg-danger text-white">Berat: {{ $berat }}</span>
                </div>
            </td>
            <td>{{ $barang->sumber_dana }}</td>
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
            $hariIni = \Carbon\Carbon::today();
            $selisihHari = $hariIni->diffInDays($next, false); // false biar nilai negatif kalau sudah lewat

            // Cek status tanggal
            $isToday = $next->isToday();
            $isPast = $next->isPast() && !$next->isToday();
            $isSoon = !$isPast && !$isToday && $selisihHari <= 7; // kalau tinggal <=7 hari

            // Warna & pesan
            if ($isPast) {
                $warna = 'bg-danger text-white';
                $pesan = "⏰ Jadwal perawatan <strong class='nama-barang'>{$barang->nama_barang}</strong> sudah lewat (<em>{$next->translatedFormat('d F Y')}</em>)";
            } elseif ($isToday) {
                $warna = 'bg-warning text-dark';
                $pesan = "⚠️ Jadwal perawatan <strong class='nama-barang'>{$barang->nama_barang}</strong> hari ini!";
            } elseif ($isSoon) {
                $warna = 'bg-info text-dark';
                $hariTersisa = $selisihHari === 0 ? 'hari ini' : "{$selisihHari} hari lagi";
                $pesan = "📆 Jadwal perawatan <strong class='nama-barang'>{$barang->nama_barang}</strong> tinggal <strong>{$hariTersisa}</strong> (<em>{$next->translatedFormat('d F Y')}</em>)";
            } else {
                continue; // skip kalau belum masuk seminggu
            }
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
/* 🌿 Soft Sort Toggle Button */
.sort-toggle {
    background: linear-gradient(145deg, #f2fdf2, #d9f7d9);
    color: #155724;
    border: 1px solid #a3d2a1;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.sort-toggle:hover {
    background: linear-gradient(145deg, #e3f9e3, #c9f1c9);
    box-shadow: 0 0 6px rgba(25, 135, 84, 0.25);
    border-color: #198754;
    transform: scale(1.02);
}

.sort-toggle:active {
    background: #eafcea;
    transform: scale(0.98);
}
.w-fit {
    width: fit-content;
}
.badge-mini {
    font-size: 11px;       /* lebih kecil tapi masih kebaca jelas */
    padding: 3px 6px;      /* lebih rapet */
    border-radius: 6px;    /* biar tetap smooth */
    line-height: 1.1;
    width: fit-content;
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
