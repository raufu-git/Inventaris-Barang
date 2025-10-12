<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
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

        $peminjamans = Peminjaman::with('barang')
            ->when($search, function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('nama_peminjam', 'like', "%{$s}%")
                        ->orWhereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$s}%"));
                });
            })
            ->orderBy('created_at', $sortOrder) // <-- ini pakai parameter sort
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create()
    {
        $barangs = Barang::all();
        return view('peminjaman.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'nama_peminjam' => 'required|string|max:150',
            'tanggal_pinjam' => 'required|date',
            'jumlah_pinjam' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
            'gambar' => 'nullable|image|max:10048',
        ]);

        $barang = Barang::find($validated['barang_id']);
        if ($barang->jumlah_barang <= 0) {
            return back()->with('error', 'Stok barang tidak mencukupi.');
        }

        Peminjaman::create([
            'barang_id' => $validated['barang_id'],
            'nama_peminjam' => $validated['nama_peminjam'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'jumlah_pinjam' => $validated['jumlah_pinjam'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        $barang->decrement('jumlah_barang', 1);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil ditambahkan.');
    }

    public function show(Peminjaman $peminjaman)
    {
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
            'barang_id' => 'required|exists:barangs,id',
            'nama_peminjam' => 'required|string|max:150',
            'tanggal_pinjam' => 'required|date',
            'jumlah_pinjam' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
            'gambar' => 'nullable|image|max:10048',
        ]);

        $oldStatus = $peminjaman->status;
        $peminjaman->update($validated);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'Dipinjam') {
            $peminjaman->barang->increment('jumlah_barang', 1);
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
        if ($peminjaman->status === 'Dikembalikan' || $peminjaman->status === 'Hilang') {
            return back()->with('error', 'Barang ini sudah dikembalikan atau dilaporkan hilang.');
        }

        $request->validate([
            'kondisi_pengembalian' => 'nullable|string|max:255',
        ]);

        $kondisi = $request->kondisi_pengembalian ?: 'Baik';

        // Jika kondisi pengembalian "Hilang", ubah status jadi Hilang
        $statusBaru = $kondisi === 'Hilang' ? 'Hilang' : 'Dikembalikan';

        $peminjaman->update([
            'status' => 'Dikembalikan'  ,
            'tanggal_kembali' => now(),
            'kondisi_pengembalian' => $kondisi,
        ]);

        // Tambah stok hanya jika barang tidak hilang
        if ($kondisi !== 'Hilang') {
            $peminjaman->barang->increment('jumlah_barang', $peminjaman->jumlah_pinjam);
        }

        // Jika kondisi rusak, update kondisi barang juga
        if (in_array($kondisi, ['Rusak Ringan', 'Rusak Berat'])) {
            $peminjaman->barang->update(['kondisi' => $kondisi]);
        }

        return back()->with('success', 'Status peminjaman berhasil diperbarui.');
    }
 
}
