<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peserta_kontestasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('nama');
            $table->string('nik')->unique();
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir');
            $table->string('nomor_hp')->unique();
            $table->string('email')->unique()->nullable(false);
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'S1', 'S2', 'S3']);

            $table->text('alamat');
            $table->string('desa');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('provinsi');
            $table->unsignedBigInteger('nomor_peserta')->nullable();
            $table->timestamps();

            // Relasi ke users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_kontestasi');
    }
};
