<?php

namespace App\Http\Controllers;

use App\Models\AiGeneratedImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image as ImageManager;

class GenerateImageController extends Controller
{
    public function index()
    {
        $latest = AiGeneratedImage::whereNotNull('image_url')
            ->where('is_show', 1)
            ->latest()
            ->take(12)
            ->get();

        return view('generator.index', compact('latest'));
    }
    public function result($taskId)
    {
        $image = AiGeneratedImage::where('task_id', $taskId)->firstOrFail();

        if ($image->status === 'COMPLETED') {
            $imageUrl = $image->image_url ?? null;
            return view('generator.result', ['image' => $image]);
        }

        $response = Http::withHeaders([
            'x-freepik-api-key' => env('FREEPIK_API_KEY0'),
        ])->get("https://api.freepik.com/v1/ai/text-to-image/flux-dev/{$taskId}");

        $data = $response->json()['data'] ?? [];

        // Jika sudah selesai, simpan gambar ke /public/ai_images
        if (($data['status'] ?? '') === 'COMPLETED' && !empty($data['generated'][0])) {
            $imageUrl = $data['generated'][0];

            // Pastikan folder ada
            $savePath = public_path('ai_images');
            if (!File::exists($savePath)) {
                File::makeDirectory($savePath, 0755, true);
            }

            // Unduh gambar dan simpan
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
            // Simpan path relatif ke database
            $localPath = 'ai_images/' . $fileName;
            $image->update([
                'image_url' => $localPath,
                'status' => 'COMPLETED',
            ]);
        }
        if ($data['status'] === 'COMPLETED') {
            $imageUrl = $data['generated'][0] ?? null;
            return view('generator.result', ['image' => $image]);
        }

        // Jika belum selesai, auto-refresh halaman tiap 5 detik
        return view('generator.wait', ['taskId' => $taskId]);
    }
}
