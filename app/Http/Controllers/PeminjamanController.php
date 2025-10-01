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
            new Middleware('permission:manage peminjaman', except: ['index', 'show', 'laporan', 'delete']),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->search ?? null;
        $peminjamans = Peminjaman::with('barang')
            ->when($search, fn($q, $s) => $q->where('nama_peminjam', 'like', "%{$s}%"))
            ->orWhereHas('barang', fn($q) => $q->where('nama_barang', 'like', "%{$search}%"))
            ->latest()->paginate(10)->withQueryString();

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

    public function kembalikan(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'Dikembalikan') {
            return back()->with('error', 'Barang ini sudah dikembalikan.');
        }

        $peminjaman->update([
            'status' => 'Dikembalikan',
            'tanggal_kembali' => now(),
        ]);

        $peminjaman->barang->increment('jumlah_barang', $peminjaman->jumlah_pinjam);

        return back()->with('success', 'Barang berhasil dikembalikan.');
    }   

}
