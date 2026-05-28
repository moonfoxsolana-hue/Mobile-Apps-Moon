<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageGeneratorService;
use App\Services\AiStoryGenerator;
use App\Services\AiTextToSpeechService;
use App\Services\ElevenLabsTTSService;
use App\Models\AiStory;
use App\Models\AiGeneratedImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image as ImageManager;


class AgenticAIController extends Controller
{
    protected ImageGeneratorService $imageGenerator;
    protected AiStoryGenerator $aistoryGenerator;

    public function __construct(ImageGeneratorService $imageGenerator, AiStoryGenerator $aistoryGenerator)
    {
        $this->imageGenerator = $imageGenerator;
        $this->aistoryGenerator = $aistoryGenerator;
    }
    public function agentic(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'query' => 'required|string|max:200',
            'request_mode' => 'nullable|string|in:chat,story,story-audio,story-image,story-audio-image',
        ]);
        $queryprompt = $request->input('query');

        // Batasi penggunaan token untuk fitur ini
        $cost = 500;
        if ($user->total_token < $cost) {
            return response()->json(['error' => 'Energi mistismu tidak mencukupi (butuh 500 MYNU)'], 403);
        }

        $user->decrement('total_token', $cost);
        $user->logTokenHistory(
            type: 'system',
            action: 'subtract',
            amount: $cost,
            description: 'Menggunakan Token untuk fitur Agentic AI'
        );

        // Proses query dengan Agentic AI (misalnya, panggil layanan eksternal atau internal)
        if ($request->mode === 'story') {
            // Jika mode cerita, gunakan AiStoryGenerator
            $generated = $this->aistoryGenerator->generateStoryfromPrompt($queryprompt);

           // Kembalikan respons cerita
            return response()->json([
                'status' => 'success',
                'data' => $generated,
            ]);
        }
        // Contoh sederhana:
        $responseMessage = "Ini adalah respons dari Agentic AI untuk pertanyaan Anda: " . $queryprompt;

        return response()->json([
            'status' => 'success',
            'message' => $responseMessage,
        ]);
    }

    public function story(Request $request)
    {
        $today = now()->toDateString();
        $story = AiStory::where('date', $today)->first();

        if (!$story) {
            try {
                // Generate cerita baru
                $generated = $this->aistoryGenerator->generateStory();

                $story = AiStory::create([
                    'date' => $today,
                    'title' => $generated['title'],
                    'theme' => $generated['theme'],
                    'content' => $generated['content'],
                ]);

                // Coba generate audio dengan ElevenLabs terlebih dahulu
                try {
                    $contentForAudio = mb_substr(strip_tags($story->content), 0, 900);
                    $contentForAudio .= ". Ikuti terus cerita lainnya, hanya di Mystic Nusa.";
                    $ttsElevenLabs = new ElevenLabsTTSService();
                    $audioUrl = $this->generateAudioElevenLabs($contentForAudio, $story->id, $ttsElevenLabs);
                    $story->update(['audio_path' => $audioUrl]);
                } catch (\Exception $e) {
                    // Jika ElevenLabs gagal (credit habis, error API, dsb), fallback ke TTS lokal
                    Log::warning('ElevenLabs gagal, fallback ke TTS lokal: ' . $e->getMessage());

                    try {
                        $ttsLocal = new AiTextToSpeechService();
                        $fallbackAudioUrl = $this->generateAudio($contentForAudio, $story->id, $ttsLocal);
                        $story->update(['audio_path' => $fallbackAudioUrl]);
                    } catch (\Exception $e2) {
                        Log::error('Gagal membuat audio fallback lokal: ' . $e2->getMessage());
                    }
                }
            } catch (\Exception $e) {
                Log::error('Gagal membuat cerita harian: ' . $e->getMessage());
                abort(500, 'Gagal membuat cerita harian.');
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $story,
        ]);
    }

    public function generateAudio(string $content, int $storyId, AiTextToSpeechService $tts)
    {
        $path = "public/story_audio/{$storyId}.mp3";

        if (Storage::exists($path)) {
            return Storage::url($path);
        }

        $audio = $tts->generate($content);
        Storage::put($path, $audio);

        return Storage::url($path);
    }
    public function generateAudioElevenLabs(string $content, int $storyId, ElevenLabsTTSService $tts)
    {
        try {
            $audioUrl = $tts->generate($content, "{$storyId}.mp3");
            return $audioUrl; // ← langsung kembalikan URL (string)
        } catch (\Exception $e) {
            Log::error('Gagal generate audio ElevenLabs: ' . $e->getMessage());
            throw $e; // biar bisa ditangani di level atas
        }
    }
    public function playAudio(AiStory $story)
    {
        $path = "public/story_audio/{$story->id}.mp3";

        if (!Storage::exists($path)) {
            abort(404, 'Audio belum tersedia.');
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function instant(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $cost = 1000;
        if ($user->total_token < $cost) {
            return response()->json(['error' => 'Energi mistismu tidak mencukupi (butuh 1000 MYNU)'], 403);
        }

        $request->validate([
            'prompt' => 'required|string|max:500',
            'image_ratio' => 'required|string',
            'style' => 'required|string',
            'effects' => 'required|array'
        ]);

        $generatedUUID = Str::uuid();

        $path = $this->imageGenerator->generateAndSave(
            $request->prompt,
            $request->image_ratio,
            $request->style,
            $request->effects,
            $generatedUUID->toString()
        );

        if (!$path) {
            return response()->json(['error' => 'Gagal generate gambar.'], 500);
        }
        $image = AiGeneratedImage::create([
            'user_id' => $user->id,
            'task_id' => $generatedUUID,
            'prompt' => $request->prompt,
            'image_url' => $path,
            'status' => 'COMPLETED',
            'token_used' => $cost,
        ]);

        // Potong token
        $user->decrement('total_token', $cost);
        $user->logTokenHistory(
            type: 'system',
            action: 'subtract',
            amount: $cost,
            description: 'Menggunakan Token untuk generate gambar AI secara instan'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Gambar berhasil dibuat',
            'data' => $image,
        ]);
    }

    public function generate(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $cost = 10000;
        if ($user->total_token < $cost) {
            return response()->json(['error' => 'Energi mistismu tidak mencukupi (butuh 10000 MYNU)'], 403);
        }

        $request->validate([
            'prompt' => 'required|string|max:500',
            'aspect_ratio' => 'required|string',
            'effects' => 'required|array',
        ]);

        $user->decrement('total_token', $cost);
        $user->logTokenHistory(
            type: 'system',
            action: 'subtract',
            amount: $cost,
            description: 'Menggunakan Token untuk membuat task deep generate gambar AI'
        );

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-freepik-api-key' => env('FREEPIK_API_KEY0'),
        ])->post('https://api.freepik.com/v1/ai/text-to-image/flux-dev', [
            'prompt' => $request->prompt,
            'aspect_ratio' => $request->aspect_ratio,
            'styling' => [
                'effects' => [
                    'color' => $request->effects['color'] ?? null,
                    'framing' => $request->effects['framing'] ?? null,
                    'lightning' => $request->effects['lightning'] ?? null,
                ],
            ],
            'seed' => rand(100000, 999999),
        ]);

        $data = $response->json();
        $taskId = $data['data']['task_id'] ?? null;

        if (!$taskId) {
            return response()->json(['error' => 'Gagal membuat task.'], 500);
        }

        AiGeneratedImage::create([
            'user_id' => $user->id,
            'task_id' => $taskId,
            'prompt' => $request->prompt,
            'status' => 'CREATED',
            'token_used' => $cost,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Task berhasil dibuat',
            'task_id' => $taskId,
        ]);
    }

    public function result($taskId)
    {
        $image = AiGeneratedImage::where('task_id', $taskId)->first();
        if (!$image) return response()->json(['error' => 'Task tidak ditemukan'], 404);

        $response = Http::withHeaders([
            'x-freepik-api-key' => env('FREEPIK_API_KEY0'),
        ])->get("https://api.freepik.com/v1/ai/text-to-image/flux-dev/{$taskId}");

        $data = $response->json()['data'] ?? [];

        if (($data['status'] ?? '') === 'COMPLETED' && !empty($data['generated'][0])) {
            $imageUrl = $data['generated'][0];
            $savePath = public_path('ai_images');

            if (!File::exists($savePath)) {
                File::makeDirectory($savePath, 0755, true);
            }

            $imageContent = Http::get($imageUrl)->body();
            $fileName = $taskId . '.jpg';
            $filePath = $savePath . '/' . $fileName;
            File::put($filePath, $imageContent);

            // --- Tambahkan watermark ---
            try {
                $img = ImageManager::make($filePath);

                $fontSize = max(40, intval($img->width() * 0.05)); // 5% lebar
                $img->text('Mystic Nusa', $img->width() - 38, $img->height() - 38, function ($font) use ($fontSize) {
                    $font->file(public_path('fonts/Calistoga.ttf')); // gunakan font custom
                    $font->size($fontSize);
                    $font->color([120, 0, 255, 0.3]); // ungu lembut transparan
                    $font->align('right');
                    $font->valign('bottom');
                });

                // Lalu teks utama
                $img->text('Mystic Nusa', $img->width() - 40, $img->height() - 40, function ($font) use ($fontSize) {
                    $font->file(public_path('fonts/Calistoga.ttf')); // gunakan font custom
                    $font->size($fontSize);
                    $font->color([255, 255, 255, 0.5]);
                    $font->align('right');
                    $font->valign('bottom');
                });

                $img->save($filePath, 90, 'jpg');
            } catch (\Exception $e) {
                Log::error('Gagal menambahkan watermark: ' . $e->getMessage(), ['path' => $filePath]);
            }

            $localPath = 'ai_images/' . $fileName;
            $image->update([
                'image_url' => $localPath,
                'status' => 'COMPLETED',
            ]);

            return response()->json([
                'status' => 'success',
                'image' => $image,
                'local_path' => asset($localPath),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'status' => $data['status'] ?? 'PENDING',
                'progress' => $data['progress'] ?? 0,
            ],
        ]);
    }
}
