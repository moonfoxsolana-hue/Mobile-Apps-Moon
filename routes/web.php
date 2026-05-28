<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NgepetController;
use App\Http\Controllers\AiStoryController;
use App\Http\Controllers\TiktokController;
use App\Http\Controllers\YoutubeController;
use App\Http\Controllers\YoutubeSehatBoostController;
use App\Http\Controllers\YoutubeMimpiController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\GenerateImageController;

Route::get('/', function () {
    return view('home');
});
Route::get('/echosystem', function () {
    return view('echosystem');
});
Route::get('/realm', function () {
    return view('realm');
});
Route::get('/airdrop', function () {
    return view('airdrop');
});
Route::get('/login', function () {
    return response()->json([
        'message' => 'Silahkan login melalui menu Login.'
    ], 401);
})->name('login');


Route::get('/monster', function () {
    return view('monster');
});
Route::get('/staking', function () {
    return view('staking');
});
Route::get('/games', function () {
    return view('games');
});
Route::get('/whitepaper', function () {
    return view('whitepaper');
});
Route::get('/profile', function () {
    return view('profile');
});
Route::get('/youtube-content-generator', function () {
    return view('youtube.index');
});
Route::get('/youtube-content', function () {
    return view('youtube.content');
});
Route::get('/youtube-list-themes', function () {
    return view('youtube.themes');
});
Route::get('/admin/content', function () {
    return view('admin.index');
});
Route::get('/content/audio/{content}', [YoutubeController::class, 'playAudio'])->name('content.audio');
Route::get('/content/video/{content}', [YoutubeController::class, 'playVideo'])->name('content.video');
Route::get('/tokenomics', function () {
    return view('tokenomics');
});
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});
Route::get('/terms-and-conditions', function () {
    return view('terms');
});
Route::get('/presentation', function () {
    return view('presentation');
});
Route::get('/maintenance', function () {
    return view('maintenance');
});


Route::get('/tiktok/login', [TiktokController::class, 'redirectToTikTok']);
Route::get('/tiktok/callback', [TiktokController::class, 'handleCallback']);
Route::get('/youtube/auth', [YoutubeController::class, 'redirectToGoogle']);
Route::get('/youtube/oauth/callback', [YoutubeController::class, 'handleGoogleCallback']);
Route::get('/youtube/sehatboost/auth', [YoutubeSehatBoostController::class, 'redirectToGoogle']);
Route::get('/youtube/sehatboost/oauth/callback', [YoutubeSehatBoostController::class, 'handleGoogleCallback']);
Route::get('/youtube/mimpi/auth', [YoutubeMimpiController::class, 'redirectToGoogle']);
Route::get('/youtube/mimpi/oauth/callback', [YoutubeMimpiController::class, 'handleGoogleCallback']);
Route::match(['get', 'post'], '/instagram/callback', [InstagramController::class, 'handle']);
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/list', [NewsController::class, 'list'])->name('news.list');
Route::post('/news', [NewsController::class, 'store'])->name('news.store');
Route::get('/games/ngepet', [NgepetController::class, 'index'])->name('ngepet.index');
Route::get('/games/ngepet/match/{id}', [NgepetController::class, 'match'])->name('ngepet.match');
Route::get('/games/intuition', function () {
    return view('games.intuition.index');
})->name('games.intuition');
Route::get('/games/logical', function () {
    return view('games.logical.index');
})->name('games.logical');
Route::get('/games/trivia', function () {
    return view('games.trivia.index');
})->name('games.trivia');
Route::get('/games/tarot', function () {
    return view('games.tarot.index');
})->name('games.tarot');
Route::get('/games/ulartangga', function () {
    return view('games.ulartangga.index');
})->name('games.ulartangga');
Route::get('/cerita', [AiStoryController::class, 'index'])->name('cerita.daily');
Route::get('/cerita/list', [AiStoryController::class, 'list'])->name('cerita.list');
Route::get('/cerita/audio/{story}', [AiStoryController::class, 'playAudio'])->name('cerita.audio');
Route::get('/cerita/audio/sehat_boost/{story}', [AiStoryController::class, 'playAudioSehatBoost'])->name('cerita.audio');
Route::get('/cerita/audio/mimpi/{story}', [AiStoryController::class, 'playAudioMimpi'])->name('cerita.audio');
Route::get('/cerita/audio/krefi/{story}', [AiStoryController::class, 'playAudioKrefi'])->name('cerita.audio');
Route::get('/cerita/audio/biota/{story}', [AiStoryController::class, 'playAudioBiota'])->name('cerita.audio');
Route::get('/cerita/video/{story}', [AiStoryController::class, 'playVideo'])->name('cerita.video');
Route::get('/cerita/video/mimpi/{story}', [AiStoryController::class, 'playVideoMimpi'])->name('cerita.video');
Route::get('/cerita/video/krefi/{story}', [AiStoryController::class, 'playVideoKrefi'])->name('cerita.video');
Route::get('/cerita/video/biota/{story}', [AiStoryController::class, 'playVideoBiota'])->name('cerita.video');
Route::get('/cerita/audio/mythora/{story}', [AiStoryController::class, 'playAudioMythora'])->name('cerita.audio');
Route::get('/cerita/video/mythora/{story}', [AiStoryController::class, 'playVideoMythora'])->name('cerita.video');
Route::get('/berita/audio/{news}', [AiStoryController::class, 'playAudioNews'])->name('berita.audio');
Route::get('/berita/video/{news}', [AiStoryController::class, 'playVideoNews'])->name('berita.video');


Route::get('/ai-analyzer', function () {
    return view('document');
});

Route::get('/pembuat-cerita', function () {
    return view('story.agentic');
});

Route::get('/ai', function () {
    return view('ai');
});
Route::get('/ai-image', function () {
    return view('ai-image');
});
Route::get('/tiktok/login', [TiktokController::class, 'login']);
Route::get('/tiktok/callback', [TiktokController::class, 'callback']);

Route::get('/ai-generator', [GenerateImageController::class, 'index'])->name('generator.index');
Route::get('/trial', function () {
    return view('generator.trial');
});
Route::get('/ai-generator/result/{taskId}', [GenerateImageController::class, 'result'])->name('generator.result');
