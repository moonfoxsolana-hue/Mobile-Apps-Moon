<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\YouTube;
use Illuminate\Support\Facades\Log;

class YoutubeBiotaController extends Controller
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
            file_put_contents(storage_path('app/biota_google-token.json'), json_encode($token));

            return "Login Berhasil! Token disimpan.";
        }
        return "Gagal login.";
    }

    // 3. Fungsi Upload Video
    public function uploadVideo($title, $description, $videoId,)
    {
        // Load token yang sudah disimpan
        $tokenPath = storage_path('app/biota_google-token.json');
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
        $videoPath = storage_path('app/public/story_video_biota/' . $videoId . '.mp4'); // Pastikan file ada

        // Setup Metadata
        $snippet = new YouTube\VideoSnippet();
        $snippet->setTitle($title . " #shorts #shorts2026");
        $snippet->setDescription($description);
        $snippet->setTags(['biotanusantara', 'faunaindonesia', 'floraindonesia', 'keanekaragamanhayati', 'alamindonesia', 'biota nusantara', 'biota', 'nusantara', 'flora fauna', 'binatangunik', 'tumbuhanlangka', 'shorts', 'shortsvideos', 'viral', 'trending']);
        $snippet->setCategoryId("24"); // Kategori "hiburan"

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
            $tokenPath = storage_path('app/biota_google-token.json');
            if (!file_exists($tokenPath)) {
                $tokenPathBackup = storage_path('app/biota_google-token.json');
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

    public function postCommentToVideo($videoId, $commentText)
    {
        try {
            // 1. Setup Token (Mengikuti pola fungsi Anda)
            $tokenPath = storage_path('app/biota_google-token.json');
            if (!file_exists($tokenPath)) {
                $tokenPathBackup = storage_path('app/biota_google-token.json');
                if (!file_exists($tokenPathBackup)) {
                    return "Belum login, silakan akses route login dulu.";
                }
                $tokenPath = $tokenPathBackup;
            }

            $this->refreshToken();
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);

            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            }

            // 2. Inisialisasi Service YouTube
            $youtube = new \Google\Service\YouTube($this->client);

            // 3. Bangun struktur body komentar
            // Kita membuat objek Comment (isi pesan)
            $comment = new \Google\Service\YouTube\Comment();
            $commentSnippet = new \Google\Service\YouTube\CommentSnippet();
            $commentSnippet->setTextOriginal($commentText);
            $comment->setSnippet($commentSnippet);

            // Kita membuat objek CommentThread (induk komentar)
            $commentThread = new \Google\Service\YouTube\CommentThread();
            $commentThreadSnippet = new \Google\Service\YouTube\CommentThreadSnippet();
            $commentThreadSnippet->setVideoId($videoId);
            $commentThreadSnippet->setTopLevelComment($comment);
            $commentThread->setSnippet($commentThreadSnippet);

            // 4. Eksekusi request insert
            $response = $youtube->commentThreads->insert('snippet', $commentThread);

            return "Berhasil! Komentar telah diposting dengan ID: " . $response->id;
        } catch (\Google\Service\Exception $e) {
            return "Google API Error: " . $e->getMessage();
        } catch (\Exception $e) {
            return "General Error: " . $e->getMessage();
        }
    }


    public function getLatestVideosByQuery($query)
    {
        $this->refreshToken();
        // Pastikan path token sudah benar (sebelumnya Anda sebut biota_google-token.json)
        $tokenPath = storage_path('app/biota_google-token.json');

        if (!file_exists($tokenPath)) {
            Log::error("Token file tidak ditemukan di: " . $tokenPath);
            return [];
        }

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $this->client->setAccessToken($accessToken);

        $youtube = new \Google\Service\YouTube($this->client);

        // 1. Tentukan waktu mulai hari ini (RFC 3339)
        $yesterday = date('Y-m-d\T00:00:00\Z', strtotime('-1 day'));

        try {
            // 2. Cari video terbaru berdasarkan query
            $searchResponse = $youtube->search->listSearch('snippet', [
                'q' => $query,
                'type' => 'video',
                'order' => 'date',
                'publishedAfter' => $yesterday,
                'maxResults' => 10,
                'relevanceLanguage' => 'id',
                'regionCode' => 'ID'
            ]);

            Log::info("Jumlah video ditemukan: " . count($searchResponse->getItems()));
            $filteredVideoIds = [];

            foreach ($searchResponse->getItems() as $item) {
                // --- PERBAIKAN 1: Definisikan variabel dasar dulu ---
                $videoId = $item->id->videoId;
                $videoTitle = $item->snippet->title;
                $channelId = $item->snippet->channelId;
                $channelName = $item->snippet->channelTitle;
                $broadcastStatus = $item->snippet->liveBroadcastContent;

                // Skip jika statusnya upcoming atau sedang live
                if ($broadcastStatus === 'upcoming' || $broadcastStatus === 'live') {
                    Log::info("AutoEngage: Skip video [{$videoTitle}] karena status {$broadcastStatus}");
                    continue;
                }

                // --- PERBAIKAN 2: Cek Made for Kids setelah $videoId tersedia ---
                $videoDetails = $youtube->videos->listVideos('status', ['id' => $videoId]);
                $videoItem = $videoDetails->getItems()[0] ?? null;

                if ($videoItem && $videoItem->getStatus()->getMadeForKids()) {
                    Log::info("AutoEngage: Skip [{$videoTitle}] karena konten Made for Kids.");
                    continue;
                }

                // 3. Cek statistik channel (jumlah subscriber)
                $channelResponse = $youtube->channels->listChannels('statistics', [
                    'id' => $channelId
                ]);

                $channelItem = $channelResponse->getItems()[0] ?? null;
                if (!$channelItem) {
                    continue;
                }

                $subscriberCount = $channelItem->getStatistics()->getSubscriberCount();

                // 4. Filter: hanya ambil jika sub >= 100
                if ($subscriberCount >= 100) {
                    // Cek database di sini jika Anda ingin filter database dilakukan di dalam fungsi ini
                    // Namun sesuai logic Anda, kita kumpulkan dulu yang lolos filter YouTube
                    $filteredVideoIds[] = [
                        'channel_name' => $channelName,
                        'channel_id' => $channelId,
                        'video_id' => $videoId,
                        'title' => $videoTitle,
                        'subscribers' => $subscriberCount
                    ];

                    Log::info("YouTube Filter: LOLOS - [{$channelName}] - [{$videoId}] - (Sub: {$subscriberCount})");

                    // Opsional: Jika ingin benar-benar hanya 1 video total per proses, aktifkan break di bawah:
                    // break; 
                }
            }

            return $filteredVideoIds;
        } catch (\Exception $e) {
            Log::error("Gagal cari video: " . $e->getMessage());
            return [];
        }
    }

    public function rateVideo($videoId, $rating)
    {
        try {
            $this->refreshToken();
            $accessToken = json_decode(file_get_contents(storage_path('app/biota_google-token.json')), true);
            $this->client->setAccessToken($accessToken);

            $youtube = new \Google\Service\YouTube($this->client);

            // Eksekusi rating (like)
            $youtube->videos->rate($videoId, $rating);

            Log::info("YouTube API: Berhasil memberikan {$rating} pada video {$videoId}");
            return true;
        } catch (\Exception $e) {
            Log::error("YouTube API Like Error: " . $e->getMessage());
            return false;
        }
    }

    public function simulateVisit($videoId)
    {
        $url = "https://www.youtube.com/watch?v={$videoId}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        $response = curl_exec($ch);
        curl_close($ch);

        Log::info("Simulasi kunjungan ke video {$videoId} selesai.");
        return;
    }

    public function refreshToken()
    {
        $tokenPath = storage_path('app/biota_google-token.json');
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
