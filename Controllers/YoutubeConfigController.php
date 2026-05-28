<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YoutubeConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class YoutubeConfigController extends Controller
{
    /**
     * Show configuration page.
     */
    public function index(Request $request)
    {
        $user = $request->user(); // ini bekerja untuk sanctum
        // Ambil config pertama
        $config = YoutubeConfig::where('user_id', $user->id)->first();
        // Jika belum ada record, buat default
        if (!$config) {
            $config = YoutubeConfig::create([
                'user_id' => $user->id,
                'channel_name' => '',
                'channel_niche' => '',
                'channel_description' => '',
                'channel_category' => '',
                'channel_tag' => '',
                'channel_status' => '',
                'api_key_gemini' => '',
                'api_key_groq' => '',
                'api_key_murfai' => '',
                'api_key_minimax' => '',
                'api_key_assemblyai' => '',
                'api_key_freepik' => '',
                'api_key_deapi' => '',
                'prompt_story' => '',
                'prompt_visual' => '',
                'prompt_audio' => '',
                'gemini_voice_id' => '',
                'backsound_audio' => '',
                'prompt_video' => '',
                'is_linked' => 0,
                'day_upload' => '',
                'time_upload' => '',
                'status' => 'Inactive',
            ]);
        }

        return response()->json($config);
    }


    /**
     * Update configuration.
     */
    public function update(Request $request)
    {
        $request->validate([
            'channel_name'        => 'nullable|string|max:255',
            'channel_niche'       => 'nullable|string|max:255',
            'channel_description' => 'nullable|string',
            'channel_category'    => 'nullable|string',
            'channel_tag'         => 'nullable|string',
            'channel_status'      => 'nullable|string',
            'api_key_gemini'      => 'nullable|string',
            'api_key_groq'        => 'nullable|string',
            'api_key_murfai'      => 'nullable|string',
            'api_key_minimax'     => 'nullable|string',
            'api_key_assemblyai'  => 'nullable|string',
            'api_key_freepik'     => 'nullable|string',
            'api_key_deapi'       => 'nullable|string',
            'is_linked'           => 'nullable|boolean',
            'prompt_story'        => 'nullable|string',
            'prompt_visual'       => 'nullable|string',
            'prompt_audio'        => 'nullable|string',
            'gemini_voice_id'     => 'nullable|string',
            'backsound_audio'     => 'nullable|string',
            'prompt_video'        => 'nullable|string',
            'day_upload'          => 'nullable|string',
            'time_upload'         => 'nullable|string',
            'is_video_with_image_only' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $user = Auth::user();

        $config = YoutubeConfig::firstOrCreate(
            ['user_id' => $user->id]
        );

        $config->update($request->all());

        return response()->json(['status' => 'success', 'message' => 'YouTube configuration updated successfully!']);
    }
}
