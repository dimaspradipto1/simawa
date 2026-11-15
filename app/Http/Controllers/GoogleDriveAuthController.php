<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Throwable;

class GoogleDriveAuthController extends Controller
{
    public function __construct(
        protected GoogleDriveService $driveService
    ) {}

    /**
     * Redirect to Google OAuth authorization page.
     */
    public function connect(Request $request)
    {
        $clientId = env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET');

        if (empty($clientId) || empty($clientSecret)) {
            Alert::error('Konfigurasi Belum Lengkap', 'GOOGLE_DRIVE_CLIENT_ID dan GOOGLE_DRIVE_CLIENT_SECRET belum diatur di file .env');
            return redirect()->back();
        }

        $client = $this->driveService->getClient();
        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    /**
     * Handle callback from Google OAuth.
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            Alert::error('Gagal Terhubung', 'Akses Google Drive ditolak: ' . $request->query('error'));
            return redirect()->route('dashboard.index');
        }

        $code = $request->query('code');
        if (empty($code)) {
            Alert::error('Kode Otorisasi Tidak Ditemukan', 'Tidak ada authorization code yang diterima dari Google.');
            return redirect()->route('dashboard.index');
        }

        try {
            $client = $this->driveService->getClient();
            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                $errorDesc = $token['error_description'] ?? $token['error'];
                Alert::error('Otorisasi Gagal', 'Gagal memproses token Google: ' . $errorDesc);
                return redirect()->route('dashboard.index');
            }

            $refreshToken = $token['refresh_token'] ?? null;
            $accessToken = $token['access_token'] ?? null;

            if ($refreshToken) {
                GoogleDriveService::updateEnvKey('GOOGLE_DRIVE_REFRESH_TOKEN', $refreshToken);
            }

            if ($accessToken) {
                GoogleDriveService::updateEnvKey('GOOGLE_DRIVE_ACCESS_TOKEN', $accessToken);
            }

            Alert::success(
                'Google Drive Terhubung!',
                'Google Drive berhasil dihubungkan ke SIMAWA. Seluruh file yang diunggah pada formulir prestasi kini akan otomatis tersimpan di folder Google Drive masing-masing.'
            );

            return redirect()->route('prestasi-mandiri.create');
        } catch (Throwable $e) {
            Alert::error('Terjadi Kesalahan', 'Gagal menghubungkan Google Drive: ' . $e->getMessage());
            return redirect()->route('dashboard.index');
        }
    }

    /**
     * Check status of Google Drive configuration and folders.
     */
    public function status()
    {
        $folders = [
            'file_sertifikat' => [
                'env_key'   => 'GOOGLE_DRIVE_UPLOAD_FILE_SERTIFIKAT',
                'folder_id' => $this->driveService->extractFolderId(env('GOOGLE_DRIVE_UPLOAD_FILE_SERTIFIKAT')),
            ],
            'file_lpj' => [
                'env_key'   => 'GOOGLE_DRIVE_UPLOAD_FILE_LPJ',
                'folder_id' => $this->driveService->extractFolderId(env('GOOGLE_DRIVE_UPLOAD_FILE_LPJ')),
            ],
            'file_ktm' => [
                'env_key'   => 'GOOGLE_DRIVE_UPLOAD_KTM',
                'folder_id' => $this->driveService->extractFolderId(env('GOOGLE_DRIVE_UPLOAD_KTM')),
            ],
            'file_reward' => [
                'env_key'   => 'GOOGLE_DRIVE_UPLOAD_SURAT_PENGAJUAN_REWARD_PRESTASI',
                'folder_id' => $this->driveService->extractFolderId(env('GOOGLE_DRIVE_UPLOAD_SURAT_PENGAJUAN_REWARD_PRESTASI')),
            ],
            'file_surat_tugas' => [
                'env_key'   => 'GOOGLE_DRIVE_UPLOAD_SURAT_WRIII_ATAU_WDI',
                'folder_id' => $this->driveService->extractFolderId(env('GOOGLE_DRIVE_UPLOAD_SURAT_WRIII_ATAU_WDI')),
            ],
            'file_foto_upp' => [
                'env_key'   => 'GOOGLE_DRIVE_UPLOAD_FOTO_UPP',
                'folder_id' => $this->driveService->extractFolderId(env('GOOGLE_DRIVE_UPLOAD_FOTO_UPP')),
            ],
        ];

        return response()->json([
            'is_configured' => $this->driveService->isConfigured(),
            'client_id_set' => !empty(env('GOOGLE_DRIVE_CLIENT_ID')),
            'client_secret_set' => !empty(env('GOOGLE_DRIVE_CLIENT_SECRET')),
            'refresh_token_valid' => (!empty(env('GOOGLE_DRIVE_REFRESH_TOKEN')) && env('GOOGLE_DRIVE_REFRESH_TOKEN') !== 'xxx'),
            'folders' => $folders,
        ]);
    }

    /**
     * Synchronize existing local prestasi mandiri files to Google Drive.
     */
    public function syncLocal(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['superadmin', 'adminbkak'])) {
            abort(403, 'Akses khusus administrator.');
        }

