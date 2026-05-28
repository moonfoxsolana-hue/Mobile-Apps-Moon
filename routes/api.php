<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AirdropController;
use App\Http\Controllers\Api\TokenUnlockController;
use App\Http\Controllers\StakingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\YoutubeConfigController;
use App\Http\Controllers\Api\NgepetMatchController;
use App\Http\Controllers\Api\NgepetAvatarController;
use App\Http\Controllers\Api\IntuitionGameController;
use App\Http\Controllers\Api\LogicalGameController;
use App\Http\Controllers\Api\TriviaGameController;
use App\Http\Controllers\Api\TarotRitualController;
use App\Http\Controllers\Api\GenerateImageController;
use App\Http\Controllers\Api\UlartanggaGameController;
use App\Http\Controllers\Api\AgenticAIController;
use App\Http\Controllers\Api\PresentationController;
use App\Http\Controllers\Api\AgenticAIAnalyzerController;
use App\Http\Controllers\Api\YoutubeContentController;
use App\Http\Controllers\Api\AdminContentController;

Route::post('/track-homepage-visit', [VisitController::class, 'track']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/token-unlocks', [TokenUnlockController::class, 'index']);
Route::post('/outline', [PresentationController::class, 'generateOutline']);
Route::get('/ppt/{id}', [PresentationController::class, 'generatePpt']);
Route::post('/analyze', [AgenticAIAnalyzerController::class, 'analyzeDocument']);


// Route yang membutuhkan autentikasi Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // 🔁 Update wallet (jika belum tersimpan)
    //Route::post('/wallet', [AuthController::class, 'updateWallet']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/airdrop/claim', [AirdropController::class, 'claim']);
    Route::post('/airdrop/claim-with-code', [AirdropController::class, 'claimWithCode']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/staking/types', [StakingController::class, 'getTypes']);
    Route::post('/staking', [StakingController::class, 'stake']);
    Route::get('/user/stakings', [StakingController::class, 'index']);
    Route::post('/staking/claim/{id}', [StakingController::class, 'claim']);
    Route::delete('/staking/cancel/{id}', [StakingController::class, 'cancel']);
    Route::get('/token-history', [ProfileController::class, 'tokenHistory']);
    Route::post('/story/agent', [AgenticAIController::class, 'agentic']);

    Route::prefix('v1/admin/content')->group(function () {
        // Endpoint: /api/v1/admin/content/general
        // Endpoint: /api/v1/admin/content/mimpi
        // Endpoint: /api/v1/admin/content/biota
        Route::get('/{type}', [AdminContentController::class, 'index']);
        Route::get('/{type}/{id}', [AdminContentController::class, 'show']);
        Route::put('/{type}/{id}', [AdminContentController::class, 'update']);
        Route::get('/{type}/{id}/media', [AdminContentController::class, 'getMedia']);
        Route::put('/{type}/media/{mediaId}', [AdminContentController::class, 'updateMedia']);
        Route::put('{type}/media/{mediaId}', [AdminContentController::class, 'updateMedia']);
        Route::delete('{type}/media/{mediaId}', [AdminContentController::class, 'deleteMedia']);
    });

    Route::prefix('youtube')->group(function () {
        Route::get('/index', [YouTubeConfigController::class, 'index']);
        Route::post('/update', [YouTubeConfigController::class, 'update']);
        Route::get('/list-themes', [YoutubeContentController::class, 'getThemes']);
        Route::post('/insert-themes', [YoutubeContentController::class, 'insertThemes']);
        Route::post('/edit-themes/{id}', [YoutubeContentController::class, 'editTheme']);
        Route::post('/delete-themes/{id}', [YoutubeContentController::class, 'deleteTheme']);
        Route::get('/list-content', [YoutubeContentController::class, 'getGeneratedContent']);
        Route::post('/generate-content', [YoutubeContentController::class, 'generateContent']);
        Route::post('/upload-content', [YoutubeContentController::class, 'uploadYoutube']);
        Route::post('/edit-content/{id}', [YoutubeContentController::class, 'editContent']);
        Route::post('/regenerate-content/{id}', [YoutubeContentController::class, 'regenerateContent']);
        Route::get('/detail-media/{id}', [YoutubeContentController::class, 'getMediaFiles']);
        Route::get('/channel-leaderboard', [YoutubeContentController::class, 'getLeaderboardData']);
    });

    Route::prefix('ngepet')->group(function () {
        Route::post('/match/create', [NgepetMatchController::class, 'create']);
        Route::post('/match/{id}/join', [NgepetMatchController::class, 'join']);
        Route::post('/match/{id}/guess', [NgepetMatchController::class, 'guess']);
        Route::post('/match/{id}/submit-choice', [NgepetMatchController::class, 'submitChoice']);
        Route::post('/match/{id}/close', [NgepetMatchController::class, 'close']);
        Route::post('/match/claim-victory', [NgepetMatchController::class, 'claimIntruderVictory']);
        Route::post('/match/{id}/hidden-item', [NgepetMatchController::class, 'storeHiddenItem']);
        Route::post('/match/{id}/hidden-guess', [NgepetMatchController::class, 'makeGuess']);
        Route::get('/match/active', [NgepetMatchController::class, 'myActiveMatch']);
        Route::get('/match/history', [NgepetMatchController::class, 'historyMatches']);
        Route::get('/match/{id}', [NgepetMatchController::class, 'show']);
        Route::get('/match', [NgepetMatchController::class, 'listOpenMatches']);
        Route::get('/leaderboard/house', [NgepetMatchController::class, 'leaderboardTopHouses']);
        Route::get('/leaderboard/host', [NgepetMatchController::class, 'leaderboardHostWinrate']);
        Route::get('/leaderboard/intruders', [NgepetMatchController::class, 'leaderboardIntruderWinrate']);
        Route::get('/avatar', [NgepetAvatarController::class, 'shoplist']);
        Route::post('/avatar/{id}/buy', [NgepetAvatarController::class, 'buy']);
        Route::get('/avatar/own', [NgepetAvatarController::class, 'myavatars']);
        Route::post('/avatar/{id}/equip', [NgepetAvatarController::class, 'equip']);
    });
    Route::prefix('intuition')->group(function () {
        Route::post('/start', [IntuitionGameController::class, 'start']);
        Route::get('/round/{matchId}', [IntuitionGameController::class, 'getRoundItems']);
        Route::post('/answer/{matchId}', [IntuitionGameController::class, 'submitAnswer']);
        Route::get('leaderboard', [IntuitionGameController::class, 'leaderboard']);
        Route::get('statistics', [IntuitionGameController::class, 'statistics']);
    });
    Route::prefix('logical')->group(function () {
        Route::post('start', [LogicalGameController::class, 'start']);
        Route::post('match', [LogicalGameController::class, 'activeMatch']);
        Route::post('answer', [LogicalGameController::class, 'answer']);
        Route::post('finish', [LogicalGameController::class, 'finish']);
        Route::get('scale', [LogicalGameController::class, 'scale']);
        Route::get('leaderboard', [LogicalGameController::class, 'leaderboard']);
        Route::get('statistics', [LogicalGameController::class, 'statistics']);
    });
    Route::prefix('trivia')->group(function () {
        Route::post('start', [TriviaGameController::class, 'start']);
        Route::post('answer', [TriviaGameController::class, 'answer']);
        Route::post('finish', [TriviaGameController::class, 'finish']);
        Route::get('leaderboard', [TriviaGameController::class, 'leaderboard']);
        Route::get('statistics', [TriviaGameController::class, 'statistics']);
        Route::get('/room/list', [TriviaGameController::class, 'listRooms']);
        Route::get('/room/active-room', [TriviaGameController::class, 'checkActiveRoom']);
        Route::post('/room/ready', [TriviaGameController::class, 'readyPlayer']);
        Route::post('/room/kick', [TriviaGameController::class, 'kickPlayer']);
        Route::post('/room/create', [TriviaGameController::class, 'createRoom']);
        Route::post('/room/join', [TriviaGameController::class, 'joinRoom']);
        Route::post('/room/start', [TriviaGameController::class, 'startRoom']);
        Route::post('/room/answer', [TriviaGameController::class, 'answerRoom']);
        Route::post('/room/finish', [TriviaGameController::class, 'finishRoom']);
        Route::post('/room/exit', [TriviaGameController::class, 'exitRoom']);
    });
    Route::prefix('tarot')->group(function () {
        Route::post('/start', [TarotRitualController::class, 'Start']);
        Route::post('/pick-card', [TarotRitualController::class, 'pickCards']);
        Route::post('/ai-reading', [TarotRitualController::class, 'aiReading']);
        Route::get('/history', [TarotRitualController::class, 'history']);
    });
    Route::prefix('ai-generator')->group(function () {
        Route::post('/instant', [GenerateImageController::class, 'instant']);
        Route::post('/trial', [GenerateImageController::class, 'trial']);
        Route::post('/task', [GenerateImageController::class, 'generate']);
        Route::get('/result/{taskId}', [GenerateImageController::class, 'result']);
    });
    Route::prefix('ulartangga')->group(function () {
        Route::get('/list-match', [UlartanggaGameController::class, 'listMatches']);
        Route::get('/active-match', [UlartanggaGameController::class, 'checkActiveMatch']);
        Route::post('/create-match', [UlartanggaGameController::class, 'createMatch']);
        Route::post('/join-match', [UlartanggaGameController::class, 'joinMatch']);
        Route::post('/match/ready', [UlartanggaGameController::class, 'readyPlayer']);
        Route::post('/match/kick-player', [UlartanggaGameController::class, 'kickPlayer']);
        Route::post('/match/exit-player', [UlartanggaGameController::class, 'exitPlayer']);
        Route::post('/match/create-bot', [UlartanggaGameController::class, 'createBotPlayer']);
        Route::post('/match/start', [UlartanggaGameController::class, 'startMatch']);
        Route::post('/match/throw-dice', [UlartanggaGameController::class, 'throwDice']);
        Route::post('/match/bot-throw-dice', [UlartanggaGameController::class, 'throwDiceForBot']);
        Route::post('/match/ongoing-match', [UlartanggaGameController::class, 'ongoingMatch']);
        Route::post('/match/exit', [UlartanggaGameController::class, 'exitMatch']);
        Route::get('/leaderboard', [UlartanggaGameController::class, 'leaderboard']);
        Route::get('/statistics', [UlartanggaGameController::class, 'statistics']);
    });
});
