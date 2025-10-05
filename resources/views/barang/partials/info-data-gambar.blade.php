<table class="table table-bordered table-striped">
    <tbody>
        <tr>
            <th style="width: 30%;">Nama Barang</th>
            <td>{{ $barang->nama_barang }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $barang->kategori->nama_kategori }}</td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>{{ $barang->jumlah_barang }} {{ $barang->satuan }}</td>
        </tr>
        <tr>
            <th>Kondisi</th>
            <td>
                @php
                    $badgeClass = 'bg-success';
                    if ($barang->kondisi === 'Rusak Ringan') {
                        $badgeClass = 'bg-warning text-dark';
                    } elseif ($barang->kondisi === 'Rusak Berat') {
                        $badgeClass = 'bg-danger';
                    }
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $barang->kondisi }}</span>
            </td>
        </tr>
        <tr>
            <th>Tanggal Pengadaan</th>
            <td>
                {{ $barang->tanggal_pengadaan 
                    ? \Carbon\Carbon::parse($barang->tanggal_pengadaan)->translatedFormat('d F Y')
                    : '-' }}
            </td>
        </tr>
        <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ $barang->updated_at->translatedFormat('d F Y, H:i') }}</td>
        </tr>

        {{-- 🛠️ Bagian Perawatan Barang --}}
        @if($barang->frekuensi_perawatan)
            <tr>
                <th>Frekuensi Perawatan</th>
                <td>{{ $barang->frekuensi_perawatan }}</td>
            <tr>
                <th>Tanggal Perawatan Selanjutnya</th>
                <td>
                    @php
                        $now = \Carbon\Carbon::now()->startOfDay();
                        $next = $barang->tanggal_perawatan_selanjutnya
                            ? \Carbon\Carbon::parse($barang->tanggal_perawatan_selanjutnya)->startOfDay()
                            : null;
                    @endphp

                    {{-- Tampilkan tanggal --}}
                    {{ $next ? $next->translatedFormat('d F Y') : '-' }}

                    {{-- Badge status --}}
                    @if($next)
                        @php
                            // Hitung selisih hari (boleh negatif)
                            $selisihHari = $now->diffInDays($next, false);

                            $badgeClass = '';
                            $pesan = '';

                            if ($next->isToday()) {
                                // ✅ Hari ini
                                $pesan = '⚠️ Jadwal perawatan hari ini!';
                                $badgeClass = 'bg-warning text-dark';
                            } elseif ($next->isPast()) {
                                // ⏰ Sudah lewat
                                $hari = abs($selisihHari);
                                $bulan = floor($hari / 30);
                                $pesan = $bulan > 0
                                    ? "⏰ Jadwal perawatan sudah lewat {$bulan} bulan ({$hari} hari)!"
                                    : "⏰ Jadwal perawatan sudah lewat {$hari} hari!";
                                $badgeClass = 'bg-danger';
                            } else {
                                // 🕒 Akan datang
                                $hari = ceil($selisihHari); // dibulatkan ke atas biar nggak desimal
                                $bulan = floor($hari / 30);
                                $pesan = $bulan > 0
                                    ? "🕒 Jadwal perawatan akan tiba dalam {$bulan} bulan ({$hari} hari) lagi"
                                    : "🕒 Jadwal perawatan akan tiba dalam {$hari} hari lagi";
                                $badgeClass = 'bg-info text-dark';
                            }
                        @endphp

                        <div class="mt-2">
                            <span class="badge {{ $badgeClass }}">{{ $pesan }}</span>
                        </div>

                        {{-- Tombol konfirmasi jika hari ini atau sudah lewat --}}
                        @if($next->isToday() || $next->isPast())
                            <form action="{{ route('barang.konfirmasiPerawatan', $barang->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    ✅ Konfirmasi Perawatan Selesai
                                </button>
                            </form>
                        @endif
                    @endif
                </td>
            </tr>
        @endif
    </tbody>
</table>
