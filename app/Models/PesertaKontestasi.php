<?php

namespace App\Models;

use App\Models\DokumenPeserta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaKontestasi extends Model
{
    use HasFactory;

    protected $table = 'peserta_kontestasi';

    protected $fillable = [
        'user_id',
        'nama',
        'nik',
        'tanggal_lahir',
        'tempat_lahir',
        'nomor_hp',
        'pendidikan_terakhir',
        'alamat',
        'email',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'nomor_peserta',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dokumen()
    {
        return $this->hasOne(DokumenPeserta::class, 'nik', 'nik');
    }
}
