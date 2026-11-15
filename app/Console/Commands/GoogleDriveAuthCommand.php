<?php

namespace App\Console\Commands;

use App\Services\GoogleDriveService;
use Illuminate\Console\Command;
use Throwable;

class GoogleDriveAuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drive:auth 
                            {--code= : Google OAuth authorization code}
                            {--check : Check live Google Drive connection and folder access}
                            {--url : Display the Google OAuth authorization URL}
                            {--test-upload : Perform end-to-end upload & delete test to Google Drive}
                            {--sync-local : Sync all existing local prestasi mandiri files to Google Drive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Authorize SIMAWA with Google Drive OAuth and save refresh token to .env';

    /**
     * Execute the console command.
     */
    public function handle(GoogleDriveService $driveService): int
    {
        if (!class_exists(\Google\Client::class)) {
            $this->error('Error: Library Google API Client (google/apiclient) belum terpasang di vendor server.');
            $this->line('Silakan jalankan perintah berikut di Terminal cPanel:');
            $this->info('  composer install --no-dev');
            return Command::FAILURE;
        }

        $clientId = env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET');

        if ($this->option('url')) {
            $client = $driveService->getClient();
            $authUrl = $client->createAuthUrl();
            $this->info('URL Otorisasi Google Drive:');
            $this->line($authUrl);
            return Command::SUCCESS;
        }

        if ($this->option('check')) {
            $this->info('===============================================================');
            $this->info('           PEMERIKSAAN KONEKSI GOOGLE DRIVE SIMAWA             ');
            $this->info('===============================================================');

            if (!$driveService->isConfigured()) {
                $this->error('Status: Konfigurasi belum lengkap di .env');
                $this->line('- Client ID: ' . (empty($clientId) ? 'TIDAK ADA' : 'ADA'));
                $this->line('- Client Secret: ' . (empty($clientSecret) ? 'TIDAK ADA' : 'ADA'));
                $this->line('- Refresh Token: ' . (empty(env('GOOGLE_DRIVE_REFRESH_TOKEN')) || env('GOOGLE_DRIVE_REFRESH_TOKEN') === 'xxx' ? 'BELUM ADA' : 'ADA'));
                return Command::FAILURE;
            }

            $this->info('Status: Konfigurasi lengkap.');
            $this->line('Menguji token akses ke Google API...');

            try {
                $client = $driveService->getClient();
                
                // If access token is set, test it first
                $accessToken = env('GOOGLE_DRIVE_ACCESS_TOKEN');
                if (!empty($accessToken) && $accessToken !== 'xxx') {
                    $client->setAccessToken($accessToken);
                    try {
                        $drive = $driveService->getDrive();
                        $about = $drive->about->get(['fields' => 'user']);
                        $this->info('[OK] Akses token yang ada di .env valid dan aktif untuk akun: ' . ($about->getUser()->getEmailAddress() ?? 'Google'));
                    } catch (Throwable $atErr) {
                        $this->warn('Access token lama tidak berlaku: ' . $atErr->getMessage());
                        $this->line('Mencoba memperbarui dengan Refresh Token...');
                        $token = $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
                        if (isset($token['error'])) {
                            $this->error('Gagal memperbarui Access Token dengan Refresh Token:');
                            $this->line(json_encode($token, JSON_PRETTY_PRINT));
                            return Command::FAILURE;
                        }
                    }
                } else {
                    $token = $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
                    if (isset($token['error'])) {
                        $this->error('Gagal memperbarui Access Token: ' . ($token['error_description'] ?? $token['error']));
                        $this->line(json_encode($token, JSON_PRETTY_PRINT));
                        return Command::FAILURE;
                    }
                }
                $this->info('[OK] Access token berhasil diverifikasi dan aktif.');

                $drive = $driveService->getDrive();
                $this->line('');
                $this->info('Memeriksa akses folder Google Drive:');

                $folders = [
                    'File Sertifikat' => env('GOOGLE_DRIVE_UPLOAD_FILE_SERTIFIKAT'),
                    'File LPJ' => env('GOOGLE_DRIVE_UPLOAD_FILE_LPJ'),
                    'KTM' => env('GOOGLE_DRIVE_UPLOAD_KTM'),
                    'Surat Reward' => env('GOOGLE_DRIVE_UPLOAD_SURAT_PENGAJUAN_REWARD_PRESTASI'),
                    'Surat WR III / WD I' => env('GOOGLE_DRIVE_UPLOAD_SURAT_WRIII_ATAU_WDI'),
                    'Foto Upacara Penyerahan Penghargaan' => env('GOOGLE_DRIVE_UPLOAD_FOTO_UPP'),
                ];

                $tableRows = [];
                foreach ($folders as $name => $rawVal) {
                    $folderId = $driveService->extractFolderId($rawVal);
                    if (!$folderId) {
                        $tableRows[] = [$name, '-', 'Format folder ID / URL tidak valid', 'GAGAL'];
                        continue;
                    }

                    try {
                        $folderMeta = $drive->files->get($folderId, ['fields' => 'id, name, mimeType', 'supportsAllDrives' => true]);
                        $tableRows[] = [$name, $folderId, $folderMeta->getName(), 'TERHUBUNG'];
                    } catch (Throwable $fe) {
                        $tableRows[] = [$name, $folderId, 'Error: ' . $fe->getMessage(), 'PERIKSA IZIN'];
                    }
                }

                $this->table(['Berkas', 'Folder ID', 'Nama Folder Drive', 'Status'], $tableRows);
                $this->info('Semua sistem Google Drive siap digunakan untuk upload berkas!');
                return Command::SUCCESS;
            } catch (Throwable $e) {
                $this->error('Gagal terhubung ke Google Drive API: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        if ($this->option('sync-local')) {
            if (!$driveService->isConfigured()) {
                $this->error('Error: Google Drive belum terkonfigurasi atau token tidak valid.');
                $this->line('Jalankan: php artisan drive:auth --check untuk memeriksa konfigurasi.');
                return Command::FAILURE;
            }

            $this->info('Memulai sinkronisasi berkas lokal Prestasi Mandiri ke Google Drive...');
            $records = \App\Models\PrestasiMandiri::all();
            $count = 0;

            foreach ($records as $record) {
                $updated = false;
                $ownerName = !empty($record->data_mahasiswa[0]['nama']) 
                    ? $record->data_mahasiswa[0]['nama'] 
                    : 'admin';

                $fileMap = [
                    'file_dokumen_sertifikat' => ['key' => 'GOOGLE_DRIVE_UPLOAD_FILE_SERTIFIKAT', 'link' => 'link_dokumen_sertifikat'],
                    'file_dokumen_lpj'        => ['key' => 'GOOGLE_DRIVE_UPLOAD_FILE_LPJ', 'link' => 'link_dokumen_lpj'],
                    'file_ktm'                => ['key' => 'GOOGLE_DRIVE_UPLOAD_KTM', 'link' => 'link_ktm'],
                    'file_pengajuan_reward'   => ['key' => 'GOOGLE_DRIVE_UPLOAD_SURAT_PENGAJUAN_REWARD_PRESTASI', 'link' => 'link_pengajuan_reward'],
                    'file_surat_tugas'        => ['key' => 'GOOGLE_DRIVE_UPLOAD_SURAT_WRIII_ATAU_WDI', 'link' => 'link_surat_tugas'],
                    'file_foto_upp'           => ['key' => 'GOOGLE_DRIVE_UPLOAD_FOTO_UPP', 'link' => 'link_foto_upp'],
                ];

                foreach ($fileMap as $field => $cfg) {
                    $val = $record->{$field};
                    if (!empty($val) && !str_starts_with($val, 'https://drive.google.com')) {
                        if (str_contains($val, '/storage/')) {
                            $val = explode('/storage/', $val)[1] ?? $val;
                        }

                        $this->line("Mengunggah {$field}: {$val}...");
                        try {
                            $res = $driveService->upload($val, $cfg['key'], dirname($val), $ownerName);
                            if ($res['drive']) {
                                $record->{$field} = $res['url'];
                                if (!empty($cfg['link']) && (empty($record->{$cfg['link']}) || !str_starts_with($record->{$cfg['link']}, 'https://drive.google.com'))) {
                                    $record->{$cfg['link']} = $res['url'];
                                }
                                $updated = true;
                                $this->info(" -> Berhasil diunggah ke Google Drive: {$res['url']}");
                            } else {
                                $this->warn(" -> Gagal diunggah ke Google Drive, tetap menggunakan berkas lokal.");
                            }
                        } catch (Throwable $uploadErr) {
                            $this->warn(" -> Melewati berkas karena terjadi error: " . $uploadErr->getMessage());
                        }
                    }
                }

                if ($updated) {
                    $record->save();
                    $count++;
                }
            }

            $this->info("Selesai! {$count} data Prestasi Mandiri berhasil disinkronkan ke Google Drive.");
            return Command::SUCCESS;
        }

        if ($this->option('test-upload')) {
            $this->info('Menguji proses upload nyata ke Google Drive (Folder Sertifikat)...');
            $tempFile = tempnam(sys_get_temp_dir(), 'test_simawa_') . '.txt';
            file_put_contents($tempFile, 'Test upload Google Drive SIMAWA pada ' . now());

            try {
                $result = $driveService->upload($tempFile, 'GOOGLE_DRIVE_UPLOAD_FILE_SERTIFIKAT', 'test');
                $this->info('[OK] Berkas berhasil diunggah!');
                $this->line('  Drive: ' . ($result['drive'] ? 'Ya (Google Drive)' : 'Tidak (Lokal)'));
                $this->line('  URL: ' . $result['url']);

                if ($result['drive'] && !empty($result['url'])) {
                    $this->info('Membersihkan / menghapus berkas uji coba dari Google Drive...');
                    $driveService->deleteFile($result['url']);
                    $this->info('[OK] Berkas uji coba berhasil dibersihkan.');
                }

                @unlink($tempFile);
                return Command::SUCCESS;
            } catch (Throwable $e) {
                $this->error('Gagal menguji upload: ' . $e->getMessage());
                @unlink($tempFile);
                return Command::FAILURE;
            }
        }

        if (empty($clientId) || empty($clientSecret)) {
            $this->error('GOOGLE_DRIVE_CLIENT_ID dan GOOGLE_DRIVE_CLIENT_SECRET belum diatur di .env');
            return Command::FAILURE;
        }

        $client = $driveService->getClient();

        $code = $this->option('code');
        if (empty($code)) {
            $authUrl = $client->createAuthUrl();

            $this->info('===============================================================');
            $this->info('               GOOGLE DRIVE OAUTH AUTHORIZATION                ');
            $this->info('===============================================================');
            $this->line('');
            $this->line('1. Buka tautan berikut di browser Anda:');
            $this->line($authUrl);
            $this->line('');
            $this->line('2. Masuk dengan Akun Google Drive dan setujui izin akses.');
            $this->line('3. Setelah disetujui, salin parameter `code` dari URL callback atau halaman Google.');
            $this->line('');

            $code = $this->ask('Masukkan Authorization Code yang Anda peroleh:');
        }

        if (empty($code)) {
            $this->error('Authorization code tidak boleh kosong.');
            return Command::FAILURE;
        }

        try {
            $token = $client->fetchAccessTokenWithAuthCode(trim($code));

            if (isset($token['error'])) {
                $errorMsg = $token['error_description'] ?? $token['error'];
                $this->error("Gagal memperoleh token: {$errorMsg}");
                return Command::FAILURE;
            }

            $refreshToken = $token['refresh_token'] ?? null;
            $accessToken = $token['access_token'] ?? null;

            if ($refreshToken) {
                GoogleDriveService::updateEnvKey('GOOGLE_DRIVE_REFRESH_TOKEN', $refreshToken);
                $this->info("GOOGLE_DRIVE_REFRESH_TOKEN berhasil disimpan ke .env!");
            } else {
                $this->warn('Peringatan: Google tidak mengembalikan refresh_token baru. Jika sebelumnya sudah pernah diotorisasi, gunakan refresh token yang sudah ada.');
            }

            if ($accessToken) {
                GoogleDriveService::updateEnvKey('GOOGLE_DRIVE_ACCESS_TOKEN', $accessToken);
                $this->info("GOOGLE_DRIVE_ACCESS_TOKEN berhasil disimpan ke .env!");
            }

            $this->info('');
            $this->info('SELAMAT! Google Drive telah aktif dan siap digunakan untuk upload berkas SIMAWA.');
            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

