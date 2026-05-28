<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiStory;
use App\Models\AiStoryThemes;
use App\Models\AiStoryMedia;
use App\Models\AiStoryMimpi;
use App\Models\AiStoryMimpiThemes;
use App\Models\AiStoryMimpiMedia;
use App\Models\AiStoryBiota;
use App\Models\AiStoryBiotaThemes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\GenerateContentJob;

class AdminContentController extends Controller
{

    private function getModelByType($type)
    {
        return match ($type) {
            'general' => AiStory::class,
            'mimpi'   => AiStoryMimpi::class,
            'biota'   => AiStoryBiota::class,
            default   => null,
        };
    }
    private function getMediaModelByType($type)
    {
        return match ($type) {
            'mimpi'   => AiStoryMimpiMedia::class,
            'general', 'biota' => AiStoryMedia::class,
            default   => null,
        };
    }
    public function index(Request $request, $type)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
            $modelClass = $this->getModelByType($type);

            if (!$modelClass) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tipe konten tidak valid (Gunakan: general, mimpi, atau biota)'
                ], 400);
            }

            // Ambil data dengan pagination agar tidak berat saat data sudah ribuan
            $contents = $modelClass::orderBy('id', 'desc')
                ->paginate($request->get('limit', 10));

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil mengambil data story $type",
                'data' => $contents
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching $type content: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server.'
            ], 500);
        }
    }

    public function show($type, $id, Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $modelClass = $this->getModelByType($type);

            if (!$modelClass) return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);

            $content = $modelClass::find($id);

            if (!$content) {
                return response()->json(['status' => 'error', 'message' => 'Konten tidak ditemukan'], 404);
            }

            // Jika ingin menyertakan detail media (asumsi ada relasi atau query manual)
            // Contoh manual jika nama tabel media berbeda:
            // $media = \DB::table("ai_story_{$type}_media")->where('story_id', $id)->get();

            return response()->json([
                'status' => 'success',
                'data' => $content
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $type, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
            $modelClass = $this->getModelByType($type);
            if (!$modelClass) return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);

            $item = $modelClass::findOrFail($id);

            // Validasi sederhana
            $validated = $request->validate([
                'title'   => 'sometimes|string|max:255',
                'theme'   => 'sometimes|string',
                'content' => 'sometimes|string',
            ]);

            $item->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => "Konten $type berhasil diperbarui",
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API untuk mengambil List Media berdasarkan Story ID
     */
    public function getMedia($type, $id, Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
            $mediaModel = $this->getMediaModelByType($type);
            if (!$mediaModel) return response()->json(['status' => 'error', 'message' => 'Media model not found'], 400);

            // Ambil semua media yang berhubungan dengan story_id tersebut
            $media = $mediaModel::where('story_id', $id)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil mengambil media untuk $type #$id",
                'data' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateMedia($type, $mediaId, Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

            $mediaModel = $this->getMediaModelByType($type);
            if (!$mediaModel) return response()->json(['status' => 'error', 'message' => 'Media model not found'], 400);

            $media = $mediaModel::findOrFail($mediaId);

            $validated = $request->validate([
                'duration_start' => 'required|string',
            ]);

            $media->update([
                'duration_start' => $validated['duration_start']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Durasi media #$mediaId berhasil diperbarui",
                'data' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API untuk Hapus Media (Record & File Fisik)
     */
    public function deleteMedia($type, $mediaId, Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

            $mediaModel = $this->getMediaModelByType($type);
            if (!$mediaModel) return response()->json(['status' => 'error', 'message' => 'Media model not found'], 400);

            $media = $mediaModel::findOrFail($mediaId);

            // 1. Hapus file fisik dari folder public jika path-nya ada
            if ($media->image_path) {
                $filePath = public_path($media->image_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // 2. Update kolom image_path menjadi null (TIDAK menghapus record)
            $media->update([
                'image_path' => null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Gambar pada media #$mediaId berhasil dikosongkan",
                'data' => $media // Mengembalikan data terbaru untuk update UI
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getThemes(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $themes = AiStoryThemes::where('user_id', $user->id)->OrderBy('id', 'desc')->get();

            return response()->json(['status' => 'success', 'message' => 'Berhasil mengambil tema.', 'data' => $themes]);
        } catch (\Exception $e) {
            Log::error('Error Get themes: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengambil tema.'], 500);
        }
    }
}
