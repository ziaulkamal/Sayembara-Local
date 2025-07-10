<?php

namespace App\Http\Controllers;

use App\Models\PesertaKontestasi;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class PartisipanController extends Controller
{
    public function register()
    {
        return view('form.register');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => [
                'required',
                'string',
                'size:16',
                Rule::unique('peserta_kontestasi', 'nik')
            ],
            'tanggal' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'tempat_lahir' => 'required|string|max:50',
            'nomor_hp' => [
                'required',
                'string',
                'regex:/^08[0-9]{8,11}$/',
                Rule::unique('peserta_kontestasi', 'nomor_hp')
            ],
            'email' => [
                'required',
                'email',
                'unique:peserta_kontestasi,email',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/@(gmail\.com|yahoo\.com|yahoo\.co\.id)$/', $value)) {
                        $fail('Email hanya boleh menggunakan domain gmail.com, yahoo.com, atau yahoo.co.id.');
                    }
                }
            ],
            'pendidikan_terakhir' => 'required|in:SD,SMP,SMA,S1,S2,S3',
            'alamat' => 'required|string',
            'desa' => 'required|string|max:50',
            'kecamatan' => 'required|string|max:50',
            'kabupaten' => 'required|string|max:50',
            'provinsi' => 'required|string|max:50',
        ], [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'size' => ':attribute harus :size karakter.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'in' => ':attribute tidak valid.',
            'unique' => ':attribute sudah terdaftar.',
            'regex' => ':attribute tidak sesuai format (harus diawali 08 dan panjang wajar).'
        ], [
            'tanggal' => 'Tanggal',
            'bulan' => 'Bulan',
            'tahun' => 'Tahun',
            'nama' => 'Nama',
            'nik' => 'NIK',
            'tanggal_lahir' => 'Tanggal Lahir',
            'tempat_lahir' => 'Tempat Lahir',
            'nomor_hp' => 'Nomor HP',
            'pendidikan_terakhir' => 'Pendidikan Terakhir',
            'alamat' => 'Alamat',
            'desa' => 'Desa',
            'kecamatan' => 'Kecamatan',
            'kabupaten' => 'Kabupaten',
            'provinsi' => 'Provinsi',
        ]);
        $tanggal_lahir = "{$request->tahun}-{$request->bulan}-{$request->tanggal}";

        $store = PesertaKontestasi::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $tanggal_lahir,
            'nomor_hp' => $request->nomor_hp,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'alamat' => $request->alamat,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'email' => $request->email, // atau '' jika tidak pakai
        ]);

        // dd($store);
        return redirect()->back()->with('success', 'Data peserta berhasil disimpan.');
    }

    public function login()
    {
        return view('form.login'); // form login OTP
    }

    public function sendOtp(Request $request)
    {
        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'nomor_hp.required' => 'Nomor HP wajib diisi.',
            'nomor_hp.regex' => 'Format nomor HP tidak valid. Harus diawali 08 dan panjang wajar.'
        ];

        $request->validate([
            'nik' => 'required|string|size:16',
            'nomor_hp' => 'required|string|regex:/^08[0-9]{8,11}$/'
        ], $messages);

        $peserta = PesertaKontestasi::where('nik', $request->nik)
            ->where('nomor_hp', $request->nomor_hp)
            ->first();

        if (!$peserta) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIK atau Nomor HP tidak ditemukan dalam sistem.'
            ], 404);
        }

        // generate OTP 6 digit
        $otp = rand(100000, 999999);
        Session::put('otp', $otp);
        Session::put('nik', $peserta->nik);
        $this->sendOtpWhatsapp($request->nomor_hp, $otp);
        Log::info("OTP $otp untuk NIK {$peserta->nik} telah dikirim ke nomor {$request->nomor_hp}");
        // **DI PRODUCTION** kirim via SMS
        // Untuk testing tampilkan langsung
        return response()->json([
            'status' => 'success',
            'message' => "OTP berhasil dikirim Ke WhatsApp."
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $otpInput = $request->otp;
        $otpSession = Session::get('otp');

        if (!$otpSession) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP tidak ditemukan atau sesi telah kadaluarsa.'
            ], 400);
        }

        if ($otpInput != $otpSession) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP yang Anda masukkan salah.'
            ], 422);
        }

        // sukses login
        Session::forget('otp');
        Session::put('guest_login', true);
        Session::put('guest_login_expire', now()->addMinutes(60)->timestamp); // 1 menit

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil login. Anda akan diarahkan ke dashboard.'
        ]);
    }

    public function dashboard()
    {
    //    dd(Session::get('guest_login'));

        return view('dashboard.index');
    }

    private function sendOtpWhatsapp($nomorHp, $otp)
    {
        // format nomor menjadi internasional dan diikuti @c.us
        $nomorInternasional = preg_replace('/^08/', '628', $nomorHp) . '@c.us';

        $payload = [
            "chatId" => $nomorInternasional,
            "reply_to" => null,
            "text" => "OTP Anda adalah $otp. Jangan bagikan ke siapa pun.",
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
    }
}
