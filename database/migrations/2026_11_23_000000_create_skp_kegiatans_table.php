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
        if (!Schema::hasTable('skp_kegiatans')) {
            Schema::create('skp_kegiatans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nama_mahasiswa')->nullable();
                $table->string('nim')->nullable();
                $table->string('program_studi')->nullable();
                $table->string('fakultas')->nullable();
                $table->string('tahun_akademik')->nullable();
                $table->string('kategori_id')->index();
                $table->string('jenis_kegiatan')->nullable();
                $table->string('nama_kegiatan');
                $table->string('lingkup_tingkat')->nullable();
                $table->string('peringkat_juara')->nullable();
                $table->string('jabatan')->nullable();
                $table->string('sifat_kegiatan')->default('Non-Akademik')->index();
                $table->string('target_role')->default('kabid')->index();
                $table->integer('poin_skp')->default(0);
                $table->date('tanggal_kegiatan')->nullable();
                $table->string('file_bukti')->nullable();
                $table->string('link_bukti')->nullable();
                $table->text('keterangan')->nullable();
                $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending')->index();
                $table->text('catatan_verifikator')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_kegiatans');
    }
};
