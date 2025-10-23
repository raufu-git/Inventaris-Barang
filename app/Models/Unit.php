<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'kode_unit',
        'kondisi',
        'tanggal_perawatan_selanjutnya',
    ];

    public function barang()
    {
        return $this->belongsTo(\App\Models\Barang::class, 'barang_id');
    }

public function peminjamans()
{
    return $this->hasMany(Peminjaman::class);
}


}
