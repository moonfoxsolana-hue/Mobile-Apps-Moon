<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\YouTube;

class YoutubeMythoraController extends Controller
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
            file_put_contents(storage_path('app/mythora_google-token.json'), json_encode($token));
            
            return "Login Berhasil! Token disimpan.";
        }
        return "Gagal login.";
    }

    // 3. Fungsi Upload Video
    public function uploadVideo($title, $description, $videoId, )
    {
        // Load token yang sudah disimpan
        $tokenPath = storage_path('app/mythora_google-token.json');
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
        $videoPath = storage_path('app/public/story_video_mythora/' . $videoId . '.mp4'); // Pastikan file ada

        // Setup Metadata
        $snippet = new YouTube\VideoSnippet();
        $snippet->setTitle($title . " #mythoraatlas");
        $snippet->setDescription($description);
        $snippet->setTags(['mythora atlas', 'mythora', 'atlas', 'fantasy', 'story', 'indonesia']);
        $snippet->setCategoryId("24"); // Kategori "Entertainment"

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

        return "Upload Sukses! ID: " . $status['id'];
    }

    public function refreshToken()
    {
        $tokenPath = storage_path('app/mythora_google-token.json');
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