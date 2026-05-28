<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YoutubeConfig;
use App\Models\YoutubeContentGenerated;
use Google\Client;
use Google\Service\YouTube;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class YoutubeController extends Controller
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
            'https://www.googleapis.com/auth/youtube.readonly',
            'https://www.googleapis.com/auth/youtube.force-ssl'
        ]);
        $this->client->setAccessType('offline'); // Penting agar dapat Refresh Token
        $this->client->setPrompt('select_account consent');
    }

    // 1. Buka link ini di browser untuk login
    public function redirectToGoogle(request $request)
    {
        $token = $request->query('token');
        if (!$token) {
            return response("Token missing", 401);
        }

        // Authenticate user via token
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response("Invalid token", 401);
        }

        $user = $accessToken->tokenable;

        $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
        if ($youtubeConfig && $youtubeConfig->is_linked) {
            return "Akun YouTube sudah terhubung.";
        }
        Log::info('User ID for YouTube Auth: ' . $user->id);
        $state = $token; // token Sanctum user

        $this->client->setState($state);

        $authUrl = $this->client->createAuthUrl();
        return redirect($authUrl);
    }

    // 2. Google akan melempar balik ke sini
    public function handleGoogleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return "Gagal login.";
        }

        // Ambil kembali token user dari state
        $stateToken = $request->query('state');

        if (!$stateToken) {
            return "State token hilang.";
        }

        // Cari user berdasarkan Sanctum token
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($stateToken);

        if (!$accessToken) {
            return "Token tidak valid atau user sudah logout.";
        }

        $user = $accessToken->tokenable;

        // Ambil token google
        $token = $this->client->fetchAccessTokenWithAuthCode($request->code);

        // Simpan
        $youtubeConfig = YoutubeConfig::updateOrCreate(
            ['user_id' => $user->id],
            [
                'is_linked' => true,
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'token_json' => json_encode($token),
            ]
        );

        return redirect('/youtube-content-generator')->with('success', 'Channel YouTube berhasil terhubung!');
    }


    // 3. Fungsi Upload Video
    public function uploadVideo($title, $description, $videoId)
    {
        // Load token yang sudah disimpan
        $tokenPath = storage_path('app/mystic_nusa_google-token.json');
        if (!file_exists($tokenPath)) {
            $tokenPathBackup = storage_path('app/mystic_nusa_backup_google-token.json');
            if (!file_exists($tokenPathBackup)) {
                return "Belum login, silakan akses route login dulu.";
            }
            $tokenPath = $tokenPathBackup;
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
        $videoPath = storage_path('app/public/story_video/' . $videoId . '.mp4'); // Pastikan file ada

        // Setup Metadata
        $snippet = new YouTube\VideoSnippet();
        $snippet->setTitle($title . " #mysticnusa");
        $snippet->setDescription($description);
        $snippet->setTags(['mystic nusa', 'mysticnusa', 'cerita mistis', 'cerita seram', 'cerita horor', 'konten horor', 'token mistis', 'indonesia']);
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
            $tokenPath = storage_path('app/mystic_nusa_google-token.json');
            if (!file_exists($tokenPath)) {
                $tokenPathBackup = storage_path('app/mystic_nusa_backup_google-token.json');
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
        $tokenPath = storage_path('app/mystic_nusa_google-token.json');
        if (!file_exists($tokenPath)) {
            $tokenPathBackup = storage_path('app/mystic_nusa_backup_google-token.json');
            if (!file_exists($tokenPathBackup)) {
                return "Belum login, silakan akses route login dulu.";
            }
            $tokenPath = $tokenPathBackup;
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

    public function uploadVideoGeneral($youtubeid, $title, $videoId, $tokenJson)
    {
        $youtubeConfig = YoutubeConfig::where('id', $youtubeid)->first();
        $youtubeVideoDescription = $youtubeConfig->channel_description;
        $youtubeVideoCategory = $youtubeConfig->channel_category;
        $youtubeVideoTags = $youtubeConfig->channel_tag;
        $youtubeStatusVideo = $youtubeConfig->channel_status;

        $tokenArray = json_decode($tokenJson, true);
        $this->refreshTokenGeneral($tokenArray, $youtubeid);
        $tokenArray['client_id'] = env('YOUTUBE_CLIENT_ID');
        $tokenArray['client_secret'] = env('YOUTUBE_CLIENT_SECRET');
        $this->client->setAccessToken($tokenArray);

        $youtube = new YouTube($this->client);
        $videoPath = storage_path('app/public/youtube_content_video/' . $videoId . '.mp4'); // Pastikan file ada
        Log::Info('Proses cek video');

        // Setup Metadata
        $snippet = new YouTube\VideoSnippet();
        $snippet->setTitle($title);
        $snippet->setDescription($youtubeVideoDescription);
        $snippet->setTags([$youtubeVideoTags]);
        $snippet->setCategoryId($youtubeVideoCategory);

        $status = new YouTube\VideoStatus();
        $status->privacyStatus = $youtubeStatusVideo;

        $video = new YouTube\Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);
        Log::Info('Proses Upload Ke Youtube');
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
        Log::Info('Proses Upload Ke Youtube Selesai');

        return $status['id'];
    }

    public function refreshTokenGeneral($tokenJson, $youtubeconfigId)
    {

        $this->client->setAccessToken($tokenJson);
        Log::Info('Proses Refresh Token');
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $token = json_encode($this->client->getAccessToken());

            $youtubeConfig = YoutubeConfig::find($youtubeconfigId);
            if (!$youtubeConfig) {
                return "Konfigurasi YouTube tidak ditemukan untuk ID: " . $youtubeconfigId;
            }
            $youtubeConfig->access_token = $this->client->getAccessToken()['access_token'];
            $youtubeConfig->token_json = $token;
            $youtubeConfig->save();
            Log::Info('Proses Refresh Token Berhasil');
            return "Token berhasil di-refresh dan disimpan.";
        }
        Log::Info('Proses Refresh Token masih Valid');
        return "Token masih valid, tidak perlu di-refresh.";
    }


    public function playAudio(YoutubeContentGenerated $content)
    {
        //mengambil file audio dari storage .mp3 jika tidak ada, coba ambil .wav
        $path = "public/youtube_content_audio/{$content->id}.mp3";
        if (!Storage::exists($path)) {
            abort(404, 'Audio belum tersedia.');
        }
        return response()->file(storage_path('app/' . $path));
    }
    public function playVideo(YoutubeContentGenerated $content)
    {
        $path = "public/youtube_content_video/{$content->id}.mp4";
        if (!Storage::exists($path)) {
            abort(404, 'Video belum tersedia.');
        }

        return response()->file(storage_path('app/' . $path));
    }
}
