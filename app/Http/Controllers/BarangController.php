<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class BarangController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:manage barang', except: ['destroy']),
            new Middleware('permission:delete barang', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $sortOrder = $request->get('sort', 'desc');
    $search = $request->search;

    $barangs = Barang::with(['kategori', 'lokasi'])
        ->when($search, function ($query, $search) {
            $query->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $search . '%');
        })
        ->orderBy('created_at', $sortOrder)
        ->paginate(10)
        ->withQueryString();

    $today = \Carbon\Carbon::today();
    $reminders = Barang::whereNotNull('tanggal_perawatan_selanjutnya')
        ->whereDate('tanggal_perawatan_selanjutnya', '<=', $today)
        ->get();

    return view('barang.index', compact('barangs', 'reminders'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();

        $barang = new Barang();
        return view('barang.create', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah_barang' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|max:10048', 
            'frekuensi_perawatan' => 'nullable|string',
            'custom_frekuensi' => 'nullable|string',
            'tanggal_perawatan_selanjutnya' => 'nullable|date',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store(null, 'gambar-barang');
        }
        if ($validated['frekuensi_perawatan'] === 'lainnya' && !empty($validated['custom_frekuensi'])) {
            $validated['frekuensi_perawatan'] = $validated['custom_frekuensi'];
        }

        unset($validated['custom_frekuensi']); 
        Barang::create($validated);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        $barang->load(['kategori', 'lokasi']);
        return view('barang.show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();
        return view('barang.edit', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah_barang' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif  |max:10048',
            'frekuensi_perawatan' => 'nullable|string',
            'custom_frekuensi' => 'nullable|string',
            'tanggal_perawatan_selanjutnya' => 'nullable|date',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($barang->gambar) {
                Storage::disk('gambar-barang')->delete($barang->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store(null, 'gambar-barang');
        }

        if ($validated['frekuensi_perawatan'] === 'lainnya' && !empty($validated['custom_frekuensi'])) {
            $validated['frekuensi_perawatan'] = $validated['custom_frekuensi'];
        }

        unset($validated['custom_frekuensi']); 
        $barang->update($validated);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        // Hapus gambar jika ada
        if ($barang->gambar) {
            Storage::disk('gambar-barang')->delete($barang->gambar);
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus.');
    }

    public function cetakLaporan()
    {
        $barangs = Barang::with(['kategori', 'lokasi'])->get();

        $data = [
            'title' => 'Laporan Data Barang Inventaris',
            'date' => date('d F Y'),
            'barangs' => $barangs,
        ];

        $pdf = Pdf::loadView('barang.laporan', $data);

        return $pdf->stream('laporan-inventaris-barang.pdf');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $barangs = \App\Models\Barang::with(['kategori:id,nama_kategori', 'lokasi:id,nama_lokasi'])
            ->where('nama_barang', 'like', "%{$query}%")
            ->select(
                'id',
                'nama_barang',
                'jumlah_barang',
                'kondisi',
                'kategori_id',
                'lokasi_id',
                'satuan',
                'tanggal_pengadaan',
                'updated_at',
                'frekuensi_perawatan',
                'tanggal_perawatan_selanjutnya'
            )
            ->limit(10)
            ->get();

        return response()->json($barangs);
    }
    
   public function konfirmasiPerawatan($id)
    {
        $barang = Barang::findOrFail($id);

        $frekuensi = strtolower($barang->frekuensi_perawatan ?? '');

        // Ambil angka dari string (contoh: "3 bulan" -> 3)
        preg_match('/(\d+)/', $frekuensi, $angka);
        $jumlah = isset($angka[1]) ? (int)$angka[1] : 1; // default 1 kalau kosong

        // Tentukan jenis interval
        if (str_contains($frekuensi, 'bulan')) {
            $barang->tanggal_perawatan_selanjutnya = \Carbon\Carbon::now()->addMonths($jumlah);
        } elseif (str_contains($frekuensi, 'minggu')) {
            $barang->tanggal_perawatan_selanjutnya = \Carbon\Carbon::now()->addWeeks($jumlah);
        } elseif (str_contains($frekuensi, 'hari')) {
            $barang->tanggal_perawatan_selanjutnya = \Carbon\Carbon::now()->addDays($jumlah);
        } else {
            $barang->tanggal_perawatan_selanjutnya = \Carbon\Carbon::now()->addMonth();
        }

        $barang->save();

        return redirect()->back()->with('success', '✅ Jadwal perawatan berhasil diperbarui!');
    }
}
