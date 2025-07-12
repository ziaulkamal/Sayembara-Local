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
        Schema::create('dokumen_peserta', function (Blueprint $table) {
            $table->id();
            $table->string('nik'); // relasi manual via nik
            $table->string('formulir_pendaftaran')->nullable();
            $table->string('fotocopy_ktp')->nullable();
            $table->string('dokumen_putra_putri_abdya')->nullable();
            $table->string('ijazah_surat_portofolio')->nullable();
            $table->string('pakta_integritas')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('submited', ['yes', 'no'])->default('no');
            $table->timestamps();

            $table->foreign('nik')->references('nik')->on('peserta_kontestasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_peserta');
    }
};
