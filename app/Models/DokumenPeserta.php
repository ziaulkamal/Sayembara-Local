<?php

namespace App\Models;

use App\Models\PesertaKontestasi;
use Illuminate\Database\Eloquent\Model;

class DokumenPeserta extends Model
{
    protected $table = 'dokumen_peserta';
    protected $fillable = [
        'nik',
        'formulir_pendaftaran',
        'fotocopy_ktp',
        'dokumen_putra_putri_abdya',
        'ijazah_surat_portofolio',
        'pakta_integritas',
        'status',
        'submited'
    ];

    public function peserta()
    {
        return $this->belongsTo(PesertaKontestasi::class, 'nik', 'nik');
    }
}
