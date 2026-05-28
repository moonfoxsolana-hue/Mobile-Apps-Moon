<?php

namespace App\Http\Controllers;

use App\Models\AiStory;
use App\Services\AiStoryGenerator;
use App\Services\AiTextToSpeechService;
use App\Services\ElevenLabsTTSService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AiStoryController extends Controller
{
    public function index()
    {
        $stories = AiStory::where('date', '<=', now()->toDateString())->orderBy('date', 'desc')->paginate(10);

        return view('story.daily', [
            'stories' => $stories
        ]);
    }

    public function list()
    {
        $stories = AiStory::where('date', '<=', now()->toDateString())->orderBy('date', 'desc')->paginate(10);
        return response()->json($stories);
    }

    public function indexgenerate(AiStoryGenerator $storyGenerator)
    {
        $today = now()->toDateString();
        $story = AiStory::where('date', $today)->first();

        if (!$story) {
            try {
                // Generate cerita baru
                $generated = $storyGenerator->generateStory();

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

        return view('story.daily', ['story' => $story]);
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
        //mengambil file audio dari storage .mp3 jika tidak ada, coba ambil .wav
        $path = "public/story_audio/{$story->id}.mp3";
        if (!Storage::exists($path)) {
            $path = "public/story_audio/{$story->id}.wav";
            if (!Storage::exists($path)) {
                abort(404, 'Audio belum tersedia.');
            }
        }

        return response()->file(storage_path('app/' . $path));
    }
    public function playAudioSehatBoost(AiStory $story)
    {
        //mengambil file audio dari storage .mp3 jika tidak ada, coba ambil .wav
        $path = "public/story_audio_sehat_boost/{$story->id}.mp3";
        if (!Storage::exists($path)) {
            $path = "public/story_audio_sehat_boost/{$story->id}.wav";
            if (!Storage::exists($path)) {
                abort(404, 'Audio belum tersedia.');
            }
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function playAudioMimpi(AiStory $story)
    {
        //mengambil file audio dari storage .mp3 jika tidak ada, coba ambil .wav
        $path = "public/story_audio_mimpi/{$story->id}.mp3";
        if (!Storage::exists($path)) {
            $path = "public/story_audio_mimpi/{$story->id}.wav";
            if (!Storage::exists($path)) {
                abort(404, 'Audio belum tersedia.');
            }
        }

        return response()->file(storage_path('app/' . $path));
    }
    public function playAudioKrefi(AiStory $story)
    {
        //mengambil file audio dari storage .mp3 jika tidak ada, coba ambil .wav
        $path = "public/story_audio_krefi/{$story->id}.mp3";
        if (!Storage::exists($path)) {
            $path = "public/story_audio_krefi/{$story->id}.wav";
            if (!Storage::exists($path)) {
                abort(404, 'Audio belum tersedia.');
            }
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function playAudioBiota(AiStory $story)
    {
        //mengambil file audio dari storage .mp3 jika tidak ada, coba ambil .wav
        $path = "public/story_audio_biota/{$story->id}.mp3";
        if (!Storage::exists($path)) {
            $path = "public/story_audio_biota/{$story->id}.wav";
            if (!Storage::exists($path)) {
                abort(404, 'Audio belum tersedia.');
            }
        }

        return response()->file(storage_path('app/' . $path));
    }
    public function playAudioMythora(AiStory $story)
    {
        //mengambil file audio dari storage .mp3 jika tidak ada, coba ambil .wav
        $path = "public/story_audio_mythora/{$story->id}.mp3";
        if (!Storage::exists($path)) {
            $path = "public/story_audio_mythora/{$story->id}.wav";
            if (!Storage::exists($path)) {
                abort(404, 'Audio belum tersedia.');
            }
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function playAudioNews($id)
    {
        //mengambil file audio dari storage .mp3 jika tidak ada, coba ambil .wav
        $path = "public/story_audio_news/{$id}.mp3";
        if (!Storage::exists($path)) {
            $path = "public/story_audio_news/{$id}.wav";
            if (!Storage::exists($path)) {
                abort(404, 'Audio belum tersedia.');
            }
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function playVideo(AiStory $story)
    {
        $path = "public/story_video/{$story->id}.mp4";
        if (!Storage::exists($path)) {
            abort(404, 'Video belum tersedia.');
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function playVideoMimpi(AiStory $story)
    {
        $path = "public/story_video_mimpi/{$story->id}.mp4";
        if (!Storage::exists($path)) {
            abort(404, 'Video belum tersedia.');
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function playVideoKrefi(AiStory $story)
    {
        $path = "public/story_video_krefi/{$story->id}.mp4";
        if (!Storage::exists($path)) {
            abort(404, 'Video belum tersedia.');
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function playVideoBiota(AiStory $story)
    {
        $path = "public/story_video_biota/{$story->id}.mp4";
        if (!Storage::exists($path)) {
            abort(404, 'Video belum tersedia.');
        }

        return response()->file(storage_path('app/' . $path));
    }
    public function playVideoMythora(AiStory $story)
    {
        $path = "public/story_video_mythora/{$story->id}.mp4";
        if (!Storage::exists($path)) {
            abort(404, 'Video belum tersedia.');
        }

        return response()->file(storage_path('app/' . $path));
    }

    public function playVideoNews($id)
    {
        $path = "public/story_video_news/{$id}.mp4";
        if (!Storage::exists($path)) {
            abort(404, 'Video belum tersedia.');
        }

        return response()->file(storage_path('app/' . $path));
    }
}
