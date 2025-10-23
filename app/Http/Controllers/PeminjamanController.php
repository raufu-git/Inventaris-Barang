<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\Unit;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // untuk laporan PDF
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PeminjamanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view peminjaman', only: ['index', 'show', 'laporan']),
            new Middleware('permission:manage peminjaman', except: ['index', 'show', 'laporan']),
        ];
    }

    public function index(Request $request)
    {
        $sortOrder = $request->get('sort', 'desc');
        $search = $request->get('search');
        $status = $request->get('status'); // ambil filter status dari form

        $peminjamans = Peminjaman::with('barang')
            ->when($search, function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('nama_peminjam', 'like', "%{$s}%")
                        ->orWhereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$s}%"));
                });
            })
            ->when($status, function ($q, $st) {
                if ($st === 'Hilang') {
                    // status hilang sebenernya dikembalikan tapi kondisi_pengembalian = Hilang
                    $q->where('status', 'Dikembalikan')
                    ->where('kondisi_pengembalian', 'Hilang');
                } elseif ($st === 'Dikembalikan') {
                    // tampilkan yg dikembalikan tapi bukan hilang
                    $q->where('status', 'Dikembalikan')
                    ->where(function ($sub) {
                        $sub->whereNull('kondisi_pengembalian')
                            ->orWhere('kondisi_pengembalian', '!=', 'Hilang');
                    });
                } else {
                    $q->where('status', $st);
                }
            })
            ->orderBy('created_at', $sortOrder)
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create()
    {
        $barangs = Barang::all();
    $units = \App\Models\Unit::with('barang')
        ->whereIn('kondisi', ['Baik', 'Rusak Ringan']) // cuma ambil yang kondisi baik/ringan
        ->get();

        return view('peminjaman.create', compact('barangs', 'units'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'barang_id' => 'required|exists:barangs,id',
        'unit_id' => 'required|exists:units,id', // ✅ tambahkan validasi unit
        'nama_peminjam' => 'required|string|max:150',
        'tanggal_pinjam' => 'required|date',
        'jumlah_pinjam' => 'required|integer|min:1',
        'keterangan' => 'nullable|string',
        'gambar' => 'nullable|image|max:10048',
        'no_hp' => 'nullable|string|max:20',
        'kelas_divisi' => 'nullable|string|max:50',
    ]);

    $barang = Barang::findOrFail($validated['barang_id']);
    $unit = Unit::findOrFail($validated['unit_id']);

    // ✅ Pastikan unit masih tersedia (misal status = "Tersedia")
    // if ($unit->status !== 'Tersedia') {
    //     return back()->with('error', 'Unit ini sedang tidak tersedia untuk dipinjam.');
    // }

    // 🔥 Cek stok
    if ($barang->jumlah_barang < $validated['jumlah_pinjam']) {
        return back()->with('error', 'Jumlah barang yang dipinjam melebihi stok yang tersedia.');
    }

    // ✅ Simpan data peminjaman
    Peminjaman::create([
        'barang_id' => $validated['barang_id'],
        'unit_id' => $validated['unit_id'],
        'nama_peminjam' => $validated['nama_peminjam'],
        'tanggal_pinjam' => $validated['tanggal_pinjam'],
        'jumlah_pinjam' => $validated['jumlah_pinjam'],
        'keterangan' => $validated['keterangan'] ?? null,
        'kondisi_awal' => $unit->kondisi ?? '-', // ambil langsung dari tabel units
        'no_hp' => $validated['no_hp'] ?? null,
        'kelas_divisi' => $validated['kelas_divisi'] ?? null,
        'status' => 'Dipinjam',
    ]);

    // ✅ Kurangi stok barang + ubah status unit
    $barang->decrement('jumlah_barang', $validated['jumlah_pinjam']);
    $unit->update(['status' => 'Dipinjam']);

    return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil ditambahkan.');
}


    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['unit.barang']); // <-- ini penting
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function edit(Peminjaman $peminjaman)
    {
        $barangs = Barang::all();
        return view('peminjaman.edit', compact('peminjaman', 'barangs'));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'barang_id' => 'required|integer|exists:barangs,id',
            'nama_peminjam' => 'required|string|max:150',
            'tanggal_pinjam' => 'required|date',
            'jumlah_pinjam' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
            'gambar' => 'nullable|image|max:10048',
            'kondisi_awal' => 'nullable|string|max:25',
            'no_hp' => 'nullable|string|max:20',
            'kelas_divisi' => 'nullable|string|max:50',
        ]);

        $oldStatus = $peminjaman->status;
        $peminjaman->update($validated);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'Dipinjam') {
            $peminjaman->barang->increment('jumlah_barang', $peminjaman->jumlah_pinjam);
        }

        $peminjaman->delete();
        return redirect()->route('peminjaman.index')->with('deleted', 'Data peminjaman telah dihapus.');
    }

    public function laporan()
    {
        $peminjamans = Peminjaman::with('barang')->get();
        $pdf = Pdf::loadView('peminjaman.laporan', compact('peminjamans'))->setPaper('a4', 'portrait');
        return $pdf->stream('laporan-peminjaman.pdf');
    }

    public function kembalikan(Request $request, Peminjaman $peminjaman)
{
    // Cegah pengembalian dobel
    if ($peminjaman->status === 'Dikembalikan' || $peminjaman->status === 'Hilang') {
        return back()->with('error', 'Barang ini sudah dikembalikan atau dilaporkan hilang.');
    }

    // Validasi input
    $request->validate([
        'kondisi_pengembalian' => 'nullable|string|max:255',
        'catatan_pengembalian' => 'nullable|string|max:500',
    ]);

    // Kalau kosong, ambil dari kondisi_awal
    $kondisi = $request->kondisi_pengembalian ?: $peminjaman->kondisi_awal;

    // Tentukan status baru
    $statusBaru = $kondisi === 'Hilang' ? 'Hilang' : 'Dikembalikan';

    // Update data peminjaman
    $peminjaman->update([
        'status' => 'Dikembalikan',
        'tanggal_kembali' => now(),
        'kondisi_pengembalian' => $kondisi,
        'catatan_pengembalian' => $request->catatan_pengembalian,
    ]);

    // Tambah stok kalau tidak hilang
    if ($kondisi !== 'Hilang') {
        $peminjaman->barang->increment('jumlah_barang', $peminjaman->jumlah_pinjam);
    }

    // Jika kondisi rusak, update kondisi barang
    if (in_array($kondisi, ['Rusak Ringan', 'Rusak Berat'])) {
        $peminjaman->barang->update(['kondisi' => $kondisi]);
    }

    return back()->with('success', 'Status peminjaman berhasil diperbarui.');
}

 
}
