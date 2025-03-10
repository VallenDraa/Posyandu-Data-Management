<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTuaModel extends Model
{
    use HasFactory;

    /**
     * Mengatur nama tabel database yang dituju.
     * 
     * @var string
     */
    protected $table = 'orang_tua';

    /**
     * Atribut atau kolom yang boleh diubah.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_ayah',
        'nik_ayah',
        'nama_ibu',
        'nik_ibu',
        'tanggal_meninggal_ibu',
        'no_telp',
        'rt_rw',
        'tempat_tinggal',
    ];

}
