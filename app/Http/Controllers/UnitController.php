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
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'frekuensi_perawatan' => 'nullable|string|max:100',
            'tanggal_perawatan_selanjutnya' => 'nullable|date',
        ]);

        // kalau user kosongin frekuensi, unit ngikut frekuensi default dari parent barang
        if (empty($validated['frekuensi_perawatan'])) {
            $validated['frekuensi_perawatan'] = $unit->barang->frekuensi_perawatan;
        }

        // kalau tanggal kosong, set otomatis berdasarkan frekuensi (kalau ada)
        // kalau tanggal kosong, set otomatis berdasarkan frekuensi (kalau ada)
        if (empty($validated['tanggal_perawatan_selanjutnya']) && !empty($validated['frekuensi_perawatan'])) {
            $frekuensi = strtolower($validated['frekuensi_perawatan']);

            // ambil angka dari string (contoh: "3 bulan" -> 3)
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

}
