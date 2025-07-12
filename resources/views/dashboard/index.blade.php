<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Files Dashboard</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon.png') }}">
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 800px;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        h2 {
            font-weight: 700;
            color: #343a40;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: box-shadow 0.3s ease;
        }

        .form-section:hover {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .form-section input[type="file"] {
            width: 100%;
        }

        .form-section .actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-left: 1rem;
        }

        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.875rem;
        }

        .verify-container {
            text-align: center;
            margin-top: 2rem;
        }

        .alert {
            border-radius: 8px;
        }

        .session-timer {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #343a40;
            color: #fff;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 9999;

            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
<div class="session-timer d-flex align-items-center gap-2">
    <button type="button" class="btn btn-sm btn-info" id="panduanButton">
        <i class="bi bi-question-circle"></i> Panduan
    </button>
    <div>
        Sisa sesi: <span id="timer">--:--</span>
    </div>
</div>

<div class="container mt-5">
    <h2>Upload Berkas Dokumen</h2>

    @if ($peserta && $peserta->dokumen)
        <div class="alert alert-info">
            Status Dokumen: <strong>{{ ucfirst($peserta->dokumen->status) }}</strong> <br>
            Telah Dikirim: <strong>{{ $peserta->dokumen->submited === 'yes' ? 'Ya' : 'Belum' }}</strong>
            @if ($peserta->nomor_peserta !== null)
            <br> Nomor Peserta: <strong>{{ $peserta->nomor_peserta }}</strong>

            @endif
        </div>
    @endif

    <form id="uploadForm" enctype="multipart/form-data">
        @csrf

        @php
            $fields = [
                'formulir_pendaftaran' => 'Formulir Pendaftaran (PDF)',
                'fotocopy_ktp' => 'Fotocopy KTP (PDF)',
                'dokumen_putra_putri_abdya' => 'Dokumen Putra-Putri ABDYA (PDF)',
                'ijazah_surat_portofolio' => 'Ijazah / Surat Aktif Kuliah / Portofolio (PDF)',
                'pakta_integritas' => 'Pakta Integritas (PDF)',
            ];
        @endphp

        @foreach($fields as $field => $label)
            <div class="mb-3 d-flex align-items-center">
                <div class="flex-grow-1">
                    <label>{{ $label }}</label>

                    @if ($peserta->dokumen && ($peserta->dokumen->submited === 'yes' || $peserta->dokumen->status === 'approved'))
                        <a href="{{ asset($peserta->dokumen->$field) }}" target="_blank" class="btn btn-outline-primary w-100">
                            Lihat {{ $label }}
                        </a>
                    @else
                        <input type="file" name="{{ $field }}" accept="application/pdf" class="form-control auto-upload">
                        <div class="upload-status"></div>
                    @endif
                </div>

                @if (!empty($peserta->dokumen->$field) && ($peserta->dokumen->submited === 'no' && $peserta->dokumen->status !== 'approved'))
                    <div class="ms-3">
                        <a href="{{ asset($peserta->dokumen->$field) }}" target="_blank" class="btn btn-sm btn-primary">Lihat</a>
                        <button type="button" class="btn btn-sm btn-warning edit-upload" data-input-name="{{ $field }}">Edit</button>
                    </div>
                @endif
            </div>
        @endforeach



        <div class="verify-container">
            @if ($peserta && $peserta->dokumen && $peserta->dokumen->submited === 'no')
                <button id="verifyButton" type="button" class="btn btn-success">Verifikasi & Kirim</button>
            @elseif ($peserta && $peserta->dokumen && $peserta->dokumen->submited === 'yes')
                <div class="alert alert-success mt-3">
                    Berkas sudah diverifikasi & dikirim.
                </div>
            @endif
        </div>
    </form>
</div>
<div class="modal fade" id="panduanModal" tabindex="-1" aria-labelledby="panduanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="panduanModalLabel">Panduan Pengisian Berkas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <ol>
                    <li>Siapkan semua dokumen dalam format <strong>PDF</strong> maksimal ukuran 5MB.</li>
                    <li>Upload setiap dokumen pada field yang tersedia.</li>
                    <li>Untuk jika anda memiliki tim, maka semua data tersebut di jadikan kedalam 1 file pdf terlebih dahulu.</li>
                    <li>Periksa kembali file yang sudah di-upload dengan tombol <strong>Lihat</strong>.</li>
                    <li>Jika sudah lengkap, klik <strong>Verifikasi & Kirim</strong> untuk mengunci data.</li>
                    <li>Data yang sudah diverifikasi tidak dapat diubah lagi.</li>
                    <li>Untuk form upload karya akan di buka mulai tanggal 25 Juli – 21 Agustus 2025</li>
                    <li>Upload karya, peserta akan login kembali nantinya dan melakukan compress file berbentuk zip dan dikirimkan juga melalui aplikasi</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
<script>
$(document).ready(function() {

    $('#panduanButton').click(function(){
        let modal = new bootstrap.Modal(document.getElementById('panduanModal'));
        modal.show();
    });

    $('#verifyButton').click(function() {
        Swal.fire({
            title: 'Konfirmasi Verifikasi',
            text: "Pastikan semua file sudah lengkap sebelum verifikasi.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, verifikasi sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('verifikasi.submit') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Sukses!', response.message, 'success')
                            .then(() => {
                                window.location.reload();
                            });
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            }
        })
    });

    $('.auto-upload').on('change', function() {
        let input = this;
        let file = input.files[0];
        if (!file) return;

        let formData = new FormData();
        formData.append(input.name, file);
        formData.append('_token', '{{ csrf_token() }}');

        let statusDiv = $(this).next('.upload-status');
        statusDiv.text('Uploading...');

        $.ajax({
            url: "{{ route('upload.auto') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                statusDiv.text('Uploaded successfully!');
                location.reload();
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Error';
                statusDiv.text('Upload failed: ' + msg);
            }
        });
    });

    $('.edit-upload').on('click', function() {
        let inputName = $(this).data('input-name');
        $('input[name="'+inputName+'"]').trigger('click');
    });
});
</script>

<script>
    let expireTimestamp = {{ session('guest_login_expire') ?? '0' }} * 1000;
    let timerInterval = setInterval(function(){
        let now = new Date().getTime();
        let distance = expireTimestamp - now;

        if (distance <= 0) {
            clearInterval(timerInterval);
            Swal.fire('Sesi Berakhir', 'Sesi anda telah berakhir, silakan login kembali.', 'warning')
                .then(() => {
                    window.location.href = "{{ route('login') }}";
                });
        } else {
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);
            $('#timer').text(
                ('0' + minutes).slice(-2) + ":" + ('0' + seconds).slice(-2)
            );
        }
    }, 1000);
</script>
</body>
</html>
