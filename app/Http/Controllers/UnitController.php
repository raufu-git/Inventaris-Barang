<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\Barang;
use Carbon\Carbon;

class UnitController extends Controller
{
    
    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'kondisi' => 'nullable|in:Baik,Rusak Ringan,Rusak Berat',
            'frekuensi_perawatan' => 'nullable|string|max:100',
            'tanggal_perawatan_selanjutnya' => 'nullable|date',
            'mode_frekuensi' => 'nullable|in:semua,custom',
            'nomor_custom' => 'nullable|string',
        ]);

        // ✅ Tambahkan 'sekali' kalau belum ada
        if (!empty($validated['frekuensi_perawatan'])) {
            $frekuensi = strtolower($validated['frekuensi_perawatan']);
            if (!str_contains($frekuensi, 'sekali')) {
                $validated['frekuensi_perawatan'] .= ' sekali';
            }
        }

        // ✅ Kalau user kosongin frekuensi → pakai default dari barang
        if (empty($validated['frekuensi_perawatan'])) {
            $validated['frekuensi_perawatan'] = $unit->barang->frekuensi_perawatan;
        }

        // ✅ Hitung tanggal otomatis kalau kosong tapi frekuensi ada
        if (empty($validated['tanggal_perawatan_selanjutnya']) && !empty($validated['frekuensi_perawatan'])) {
            $frekuensi = strtolower($validated['frekuensi_perawatan']);

            // Ambil angka dari string (contoh: "3 bulan" → 3)
            preg_match('/(\d+)/', $frekuensi, $angka);
            $jumlah = isset($angka[1]) ? (int)$angka[1] : 1;

            $validated['tanggal_perawatan_selanjutnya'] = match (true) {
                str_contains($frekuensi, 'hari') => now()->addDays($jumlah),
                str_contains($frekuensi, 'minggu') => now()->addWeeks($jumlah),
                str_contains($frekuensi, 'bulan') => now()->addMonths($jumlah),
                str_contains($frekuensi, 'tahun') => now()->addYears($jumlah),
                default => null,
            };
        }

        // ✅ Kalau mode ubah semua → update semua unit di barang ini
        if (($validated['mode_frekuensi'] ?? null) === 'semua') {
            Unit::where('barang_id', $unit->barang_id)
                ->update([
                    'frekuensi_perawatan' => $validated['frekuensi_perawatan'],
                    'tanggal_perawatan_selanjutnya' => $validated['tanggal_perawatan_selanjutnya'],
                ]);

            return redirect()
                ->route('barang.show', $unit->barang_id)
                ->with('success', 'Frekuensi semua unit berhasil diperbarui!');
        }

        // ✅ Kalau mode custom → update unit berdasarkan nomor_custom
        if (($validated['mode_frekuensi'] ?? null) === 'custom' && !empty($validated['nomor_custom'])) {
            $nomorList = collect(explode(',', $validated['nomor_custom']))
                ->map(fn($n) => trim($n))
                ->filter()
                ->toArray();

            Unit::where('barang_id', $unit->barang_id)
                ->whereIn('nomor_unit', $nomorList)
                ->update([
                    'frekuensi_perawatan' => $validated['frekuensi_perawatan'],
                    'tanggal_perawatan_selanjutnya' => $validated['tanggal_perawatan_selanjutnya'],
                ]);

            return redirect()
                ->route('barang.show', $unit->barang_id)
                ->with('success', 'Frekuensi unit tertentu berhasil diperbarui!');
        }

        // ✅ Kalau nggak ada mode (update manual biasa)
        $unit->update($validated);

        return redirect()
            ->route('barang.show', $unit->barang_id)
            ->with('success', 'Unit berhasil diperbarui!');
    }

    public function updateFrekuensiKondisi(Request $request, Barang $barang)
{
    $validated = $request->validate([
        'frekuensi_perawatan' => 'nullable|string|max:50',
        'kondisi' => 'required|string|max:50',
    ]);

    $barang->update($validated);

    // kalau frekuensi diubah → hitung tanggal baru
    if (!empty($validated['frekuensi_perawatan'])) {
        preg_match('/(\d+)/', $validated['frekuensi_perawatan'], $angka);
        $jumlah = isset($angka[1]) ? (int)$angka[1] : 1;
        $frekuensi = strtolower($validated['frekuensi_perawatan']);

        $tanggalSelanjutnya = match (true) {
            str_contains($frekuensi, 'hari') => now()->addDays($jumlah),
            str_contains($frekuensi, 'minggu') => now()->addWeeks($jumlah),
            str_contains($frekuensi, 'bulan') => now()->addMonths($jumlah),
            str_contains($frekuensi, 'tahun') => now()->addYears($jumlah),
            default => null,
        };

        // update unit yang masih ngikut parent
        foreach ($barang->units as $unit) {
            if (empty($unit->frekuensi_perawatan)) {
                $unit->update([
                    'frekuensi_perawatan' => $validated['frekuensi_perawatan'],
                    'tanggal_perawatan_selanjutnya' => $tanggalSelanjutnya,
                ]);
            }
        }
    }

    return redirect()->back()->with('success', 'Frekuensi dan kondisi barang berhasil diperbarui!');
}

public function konfirmasiPerawatan($id)
    {
        $unit = Unit::with('barang')->findOrFail($id);

        // Cek apakah sudah waktunya perawatan
        if ($unit->tanggal_perawatan_selanjutnya && now()->gte($unit->tanggal_perawatan_selanjutnya)) {

            $frekuensi = $unit->frekuensi_perawatan ?? $unit->barang->frekuensi_perawatan ?? null;

            if ($frekuensi) {
                [$angka, $satuan] = explode(' ', strtolower($frekuensi));
                $angka = (int) $angka;

                switch ($satuan) {
                    case 'hari':
                    case 'day':
                    case 'days':
                        $unit->tanggal_perawatan_selanjutnya = Carbon::now()->addDays($angka);
                        break;
                    case 'minggu':
                    case 'week':
                    case 'weeks':
                        $unit->tanggal_perawatan_selanjutnya = Carbon::now()->addWeeks($angka);
                        break;
                    case 'bulan':
                    case 'month':
                    case 'months':
                        $unit->tanggal_perawatan_selanjutnya = Carbon::now()->addMonths($angka);
                        break;
                    case 'tahun':
                    case 'year':
                    case 'years':
                        $unit->tanggal_perawatan_selanjutnya = Carbon::now()->addYears($angka);
                        break;
                    default:
                        // fallback kalau formatnya aneh
                        $unit->tanggal_perawatan_selanjutnya = Carbon::now();
                        break;
                }
            } else {
                // Kalau frekuensi kosong, kosongkan tanggal
                $unit->tanggal_perawatan_selanjutnya = null;
            }

            $unit->save();

            return back()->with('success', 'Perawatan unit berhasil dikonfirmasi!');
        }

        return back()->with('error', 'Belum waktunya perawatan untuk unit ini.');
    }
    public function getUnitsByBarang($id)
{
    $units = Unit::where('barang_id', $id)
        ->whereIn('kondisi', ['Baik', 'Rusak Ringan'])
        ->where('status', '!=', 'Dipinjam') // pastikan ada kolom status
        ->select('id', 'kode_unit', 'kondisi')
        ->get();

    return response()->json($units);
}

}
