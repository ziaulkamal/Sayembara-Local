<?php

namespace App\Http\Controllers;

use App\Models\DokumenPeserta;
use App\Models\PesertaKontestasi;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class UploadDokumenController extends Controller
{
    public function autoUpload(Request $request)
    {
        $nik = Session::get('nik');

        $peserta = PesertaKontestasi::where('nik', $nik)->first();
        if (!$peserta) {
            return response()->json(['message' => 'Peserta tidak ditemukan'], 404);
        }

        $dokumen = DokumenPeserta::firstOrCreate(['nik' => $nik], ['peserta_kontestasi_id' => $peserta->id]);

        $fields = [
            'formulir_pendaftaran',
            'fotocopy_ktp',
            'dokumen_putra_putri_abdya',
            'ijazah_surat_portofolio',
            'pakta_integritas'
        ];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                $request->validate([
                    $field => 'mimes:pdf|max:5120' // 5MB
                ], [
                    'mimes' => 'File harus berupa PDF.',
                    'max' => 'Ukuran file maksimal 5MB.'
                ]);

                $file = $request->file($field);

                // Extra hardening: cek mime type betulan
                if ($file->getMimeType() !== 'application/pdf') {
                    return response()->json(['message' => 'File bukan PDF valid'], 422);
                }

                // Hapus file lama
                if ($dokumen->$field) {
                    Storage::disk('public')->delete($dokumen->$field);
                }

                // Simpan file baru
                $path = $file->store("uploads/$nik", 'public');

                // Update database
                $dokumen->$field = $path;
                $dokumen->save();

                return response()->json([
                    'success' => true,
                    'field' => $field,
                    'path' => asset('storage/' . $path)
                ]);
            }
        }

        return response()->json(['message' => 'Tidak ada file yang dikirim'], 400);
    }

    public function verifikasiSubmit(Request $request)
    {
        $nik = Session::get('nik');

        $dokumen = DokumenPeserta::where('nik', $nik)->first();
        if (!$dokumen) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        $peserta = PesertaKontestasi::where('nik', $nik)->first();
        if (!$peserta) {
            return response()->json(['message' => 'Peserta tidak ditemukan'], 404);
        }

        // Validasi semua file sudah diupload
        $fields = [
            'formulir_pendaftaran',
            'fotocopy_ktp',
            'dokumen_putra_putri_abdya',
            'ijazah_surat_portofolio',
            'pakta_integritas'
        ];

        foreach ($fields as $field) {
            if (!$dokumen->$field) {
                return response()->json(['message' => "Dokumen $field belum lengkap"], 422);
            }
        }

        // Generate nomor peserta unik 6 digit jika belum ada
        if (!$peserta->nomor_peserta) {
            do {
                $nomorPeserta = mt_rand(100000, 999999);
            } while (PesertaKontestasi::where('nomor_peserta', $nomorPeserta)->exists());

            $peserta->nomor_peserta = $nomorPeserta;
            $peserta->save();
        }

        // Update submited dokumen
        $dokumen->submited = 'yes';
        $dokumen->save();

        $nomorInternasional = preg_replace('/^08/', '628', $peserta->nomor_hp) . '@c.us';
        $payload = [
            "chatId" => $nomorInternasional,
            "reply_to" => null,
            "text" => "Terima kasih telah melukan verifikasi dokumen.\n\n" .
                      "Nomor Peserta Anda: {$peserta->nomor_peserta}\n" .
                      "Kami akan segera melakukan verifikasi terkait dokumen yang dikrimkan.\n" .
                      "Jika nantinya dokumen anda telah di approve, maka anda dibenarkan untuk mengikuti step berikutnya dalam mekukan upload hasil karya.\n\n" .
                      "Untuk informasi lainya akan disampaikan melalui sosial media resmi kami.\n\n",
            "linkPreview" => true,
            "linkPreviewHighQuality" => false,
            "session" => "default"
        ];

        try {
            $client = new Client([
                'base_uri' => env('URL_GATEWAY_WA'),
                'timeout'  => 10.0,
                'verify'   => false, // kalau SSL self-signed
            ]);

            $response = $client->post('/api/sendText', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::error("Gagal kirim OTP WA: " . $response->getBody());
            }
        } catch (\Exception $e) {
            Log::error("Exception saat kirim OTP WA: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diverifikasi & dikirim',
            'nomor_peserta' => $peserta->nomor_peserta
        ]);
    }
}