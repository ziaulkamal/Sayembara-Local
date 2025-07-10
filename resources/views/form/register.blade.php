<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Studio | Register Peserta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app" class="app app-full-height app-without-header">
        <div class="register">
            <div class="register-content">
                <form action="{{ route('register.store') }}" method="POST">
                    @csrf
                    <h1 class="text-center">Register Peserta Kontestasi</h1>
                    <p class="text-muted text-center">Lengkapi data berikut untuk mendaftar.</p>

                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control form-control-lg fs-15px" placeholder="Nama Lengkap" value="{{ old('nama') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Induk Kependudukan <span class="text-danger">*</span></label>
                        <input type="text" name="nik" class="form-control form-control-lg fs-15px numonly maxchar-16" placeholder="Nomor Induk Kependudukan" value="{{ old('nik') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control form-control-lg fs-15px" placeholder="Kota/Kabupaten Lahir" value="{{ old('tempat_lahir') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-4">
                                <select name="tanggal" class="form-select form-select-lg fs-15px">
                                    <option value="">Tanggal</option>
                                    @for ($i = 1; $i <= 31; $i++)
                                        <option value="{{ sprintf('%02d', $i) }}" {{ old('tanggal') == sprintf('%02d', $i) ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <select name="bulan" class="form-select form-select-lg fs-15px">
                                    <option value="">Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ sprintf('%02d', $i) }}" {{ old('bulan') == sprintf('%02d', $i) ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <select name="tahun" class="form-select form-select-lg fs-15px">
                                    <option value="">Tahun</option>
                                    @for ($i = date('Y'); $i >= 1900; $i--)
                                        <option value="{{ $i }}" {{ old('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_hp" class="form-control form-control-lg fs-15px numonly maxchar-14" placeholder="08XXXXXXXXXX" value="{{ old('nomor_hp') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-lg fs-15px" placeholder="example@gmail.com" value="{{ old('email') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                        <select name="pendidikan_terakhir" class="form-control form-control-lg fs-15px">
                            <option value="">-- Pilih Pendidikan --</option>
                            @foreach(['SD','SMP','SMA','S1','S2','S3'] as $pend)
                                <option value="{{ $pend }}" {{ old('pendidikan_terakhir') == $pend ? 'selected' : '' }}>{{ $pend }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control form-control-lg fs-15px" rows="3" placeholder="Alamat Lengkap">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Desa <span class="text-danger">*</span></label>
                        <input type="text" name="desa" class="form-control form-control-lg fs-15px" placeholder="Nama Desa" value="{{ old('desa') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                        <input type="text" name="kecamatan" class="form-control form-control-lg fs-15px" placeholder="Nama Kecamatan" value="{{ old('kecamatan') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kabupaten <span class="text-danger">*</span></label>
                        <input type="text" name="kabupaten" class="form-control form-control-lg fs-15px" placeholder="Nama Kabupaten" value="{{ old('kabupaten') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <input type="text" name="provinsi" class="form-control form-control-lg fs-15px" placeholder="Nama Provinsi" value="{{ old('provinsi') }}">
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-theme btn-lg fs-15px fw-500 d-block w-100">Daftar</button>
                    </div>

                    <div class="text-muted text-center">
                        Sudah pernah daftar? <a href="{{ route('login') }}">Login di sini</a>
                    </div>
                </form>
            </div>
        </div>
        <a href="#" data-click="scroll-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '{!! implode("<br>", $errors->all()) !!}'
        });
    </script>
    @endif

    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}'

        });
    </script>
    @endif

    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
</body>
</html>
