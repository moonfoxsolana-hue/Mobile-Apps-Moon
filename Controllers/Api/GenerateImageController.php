<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiGeneratedImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use App\Services\ImageGeneratorService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image as ImageManager;
use App\Jobs\ProcessVeoVideo;

class GenerateImageController extends Controller
{
    protected ImageGeneratorService $imageGenerator;

    public function __construct(ImageGeneratorService $imageGenerator)
    {
        $this->imageGenerator = $imageGenerator;
    }

    public function instant22(Request $request)
    {
        $prompt = "realistic, A dimly lit room filled with stacked old papers and black-and-white photographs, a single photo of a young factory worker named Rita in a 1972 uniform, her eyes flashing like lightning.";
        $filename = "video_" . uniqid();

        // Masukkan ke antrean (queue)
        ProcessVeoVideo::dispatch($prompt, $filename);

        return response()->json(['message' => 'Video sedang diproses di background.']);
    }

    public function instant2(Request $request)
    {
        $prompt = "tengah hutan, sinematik, lighting dramatis.";
        $generatedUUID = Str::uuid();
        $result = $this->imageGenerator->generateImagen3($prompt, $generatedUUID);

        if ($result) {
            return response()->json([
                'status' => 'success',
                'data' =>
                ['image_url' => $result]
            ]);
        }


        return response()->json(['error' => 'Gagal membuat task.'], 500);
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
            'prompt' => 'required|string|max:700',
            'image_ratio' => 'nullable|string',
            'style' => 'nullable|string',
            'effects' => 'nullable|array'
        ]);

        $generatedUUID = Str::uuid();
        // $path = $this->imageGenerator->generateImagefromGoogle(
        //     $request->prompt,
        //     $generatedUUID->toString()
        // );

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

        public function trial(Request $request)
    {

        $request->validate([
            'prompt' => 'required|string|max:700',
            'image_ratio' => 'nullable|string',
            'style' => 'nullable|string',
            'effects' => 'nullable|array'
        ]);

        $generatedUUID = Str::uuid();
        // $path = $this->imageGenerator->generateImagefromGoogle(
        //     $request->prompt,
        //     $generatedUUID->toString()
        // );

        $path = $this->imageGenerator->generateAndSaveTrial(
            $request->prompt,
            $request->image_ratio,
            $request->style,
            $request->effects,
            $generatedUUID->toString()
        );

        if (!$path) {
            return response()->json(['error' => 'Gagal generate gambar.'], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Gambar berhasil dibuat',
            'data' => ['image_url' => $path],
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
