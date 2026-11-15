<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiMandiri extends Model
{
    use HasFactory;

    protected $table = 'prestasi_mandiris';

    protected static function booted()
    {
        static::ensureSchemaCompatibility();
    }

    /**
     * Ensure all schema columns exist in the database table dynamically.
     */
    public static function ensureSchemaCompatibility(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('prestasi_mandiris')) {
                $missingColumns = [];
                $columnsToCheck = [
                    'link_pengajuan_reward' => 'string',
                    'link_ktm'              => 'string',
                    'link_surat_tugas'      => 'string',
                    'link_foto_upp'         => 'string',
                    'file_foto_upp'         => 'string',
                    'link_dokumen_undangan' => 'string',
                    'link_dokumen_lpj'      => 'string',
                    'file_dokumen_lpj'      => 'string',
                    'no_hp'                 => 'string',
                    'file_pengajuan_reward' => 'string',
                    'file_ktm'              => 'string',
                    'file_surat_tugas'      => 'string',
                    'url_kompetisi'         => 'string',
                    'jumlah_pt_peserta'     => 'integer',
                ];

                foreach ($columnsToCheck as $col => $type) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('prestasi_mandiris', $col)) {
                        $missingColumns[$col] = $type;
                    }
                }

                if (!empty($missingColumns)) {
                    \Illuminate\Support\Facades\Schema::table('prestasi_mandiris', function (\Illuminate\Database\Schema\Blueprint $table) use ($missingColumns) {
                        foreach ($missingColumns as $col => $type) {
                            if (!\Illuminate\Support\Facades\Schema::hasColumn('prestasi_mandiris', $col)) {
                                if ($type === 'integer') {
                                    $table->integer($col)->nullable();
                                } else {
                                    $table->string($col)->nullable();
                                }
                            }
                        }
                    });
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('PrestasiMandiri schema auto-migration error: ' . $e->getMessage());
        }
    }

    protected $fillable = [
        'level',
        'kategori',
        'nama_kompetisi',
        'nama_cabang',
        'peringkat',
        'nama_penyelenggara',
        'jumlah_pt_peserta',
        'kepesertaan',
        'bentuk',
        'url_kompetisi',
        'link_dokumen_sertifikat',
        'file_dokumen_sertifikat',
        'tanggal_sertifikat',
        'link_foto_upp',
        'file_foto_upp',
        'link_dokumen_undangan',
        'link_dokumen_lpj',
        'file_dokumen_lpj',
        'no_hp',
        'file_pengajuan_reward',
        'link_pengajuan_reward',
        'file_ktm',
        'link_ktm',
        'file_surat_tugas',
        'link_surat_tugas',
        'keterangan',
        'tahun',
        'pt',
        'status',
        'data_mahasiswa',
        'data_dosen',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sertifikat' => 'date',
            'data_mahasiswa' => 'array',
            'data_dosen' => 'array',
        ];
    }
}
