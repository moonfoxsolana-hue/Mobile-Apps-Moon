<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\YouTube;
use Illuminate\Support\Facades\Log;

class YoutubeMimpiController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(env('YOUTUBE_CLIENT_ID'));
        $this->client->setClientSecret(env('YOUTUBE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('YOUTUBE_REDIRECT_URI'));
        $this->client->setScopes([
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube.readonly'
        ]);
        $this->client->setAccessType('offline'); // Penting agar dapat Refresh Token
        $this->client->setPrompt('consent');
    }

    // 1. Buka link ini di browser untuk login
    public function redirectToGoogle()
    {
        $authUrl = $this->client->createAuthUrl();
        return redirect($authUrl);
    }

    // 2. Google akan melempar balik ke sini
    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('code')) {
            $token = $this->client->fetchAccessTokenWithAuthCode($request->code);
            
            // SIMPAN TOKEN INI KE DATABASE/FILE
            // Token ini berisi 'access_token' dan 'refresh_token'
            // Anda butuh 'refresh_token' untuk upload otomatis besok-besok tanpa login lagi.
            file_put_contents(storage_path('app/mimpi_google-token.json'), json_encode($token));
            
            return "Login Berhasil! Token disimpan.";
        }
        return "Gagal login.";
    }

    // 3. Fungsi Upload Video
    public function uploadVideo($title, $description, $videoId)
    {
        // Load token yang sudah disimpan
        $tokenPath = storage_path('app/mimpi_google-token.json');
        if (!file_exists($tokenPath)) {
            return "Belum login, silakan akses route login dulu.";
        }
        $this->refreshToken();

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $this->client->setAccessToken($accessToken);

        // Cek jika token expired, refresh otomatis
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }

        $youtube = new YouTube($this->client);
        $videoPath = storage_path('app/public/story_video_mimpi/' . $videoId . '.mp4'); // Pastikan file ada

        // Setup Metadata
        $snippet = new YouTube\VideoSnippet();
        $snippet->setTitle($title);
        $snippet->setDescription($description);
        $snippet->setTags(['dikasi mimpi', 'mimpi', 'tafsirmimpi', 'artimimpi', 'maknamimpi', 'primbonmimpi', 'alam bawah sadar', 'dreaminterpretation']);
        $snippet->setCategoryId("24");

        $status = new YouTube\VideoStatus();
        $status->privacyStatus = "public"; 

        $video = new YouTube\Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        // Proses Upload
        $chunkSizeBytes = 1 * 1024 * 1024;
        $this->client->setDefer(true);
        $insertRequest = $youtube->videos->insert("status,snippet", $video);
        
        $media = new \Google\Http\MediaFileUpload(
            $this->client,
            $insertRequest,
            'video/mp4',
            null,
            true,
            $chunkSizeBytes
        );
        $media->setFileSize(filesize($videoPath));

        $status = false;
        $handle = fopen($videoPath, "rb");
        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }
        fclose($handle);

        $this->client->setDefer(false);
        Log::Info('Proses Upload Ke Youtube Selesai - Youtube Dikasi Mimpi');

        return $status['id'];
    }


   public function uploadThumbnailVideo($youtube_id, $thumbnailPath)
    {
        // Pastikan path absolut benar
        $fullPath = public_path($thumbnailPath);

        if (!file_exists($fullPath)) {
            return "File tidak ditemukan di: " . $fullPath;
        }

        try {
            // Ambil token dari database atau session
            $tokenPath = storage_path('app/mimpi_google-token.json');
            if (!file_exists($tokenPath)) {
                $tokenPathBackup = storage_path('app/mimpi_google-token.json');
                if (!file_exists($tokenPathBackup)) {
                    return "Belum login, silakan akses route login dulu.";
                }
                $tokenPath = $tokenPathBackup;
            }
            $this->refreshToken();

            $accessToken = json_decode(file_get_contents($tokenPath), true);
            // Set token ke client
            $this->client->setAccessToken($accessToken);

            // Cek apakah token sudah expired, jika ya, refresh
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                // Simpan token baru ke database jika perlu
            }
            $youtube = new \Google\Service\YouTube($this->client);

            // Ambil konten file
            $data = file_get_contents($fullPath);
            $mimeType = mime_content_type($fullPath);

            // Gunakan parameter 'uploadType' => 'multipart' untuk file kecil seperti thumbnail
            $response = $youtube->thumbnails->set($youtube_id, [
                'data' => $data,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart'
            ]);

            return "Upload Sukses! Thumbnail berhasil diunggah untuk Youtube video ID: " . $youtube_id;
        } catch (\Google\Service\Exception $e) {
            return "Google API Error: " . $e->getMessage();
        } catch (\Exception $e) {
            return "General Error: " . $e->getMessage();
        }
    }

    public function refreshToken()
    {
        $tokenPath = storage_path('app/mimpi_google-token.json');
        if (!file_exists($tokenPath)) {
            return "Belum login, silakan akses route login dulu.";
        }

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $this->client->setAccessToken($accessToken);

        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
            return "Token berhasil di-refresh dan disimpan.";
        }

        return "Token masih valid, tidak perlu di-refresh.";
    }
}