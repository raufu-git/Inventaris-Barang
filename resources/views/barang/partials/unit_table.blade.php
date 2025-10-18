@forelse($barang->units as $i => $unit)
    @php
        $badgeClass = match($unit->kondisi) {
            'Rusak Ringan' => 'bg-warning text-dark',
            'Rusak Berat' => 'bg-danger',
            default => 'bg-success'
        };
        $next = $unit->tanggal_perawatan_selanjutnya
            ? \Carbon\Carbon::parse($unit->tanggal_perawatan_selanjutnya)
            : null;
    @endphp
    <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $unit->kode_unit }}</td>
        <td><span class="badge {{ $badgeClass }}">{{ $unit->kondisi }}</span></td>
        <td>{{ $unit->frekuensi_perawatan ?? $barang->frekuensi_perawatan ?? '-' }}</td>
        <td>{{ $next ? $next->translatedFormat('d F Y') : '-' }}</td>
        <td>
            <form action="{{ route('barang.konfirmasiPerawatan', $unit->id) }}" method="POST">
                @csrf
                @if ($next && ($next->isPast() || $next->isToday()))
                    <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        <i data-lucide="check-circle"></i> Konfirmasi
                    </button>
                @else
                    <button class="btn btn-secondary btn-sm d-flex align-items-center gap-1" disabled>
                        <i data-lucide="clock"></i> Menunggu
                    </button>
                @endif
            </form>
        </td>
    </tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted">Belum ada unit untuk barang ini.</td>
</tr>
@endforelse
