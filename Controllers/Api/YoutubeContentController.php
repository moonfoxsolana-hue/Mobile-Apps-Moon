<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\YoutubeConfig;
use App\Models\YoutubeChannelThemes;
use App\Models\YoutubeContentGenerated;
use App\Models\YoutubeContentMedia;
use App\Models\YoutubeChannelLeaderboard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\GenerateYoutubeContentJob;

class YoutubeContentController extends Controller
{

    public function insertThemes(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $request->validate([
                'themes' => 'required|string',
                'focus' => 'string|max:255',
                'date' => 'nullable|string|max:255',

            ]);
            $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
            if (!$youtubeConfig) {
                return response()->json(['status' => 'error', 'message' => 'YouTube configuration not found.'], 404);
            }

            $themes = $request->input('themes');
            $focus = $request->input('focus');
            $date = $request->input('date');
            $createtheme = YoutubeChannelThemes::create([
                'user_id' => $user->id,
                'youtube_configuration_id' => $youtubeConfig->id,
                'channel_name' => $youtubeConfig->channel_name,
                'theme' => $themes,
                'focus' => $focus,
                'date' => $date,
                'used' => 0,
            ]);
            return response()->json(['status' => 'success', 'message' => 'Tema berhasil disimpan.', 'data' => $createtheme]);
        } catch (\Exception $e) {
            Log::error('Error inserting themes: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan tema.'], 500);
        }
    }

    public function getThemes(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
            if (!$youtubeConfig) {
                return response()->json(['status' => 'error', 'message' => 'YouTube configuration not found.'], 404);
            }

            $themes = YoutubeChannelThemes::where('youtube_configuration_id', $youtubeConfig->id)->where('user_id', $user->id)->OrderBy('id', 'desc')->get();

            return response()->json(['status' => 'success', 'message' => 'Berhasil mengambil tema.', 'data' => $themes]);
        } catch (\Exception $e) {
            Log::error('Error inserting themes: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengambil tema.'], 500);
        }
    }

    public function editTheme(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $request->validate([
                'theme' => 'required|string|max:255',
                'focus' => 'string|max:255',
                'date' => 'nullable|string|max:255',
            ]);

            $theme = YoutubeChannelThemes::where('id', $id)->where('user_id', $user->id)->first();
            if (!$theme) {
                return response()->json(['status' => 'error', 'message' => 'Tema tidak ditemukan.'], 404);
            }

            $theme->theme = $request->input('theme');
            $theme->focus = $request->input('focus');
            $theme->date = $request->input('date');
            $theme->save();

            return response()->json(['status' => 'success', 'message' => 'Tema berhasil diperbarui.', 'data' => $theme]);
        } catch (\Exception $e) {
            Log::error('Error editing theme: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat memperbarui tema.'], 500);
        }
    }

    public function deleteTheme(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $theme = YoutubeChannelThemes::where('id', $id)->where('user_id', $user->id)->first();
            if (!$theme) {
                return response()->json(['status' => 'error', 'message' => 'Tema tidak ditemukan.'], 404);
            }

            $theme->delete();

            return response()->json(['status' => 'success', 'message' => 'Tema berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error deleting theme: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus tema.'], 500);
        }
    }

    public function getGeneratedContent(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
            if (!$youtubeConfig) {
                return response()->json(['status' => 'error', 'message' => 'YouTube configuration not found.'], 404);
            }

            $contents = YoutubeContentGenerated::where('youtube_configuration_id', $youtubeConfig->id)->orderBy('id', 'desc')->get();

            return response()->json(['status' => 'success', 'message' => 'Berhasil mengambil konten.', 'data' => $contents]);
        } catch (\Exception $e) {
            Log::error('Error fetching generated content: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengambil konten.'], 500);
        }
    }
    public function generateContent(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
            if (!$youtubeConfig) {
                return response()->json(['status' => 'error', 'message' => 'YouTube configuration not found.'], 404);
            }
            Log::info('YouTube Configuration Found', [
                'user_id' => $user->id,
                'youtube_channel_id' => $youtubeConfig->id ?? 'N/A'
            ]);
            Log::Info('Lolos Validasi');
            $youtubeContentGenerated = YoutubeContentGenerated::where('youtube_configuration_id', $youtubeConfig->id)->where('date', now()->toDateString())->where('is_complete', true)->get();
            if ($youtubeContentGenerated->count() > 0) {
                Log::info('Content Generation Status', [
                    'status' => 'Daily Limit Reached',
                    'content_count' => $youtubeContentGenerated->count(),
                    'date' => now()->toDateString(),
                    'channel_id' => $youtubeConfig->id ?? 'N/A'
                ]);
                return response()->json(['status' => 'success', 'message' => 'Konten Hari ini sudah dibuat.']);
            }


            GenerateYoutubeContentJob::dispatch($youtubeConfig->id);

            return response()->json(['status' => 'success', 'message' => 'Content generation started.']);
        } catch (\Exception $e) {
            Log::error('Error starting content generation: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat memulai pembuatan konten.'], 500);
        }
    }


    public function uploadYoutube(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
            $request->validate([
                'content_id' => 'string'
            ]);

            $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
            if (!$youtubeConfig) {
                return response()->json(['status' => 'error', 'message' => 'YouTube configuration not found.'], 404);
            }
            $contentId = $request->input('content_id');
            if ($contentId) {
                // Dispatch the content generation job
                Artisan::call('youtube:upload', [
                    '--content_id' => $contentId,
                    '--config_id' => $youtubeConfig->id,
                ]);
                return response()->json(['status' => 'success', 'message' => 'Proses upload youtube dimulai.']);
            }
            Artisan::call('youtube:upload', [
                '--config_id' => $youtubeConfig->id,
            ]);
            return response()->json(['status' => 'success', 'message' => 'Proses upload youtube dimulai.']);
        } catch (\Exception $e) {
            Log::error('Error starting YouTube upload process: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat memulai proses upload YouTube.'], 500);
        }
    }



    public function editContent(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
            if (!$youtubeConfig) {
                return response()->json(['status' => 'error', 'message' => 'YouTube configuration not found.'], 404);
            }

            // 💡 FUNGSI BARU: Cari dan pastikan konten milik user yang bersangkutan
            $existContent = YoutubeContentGenerated::where('id', $id)
                ->where('youtube_configuration_id', $youtubeConfig->id)
                ->first();
            $existContentMedia = YoutubeContentMedia::where('youtube_content_generated_id', $existContent->id)->get();

            if (!$existContent) {
                return response()->json(['status' => 'error', 'message' => 'Konten tidak ditemukan atau bukan milik Anda.'], 404);
            }

            $validatedData = $request->validate([
                'title' => 'nullable|string|max:80',
                'content' => 'nullable|string|max:5000',
            ]);

            $title = $validatedData['title'];
            $content = $validatedData['content'];

            if ($content == $existContent->content) {
                $existContent->update([
                    'title' => $title ?? $existContent->title,
                ]);
            } else {
                // 💡 FUNGSI BARU: Update data di database
                $existContent->update([
                    'title' => $title ?? $existContent->title,
                    'content' => $content ?? $existContent->content,
                    'is_complete' => false,
                    'video_path' => null,
                    'audio_path' => null,
                    'subtitles_path' => null,
                    'image_json' => null,
                    'duration_json' => null,
                    'video_json' => null,
                ]);
                $existContentMedia->delete();
            }

            return response()->json(['status' => 'success', 'message' => 'Konten berhasil diupdate. Status produksi di-reset untuk re-generate.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi
            return response()->json(['status' => 'error', 'message' => 'Data tidak valid: ' . implode(', ', $e->errors()['title'] ?? [])], 422);
        } catch (\Exception $e) {
            Log::error('Error editing content: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengedit konten.'], 500);
        }
    }
    public function regenerateContent(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
            if (!$youtubeConfig) {
                return response()->json(['status' => 'error', 'message' => 'YouTube configuration not found.'], 404);
            }

            // Cek kepemilikan konten sebelum dispatch
            $existContent = YoutubeContentGenerated::where('id', $id)
                ->where('youtube_configuration_id', $youtubeConfig->id)
                ->first();

            if (!$existContent) {
                return response()->json(['status' => 'error', 'message' => 'Konten tidak ditemukan atau bukan milik Anda.'], 404);
            }

            if ($existContent->video_path || $existContent->is_complete == 1) {
                $existContent->update([
                    'is_complete'      => 0,
                    'video_path'       => null,
                    'video_json'       => null,
                ]);
            }
            if (!$existContent->subtitles_path) {
                $existContent->update([
                    'is_complete'      => 0,
                    'video_path'       => null,
                    'image_json'       => null,
                    'duration_json'    => null,
                    'video_json'       => null,
                ]);
            }

            Log::info('Re-Generate Content Started', [
                'content_id' => $id,
                'config_id' => $youtubeConfig->id,
            ]);

            // 💡 FUNGSI BARU: Dispatch job dengan Content ID sebagai argumen kedua
            // GenerateYoutubeContentJob::dispatch($configId, $contentId);
            GenerateYoutubeContentJob::dispatch($youtubeConfig->id, $id);

            return response()->json(['status' => 'success', 'message' => 'Proses Re-Generation telah dimulai. Silakan cek status konten Anda.']);
        } catch (\Exception $e) {
            Log::error('Error starting content regeneration: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat memulai pembuatan konten.'], 500);
        }
    }

    public function getMediaFiles(Request $request, $contentId)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
            if (!$youtubeConfig) {
                return response()->json(['status' => 'error', 'message' => 'YouTube configuration not found.'], 404);
            }

            $content = YoutubeContentGenerated::where('id', $contentId)
                ->where('youtube_configuration_id', $youtubeConfig->id)
                ->first();

            if (!$content) {
                return response()->json(['status' => 'error', 'message' => 'Content not found.'], 404);
            }

            $mediaFiles = YoutubeContentMedia::where('youtube_content_generated_id', $content->id)->get();

            return response()->json(['status' => 'success', 'message' => 'Berhasil mengambil media files.', 'data' => $mediaFiles]);
        } catch (\Exception $e) {
            Log::error('Error fetching media files: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengambil media files.'], 500);
        }
    }

    public function getLeaderboardData(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $youtubeConfig = YoutubeConfig::where('user_id', $user->id)->first();
            if (!$youtubeConfig) {
                return response()->json(['status' => 'error', 'message' => 'YouTube configuration not found.'], 404);
            }

            $leaderboardData = YoutubeChannelLeaderboard::where('youtube_configuration_id', $youtubeConfig->id)->get();

            return response()->json(['status' => 'success', 'message' => 'Berhasil mengambil data leaderboard.', 'data' => $leaderboardData]);
        } catch (\Exception $e) {
            Log::error('Error fetching leaderboard data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengambil data leaderboard.'], 500);
        }
    }
}