        if (!$this->driveService->isConfigured()) {
            Alert::error('Google Drive Belum Siap', 'Google Drive belum terhubung atau konfigurasi di .env belum lengkap.');
            return redirect()->back();
        }

        $records = \App\Models\PrestasiMandiri::all();
        $syncedFilesCount = 0;
        $updatedRecordsCount = 0;

        $fileMap = [
            'file_dokumen_sertifikat' => ['key' => 'GOOGLE_DRIVE_UPLOAD_FILE_SERTIFIKAT', 'subdir' => 'sertifikat', 'link' => 'link_dokumen_sertifikat'],
            'file_dokumen_lpj'        => ['key' => 'GOOGLE_DRIVE_UPLOAD_FILE_LPJ', 'subdir' => 'dokumen_lpj', 'link' => 'link_dokumen_lpj'],
            'file_ktm'                => ['key' => 'GOOGLE_DRIVE_UPLOAD_KTM', 'subdir' => 'ktm', 'link' => 'link_ktm'],
            'file_pengajuan_reward'   => ['key' => 'GOOGLE_DRIVE_UPLOAD_SURAT_PENGAJUAN_REWARD_PRESTASI', 'subdir' => 'surat_reward', 'link' => 'link_pengajuan_reward'],
            'file_surat_tugas'        => ['key' => 'GOOGLE_DRIVE_UPLOAD_SURAT_WRIII_ATAU_WDI', 'subdir' => 'surat_tugas', 'link' => 'link_surat_tugas'],
            'file_foto_upp'           => ['key' => 'GOOGLE_DRIVE_UPLOAD_FOTO_UPP', 'subdir' => 'foto_upp', 'link' => 'link_foto_upp'],
        ];

        foreach ($records as $record) {
            $updated = false;
            $ownerName = !empty($record->data_mahasiswa[0]['nama']) ? $record->data_mahasiswa[0]['nama'] : 'admin';

            foreach ($fileMap as $field => $cfg) {
                $val = $record->{$field};
                // Jika berupa path lokal (bukan link http/https drive)
                if (!empty($val) && !str_starts_with($val, 'https://drive.google.com')) {
                    // Cek jika val adalah full URL lokal seperti https://simawa.../storage/xxx
                    if (str_contains($val, '/storage/')) {
                        $val = explode('/storage/', $val)[1] ?? $val;
                    }

                    $res = $this->driveService->upload($val, $cfg['key'], $cfg['subdir'], $ownerName);
                    if ($res['drive']) {
                        $record->{$field} = $res['url'];
                        if (!empty($cfg['link']) && (empty($record->{$cfg['link']}) || !str_starts_with($record->{$cfg['link']}, 'https://drive.google.com'))) {
                            $record->{$cfg['link']} = $res['url'];
                        }
                        $updated = true;
                        $syncedFilesCount++;
                    }
                }
            }

            if ($updated) {
                $record->save();
                $updatedRecordsCount++;
            }
        }

        if ($syncedFilesCount > 0) {
            Alert::success('Sinkronisasi Berhasil', "Sebanyak {$syncedFilesCount} file pada {$updatedRecordsCount} data prestasi berhasil diunggah ke Google Drive!");
        } else {
            Alert::info('Tidak Ada File Baru', 'Seluruh berkas lokal sudah sinkron ke Google Drive.');
        }

        return redirect()->back();
    }
}

