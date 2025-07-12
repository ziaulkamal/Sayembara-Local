<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Studio | Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon.png') }}">
	<link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
	<div id="app" class="app app-full-height app-without-header">
		<div class="login">
			<div class="login-content">
				<form id="loginForm" action="{{ route('send.otp') }}" method="POST">
					@csrf
					<h1 class="text-center">Login</h1>
					<div class="text-muted text-center mb-4">
						Masukkan NIK & Nomor HP untuk menerima OTP.
					</div>
					<div class="mb-3">
						<label class="form-label">NIK</label>
						<input type="text" name="nik" class="form-control form-control-lg fs-15px numonly maxchar-16"
							placeholder="Masukkan NIK" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Nomor HP</label>
						<input type="text" name="nomor_hp" class="form-control form-control-lg fs-15px numonly maxchar-14"
							placeholder="08xxxxxxxxxx" required>
					</div>
					<button type="submit" class="btn btn-theme btn-lg d-block w-100 fw-500 mb-3">Kirim OTP</button>
					<div class="text-center text-muted">
						Belum punya akun? <a href="{{ route('register') }}">Daftar</a>.
					</div>
				</form>

				<div id="otpArea" class="mt-4" style="display:none;">
					<h5 class="text-center mb-3">Masukkan OTP</h5>
					<input type="text" name="otp" class="form-control form-control-lg mb-3" placeholder="Masukkan OTP">
	                <button id="verifyOtpBtn" class="btn btn-theme btn-lg d-block w-100">Verifikasi OTP</button>
				</div>
			</div>
		</div>
		<a href="#" data-click="scroll-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>
	</div>

	<script src="{{ asset('assets/js/custom.js') }}"></script>
	<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
	<script src="{{ asset('assets/js/app.min.js') }}"></script>
	<script>
		$(document).ready(function(){
			$('#loginForm').on('submit', function(e){
				e.preventDefault();
				var formData = new FormData(this);
				$.ajax({
					url: "{{ route('send.otp') }}",
					type: "POST",
					data: formData,
					processData: false,
					contentType: false,
					headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
					success: function(data){
						if(data.status === 'success'){
							Swal.fire('Sukses', data.message, 'success');
							$('#otpArea').show();
							$('#loginForm').hide();
						}else{
							Swal.fire('Gagal', data.message, 'error');
						}
					},
					error: function(xhr){
						let msg = 'Terjadi kesalahan server';
						if(xhr.responseJSON && xhr.responseJSON.message){
							msg = xhr.responseJSON.message;
						}
						Swal.fire('Gagal', msg, 'error');
					}
				});
			});

			$('#verifyOtpBtn').on('click', function(){
				var otp = $('input[name="otp"]').val();
				$.ajax({
					url: "{{ route('verify.otp') }}",
					type: "POST",
					data: JSON.stringify({ otp: otp }),
					contentType: "application/json",
					headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
					success: function(data){
						if(data.status === 'success'){
							Swal.fire('Berhasil', data.message, 'success').then(() => {
								window.location.href = '{{ route("dashboard") }}';
							});
						}else{
							Swal.fire('Gagal', data.message, 'error');
						}
					},
					error: function(xhr){
						let msg = 'Terjadi kesalahan server';
						if(xhr.responseJSON && xhr.responseJSON.message){
							msg = xhr.responseJSON.message;
						}
						Swal.fire('Gagal', msg, 'error');
					}
				});
			});

			// SweetAlert jika session error/message dari middleware
			@if(session('error'))
				Swal.fire('Perhatian', '{{ session('error') }}', 'warning');
			@endif
			@if(session('message'))
				Swal.fire('Informasi', '{{ session('message') }}', 'info');
			@endif
		});
	</script>
</body>

</html>
