<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SkpKegiatan extends Model
{
    use HasFactory;

    protected $table = 'skp_kegiatans';

    protected $fillable = [
        'user_id',
        'nama_mahasiswa',
        'nim',
        'program_studi',
        'fakultas',
        'tahun_akademik',
        'kategori_id',
        'jenis_kegiatan',
        'nama_kegiatan',
        'lingkup_tingkat',
        'peringkat_juara',
        'jabatan',
        'sifat_kegiatan',
        'target_role',
        'poin_skp',
        'tanggal_kegiatan',
        'file_bukti',
        'link_bukti',
        'keterangan',
        'status',
        'catatan_verifikator',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'verified_at'      => 'datetime',
        'poin_skp'         => 'integer',
    ];

    /**
     * Pastikan tabel dan kolom selalu siap di server tanpa error SQL.
     */
    public static function ensureSchemaCompatibility(): void
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
     * Mahasiswa pemilik kegiatan.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Pejabat yang memverifikasi.
     */
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope filter berdasarkan kewenangan role pengguna.
     */
    public function scopeForUser($query, User $user)
    {
        if ($user->isMahasiswa()) {
            return $query->where('user_id', $user->id);
        }

        if ($user->isWadek1()) {
            // Wakil Dekan 1: Khusus sifat Akademik dari fakultasnya
            $query->where('sifat_kegiatan', 'Akademik');
            if (!empty($user->fakultas) && !str_contains(strtolower($user->fakultas), 'semua')) {
                $query->where('fakultas', $user->fakultas);
            }
            return $query;
        }

        if ($user->isKabid()) {
            // Kabid Kemahasiswaan: Seluruh kegiatan Non-Akademik se-Universitas
            return $query->where('sifat_kegiatan', 'Non-Akademik');
        }

        if ($user->role === 'prodi') {
            if (!empty($user->program_studi)) {
                return $query->where('program_studi', $user->program_studi);
            }
        }

        // Superadmin & Admin BKAK bisa melihat semua
        return $query;
    }

    /**
     * URL File Bukti (Google Drive atau Lokal Storage).
     */
    public function getBuktiUrlAttribute(): ?string
    {
        if (!empty($this->link_bukti) && str_starts_with($this->link_bukti, 'http')) {
            return $this->link_bukti;
        }

        if (!empty($this->file_bukti)) {
            if (str_starts_with($this->file_bukti, 'http')) {
                return $this->file_bukti;
            }
            return asset('storage/' . $this->file_bukti);
        }

        return null;
    }
}
