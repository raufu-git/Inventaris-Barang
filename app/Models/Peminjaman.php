<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';
    // aman untuk mass assignment

    protected $fillable = [
    'barang_id',
    'user_id',
    'nama_peminjam',
    'no_hp',
    'kelas_divisi',
    'tanggal_pinjam',
    'jumlah_pinjam',
    'tanggal_kembali',
    'status',
    'keterangan',
    'gambar',
    'kondisi_pengembalian',
    'kondisi_awal',
    ];


    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
