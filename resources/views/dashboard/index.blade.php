<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Files Dashboard</title>
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .img-preview {
            width: 150px;
            height: auto;
            margin-top: 10px;
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
        }
    </style>
</head>
<body>
<div class="session-timer">
    Sisa sesi: <span id="timer">--:--</span>
</div>

<div class="container mt-5">
    <h2>Upload Berkas Dashboard</h2>
    <form id="uploadForm" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>KTP (gambar)</label>
            <input type="file" name="ktp" accept="image/*" class="form-control">
            <img id="ktpPreview" class="img-preview" style="display:none;">
        </div>

        <div class="mb-3">
            <label>File PDF</label>
            <input type="file" name="pdf_file" accept="application/pdf" class="form-control">
            <p id="pdfName" class="mt-2"></p>
        </div>

        <hr>
        <h4>Demo 4 Upload Gambar</h4>
        <div class="row">
            @for ($i = 1; $i <= 4; $i++)
            <div class="col-md-3 mb-3">
                <input type="file" name="demo_image_{{ $i }}" accept="image/*" class="form-control demoImageInput" data-preview="#demoImagePreview{{ $i }}">
                <img id="demoImagePreview{{ $i }}" class="img-preview" style="display:none;">
            </div>
            @endfor
        </div>

        <hr>
        <h4>Demo 4 Input File Lain</h4>
        <div class="row">
            @for ($i = 1; $i <= 4; $i++)
            <div class="col-md-3 mb-3">
                <input type="file" name="other_file_{{ $i }}" class="form-control">
            </div>
            @endfor
        </div>

        <button type="submit" class="btn btn-theme mt-3">Upload</button>
    </form>
</div>

<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('input[name="ktp"]').on('change', function(){
            previewImage(this, '#ktpPreview');
        });

        $('input[name="pdf_file"]').on('change', function(){
            let fileName = this.files[0] ? this.files[0].name : '';
            $('#pdfName').text(fileName);
        });

        $('.demoImageInput').on('change', function(){
            let previewTarget = $(this).data('preview');
            previewImage(this, previewTarget);
        });

        function previewImage(input, selector){
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e){
                    $(selector).attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#uploadForm').on('submit', function(e){
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('dashboard.upload') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(response){
                    Swal.fire('Sukses', 'File berhasil diupload!', 'success');
                },
                error: function(){
                    Swal.fire('Error', 'Terjadi kesalahan saat upload.', 'error');
                }
            });
        });

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
    });
</script>
</body>
</html>
