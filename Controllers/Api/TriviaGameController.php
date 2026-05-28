<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TriviaRoom;
use App\Models\TriviaRoomPlayer;
use App\Models\TriviaSession;
use App\Models\TriviaQuestion;
use App\Models\TriviaSessionQuestion;
use App\Models\TriviaSessionAnswer;
use App\Models\TriviaPlayer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Services\AiQuestionGenerator;
use Carbon\Carbon;

class TriviaGameController extends Controller
{
    /**
     * SINGLE PLAYER MODE
     */
    public function start(Request $request)
    {
        try {
            $user = $request->user();
            // Pastikan user valid
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }

            // Pastikan player profile sudah ada atau buat baru
            $player = TriviaPlayer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name
            ]);

            // Cek apakah masih ada match aktif
            $active = TriviaSession::where('player_id', $player->id)
                ->where('is_complete', false)
                ->whereNull('room_id')
                ->latest()
                ->with(['questions', 'answers'])
                ->first();

            if ($active) {
                // Cari pertanyaan berikutnya yang belum dijawab
                $next = $active->answers()->whereNull('chosen_answer')->with('question')->first();
                $answeredCount = $active->answers()->whereNotNull('chosen_answer')->count();
                $totalQuestions = $active->answers()->count();
                if (!$next) {
                    return response()->json(['status' => 'success', 'session_id' => $active->id, 'complete' => true]);
                }
                if ($next->question && is_array($next->question->answers)) {
                    $shuffledAnswers = collect($next->question->answers)->shuffle()->values()->toArray();
                    $next->question->answers = $shuffledAnswers;
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Melanjutkan Permainan!',
                    'session_id' => $active->id,
                    'question' => $next->question,
                    'current_question' => $answeredCount + 1,
                    'total_question' => $totalQuestions,
                    'complete' => false
                ]);
            }
            // === BAGIAN BARU ===
            $request->validate([
                'category' => 'required|string',
                'question_count' => 'required|integer|min:5|max:50'
            ]);
            $questionCount = $request->question_count ?? 10;
            $availableCount = TriviaQuestion::where('category', $request->category)->count();

            if ($availableCount < 1000) {
                // Generate pertanyaan baru pakai AI
                $generator = new AiQuestionGenerator();
                $generated = $generator->generate($request->category, $questionCount);
                $questions = $generated->take($questionCount);
            } else {
                // Ambil pertanyaan acak dari DB
                $questions = TriviaQuestion::inRandomOrder()->take($questionCount)->get();
            }

            // Buat session baru
            $session = TriviaSession::create([
                'player_id' => $player->id,
                'total_questions' => $questionCount,
                'room_id' => null,
            ]);

            foreach ($questions as $index => $q) {
                TriviaSessionQuestion::create([
                    'session_id' => $session->id,
                    'question_id' => $q->id,
                    'order' => $index + 1,
                ]);
                TriviaSessionAnswer::create([
                    'session_id' => $session->id,
                    'question_id' => $q->id,
                    'player_id' => $player->id,
                    'chosen_answer' => null,
                    'correct_answer' => $q->correct_answer,
                ]);
            }

            $first = $questions->first();
            // $first->load('answers');
            return response()->json([
                'status' => 'success',
                'message' => 'Permainan baru dimulai!',
                'session_id' => $session->id,
                'question' => $first,
                'current_question' => 1,
                'total_question' => $questionCount,
                'complete' => false,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message ' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Start Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memulai permainan.'
            ], 500);
        }
    }

    public function answer(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }
            $player = TriviaPlayer::firstWhere('user_id', $user->id);
            if (!$player) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemain tidak ditemukan.'
                ], 404);
            }
            $request->validate([
                'session_id' => 'required|integer',
                'question_id' => 'required|integer',
                'selected_answer' => 'nullable|string',
            ]);

            $sessionQuestion = TriviaSessionAnswer::where('session_id', $request->session_id)
                ->where('question_id', $request->question_id)
                ->whereNull('chosen_answer')
                ->first();

            if (!$sessionQuestion) {
                return response()->json(['status' => 'error', 'message' => 'Pertanyaan tidak valid.'], 400);
            }
            if ($sessionQuestion->chosen_answer !== null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pertanyaan ini sudah dijawab sebelumnya.'
                ], 400);
            }
            if ($sessionQuestion->question_id !== $request->question_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jawaban tidak sesuai dengan pertanyaan.'
                ], 400);
            }
            $question = $sessionQuestion->question;

            $session = TriviaSession::where('id', $request->session_id)
                ->where('player_id', $player->id)
                ->where('is_complete', false)
                ->whereNull('room_id')
                ->latest()
                ->with(['questions', 'answers'])
                ->first();
            if (!$session) {
                return response()->json(['status' => 'error', 'message' => 'Session tidak valid atau sudah selesai.'], 400);
            }
            $isCorrect = strtolower(trim($request->selected_answer)) === strtolower(trim($question->correct_answer));
            $lastAnswer = TriviaSessionAnswer::where('session_id', $request->session_id)
                ->where('player_id', $player->id)
                ->orderByDesc('answered_at')
                ->first();
            if ($lastAnswer && $lastAnswer->answered_at) {
                $duration = now()->diffInSeconds($lastAnswer->answered_at); // dari jawaban sebelumnya
            } else {
                $duration = now()->diffInSeconds($session->created_at); // dari awal sesi
            }
            $sessionQuestion->update([
                'chosen_answer' => $request->selected_answer,
                'is_correct' => $isCorrect,
                'answered_at' => now(),
                'duration' => $duration,
            ]);
            $answers = TriviaSessionAnswer::where('session_id', $request->session_id)
                ->whereNotNull('chosen_answer')
                ->orderBy('id', 'asc')
                ->pluck('is_correct')
                ->toArray();

            $streak = 0;
            $maxStreak = 0;

            foreach ($answers as $ans) {
                if ($ans == 1) {
                    $streak++;
                    $maxStreak = max($maxStreak, $streak);
                } else {
                    $streak = 0;
                }
            }

            // Update session
            $session->update([
                'current_streak' => $maxStreak,
            ]);
            if ($isCorrect) {
                $player->total_correct += 1;
                $player->token_reward += 10;
            } else {
                $player->total_wrong += 1;
            }
            $player->save();
            $next = $session->answers()->whereNull('chosen_answer')->with('question')->first();
            if (!$next) {
                return response()->json(['status' => 'success', 'correct_answer' => $question->correct_answer, 'streak' => $streak, 'complete' => true]);
            }
            $answeredCount = $session->answers()->whereNotNull('chosen_answer')->count();
            $totalQuestions = $session->answers()->count();
            if ($next->question && is_array($next->question->answers)) {
                $shuffledAnswers = collect($next->question->answers)->shuffle()->values()->toArray();
                $next->question->answers = $shuffledAnswers;
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Jawaban diterima.',
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'streak' => $streak,
                'next_question' => $next->question,
                'current_question' => $answeredCount + 1,
                'total_question' => $totalQuestions,
                'complete' => false
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message ' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Answer Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengirim jawaban.'
            ], 500);
        }
    }
    public function finish(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required'
            ]);
            $user = $request->user();
            $player = TriviaPlayer::firstWhere('user_id', $user->id);
            if (!$player) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemain tidak ditemukan.'
                ], 404);
            }
            if (!$request) {
                return response()->json(['status' => 'error', 'message' => 'Request tidak ditemukan.'], 404);
            }
            $session = TriviaSession::where('id', $request->session_id)
                ->where('player_id', $player->id)
                ->where('is_complete', false)
                ->first();

            if (!$session) {
                return response()->json(['status' => 'error', 'message' => 'Session tidak ditemukan atau sudah selesai.'], 404);
            }

            $total = TriviaSessionAnswer::where('session_id', $session->id)->sum('is_correct');
            $totalDuration = TriviaSessionAnswer::where('session_id', $session->id)->sum('duration');

            if ($total == '10') {
                if ($totalDuration <= 10) {
                    $total += 3;
                } elseif ($totalDuration <= 20) {
                    $total += 2;
                } elseif ($totalDuration <= 30) {
                    $total += 1;
                }
            }

            $session->update([
                'current_score' => $total * 10,
                'finished_at' => now(),
                'duration_seconds' => $totalDuration,
                'is_complete' => true
            ]);

            // Update data player
            $player->update([
                'total_played' => $player->total_played + 1,
                'highest_score' => max($player->highest_score, $total * 10),
                'highest_streak' => max($player->highest_streak, $session->current_streak),
            ]);

            return response()->json([
                'status' => 'success',
                'score' => $total * 10,
                'streak' => $session->current_streak,
                'category' => $session->category,
                'duration_seconds' => round($totalDuration)
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Finish Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyelesaikan permainan.'
            ], 500);
        }
    }

    public function leaderboard()
    {
        return response()->json(
            TriviaPlayer::select('name', 'total_played', 'highest_score', 'highest_streak', 'average_accuracy')
                ->orderByDesc('highest_score')
                ->limit(10)
                ->get()
        );
    }
    /**
     *  MULTIPLAYER ROOM MODE
     */
    public function listRooms(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }
            $player = TriviaPlayer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name
            ]);

            // Ambil room yang sedang diikuti player (jika ada)
            $active = TriviaRoom::whereHas('players', function ($q) use ($player) {
                $q->where('player_id', $player->id);
            })
                ->whereIn('status', ['waiting', 'playing'])
                ->with('players', 'players.player:id,name')
                ->latest()
                ->first();

            if ($active && $active->created_at <= now()->subMinutes(60)) {
                // Hapus room lama yang tidak aktif
                $active->status = 'finished';
                $active->save();
            }
            // Ambil semua room yang masih menunggu pemain
            $rooms = TriviaRoom::select('id', 'name', 'category', 'max_players', 'status')
                ->withCount('players')
                ->where('status', 'waiting')
                ->orderByDesc('created_at')
                ->get();
            return response()->json([
                'status' => 'success',
                'player_id' => $player ? $player->id : null,
                'room_detail' => $active,
                'rooms' => $rooms,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat daftar room.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function checkActiveRoom(Request $request)
    {
        $user = $request->user();
        $player = TriviaPlayer::where('user_id', $user->id)->first();

        if (!$player) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pemain tidak ditemukan.'
            ], 404);
        }

        // Cari room aktif di mana player masih tergabung
        $activeRoom = TriviaRoom::whereHas('players', function ($q) use ($player) {
            $q->where('player_id', $player->id);
        })
            ->with(['players.player:id,name'])
            ->whereIn('status', ['waiting', 'playing'])
            ->latest()
            ->first();

        if (!$activeRoom) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada room aktif.'
            ], 404);
        }

        // Ambil session berdasarkan room_id (bukan trivia_room_id)
        $session = TriviaSession::where('room_id', $activeRoom->id)->first();

        // Jika session belum dibuat, berarti game belum mulai
        if (!$session) {
            return response()->json([
                'status' => 'success',
                'state' => 'waiting',
                'room_detail' => $activeRoom,
                'message' => 'Menunggu permainan dimulai oleh host.'
            ]);
        }

        // Ambil semua question_id yang sudah dijawab oleh pemain di session ini
        $answeredQuestionIds = TriviaSessionAnswer::where('session_id', $session->id)
            ->where('player_id', $player->id)
            ->pluck('question_id');

        // Cari pertanyaan session yang belum dijawab
        $next = TriviaSessionQuestion::where('session_id', $session->id)
            ->whereNotIn('question_id', $answeredQuestionIds)
            ->with('question')
            ->orderBy('order')
            ->first();


        // Hitung progres pemain
        $answeredCount = TriviaSessionAnswer::where('session_id', $session->id)
            ->where('player_id', $player->id)
            ->whereNotNull('chosen_answer')
            ->count();

        $totalQuestions = TriviaSessionQuestion::where('session_id', $session->id)->count();

        // Jika tidak ada pertanyaan tersisa
        if (!$next) {
            return response()->json([
                'status' => 'success',
                'state' => 'finished',
                'room_detail' => $activeRoom,
                'player_id' => $player->id,
                'message' => 'Semua pertanyaan sudah dijawab.'
            ]);
        }

        // Acak jawaban
        if ($next->question && is_array($next->question->answers)) {
            $next->question->answers = collect($next->question->answers)
                ->shuffle()
                ->values()
                ->toArray();
        }
        $logicmode = str_starts_with($activeRoom->category, 'logic_mode -');

        return response()->json([
            'status' => 'success',
            'state' => 'playing',
            'room_detail' => $activeRoom,
            'player_id' => $player->id,
            'question' => $next->question,
            'current_question' => $answeredCount + 1,
            'total_question' => $totalQuestions,
            'complete' => false,
            'logic_mode' => $logicmode,
        ]);
    }

    public function readyPlayer(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer',
            'is_ready' => 'required|boolean',
        ]);
        $player = TriviaPlayer::where('user_id', $request->user()->id)->first();

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Pemain tidak ditemukan'], 404);
        }
        $room = TriviaRoom::find($request->room_id);
        if (!$room) {
            return response()->json(['status' => 'error', 'message' => 'Room tidak ditemukan'], 404);
        }
        try {
            TriviaRoomPlayer::where('room_id', $room->id)
                ->where('player_id', $player->id)
                ->update(['is_ready' => $request->is_ready]);
            if ($request->is_ready  == true) {
                return response()->json(['status' => 'success', 'message' => 'Anda sudah siap']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'Anda batal siap']);
            }
        } catch (\Exception $e) {
            Log::error('Ready Player Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menandai siap.'
            ], 500);
        }
    }
    public function kickPlayer(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer',
            'player_id' => 'required|integer',
        ]);
        $player = TriviaPlayer::where('user_id', $request->user()->id)->first();

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Pemain tidak ditemukan'], 404);
        }
        $room = TriviaRoom::find($request->room_id);
        if (!$room) {
            return response()->json(['status' => 'error', 'message' => 'Room tidak ditemukan'], 404);
        }
        $playerId = $request->player_id;
        try {
            $isHost = TriviaRoomPlayer::where('room_id', $room->id)
                ->where('player_id', $player->id)
                ->where('is_host', true)
                ->exists();
            if (!$isHost) {
                return response()->json(['status' => 'error', 'message' => 'Hanya host yang dapat mengeluarkan pemain'], 403);
            }
            TriviaRoomPlayer::where('room_id', $room->id)
                ->where('player_id', $playerId)
                ->delete();
            return response()->json(['status' => 'success', 'message' => 'Pemain dikeluarkan']);
        } catch (\Exception $e) {
            Log::error('Kick Player Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengeluarkan pemain.'
            ], 500);
        }
    }

    // 1️⃣ Buat Room
    public function createRoom(Request $request)
    {
        try {
            $user = $request->user();
            // Pastikan user valid
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }

            // Pastikan player profile sudah ada atau buat baru
            $player = TriviaPlayer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name
            ]);

            $active = TriviaRoom::where('host_id', $player->id)
                ->whereNotIn('status', ['finished'])
                ->latest()
                ->first();
            if ($active && $active->created_at > now()->subMinutes(60)) {
                return response()->json(['status' => 'error', 'message' => 'Selesaikan permainan aktif Anda terlebih dahulu.'], 400);
            };
            if ($active && $active->created_at <= now()->subMinutes(60)) {
                // Hapus room lama yang tidak aktif
                $active->status = 'finished';
                $active->save();
            }
            $request->validate([
                'name' => 'required|string',
                'category' => 'nullable|string',
                'max_players' => 'required|integer|min:2|max:99',
                'join_code' => 'nullable|string|size:6',
                'logic_mode' => 'nullable|boolean:true,false',
            ]);

            if ($request->logic_mode == true) {
                $category = "logic_mode - " . ($request->category ?? 'pengetahuan umum');
            } else {
                $category = $request->category ?? 'pengetahuan umum';
            }

            $room = TriviaRoom::create([
                'name' => $request->name,
                'category' => $category ?? 'pengetahuan umum',
                'join_code' => $request->join_code,
                'host_id' => $player->id,
                'max_players' => $request->max_players,
                'status' => 'waiting',
            ]);

            // Tambahkan host ke room
            TriviaRoomPlayer::create([
                'room_id' => $room->id,
                'player_id' => $player->id,
                'is_host' => true,
                'joined_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'player_id' => $player ? $player->id : null,
                'message' => 'Room berhasil dibuat.',
                'room_detail' => $room->load('players', 'players.player:id,name')
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message ' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Create Room Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membuat room.'
            ], 500);
        }
    }

    // 2️⃣ Join Room
    public function joinRoom(Request $request)
    {
        $player = TriviaPlayer::where('user_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'room_id' => 'required|integer|exists:trivia_rooms,id',
            'join_code' => 'nullable|string',
        ]);

        $room = TriviaRoom::where('id', $validated['room_id'])->firstOrFail();

        if ($room->join_code && $room->join_code !== null) {
            if (!isset($validated['join_code']) || $validated['join_code'] !== $room->join_code) {
                return response()->json(['status' => 'error', 'message' => 'Kode bergabung salah.'], 400);
            }
        }

        if ($room->status !== 'waiting') {
            return response()->json(['status' => 'error', 'message' => 'Room sudah dimulai atau selesai.'], 400);
        }

        $currentPlayers = $room->players()->count();
        if ($currentPlayers >= $room->max_players) {
            return response()->json(['status' => 'error', 'message' => 'Room sudah penuh.'], 400);
        }

        $exists = TriviaRoomPlayer::where('room_id', $room->id)
            ->where('player_id', $player->id)
            ->exists();

        if (!$exists) {
            TriviaRoomPlayer::create([
                'room_id' => $room->id,
                'player_id' => $player->id,
                'joined_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'player_id' => $player ? $player->id : null,
            'message' => 'Berhasil bergabung ke room.',
            'room_detail' => $room->load('players.player'),
        ]);
    }

    // 3️⃣ Host memulai permainan
    public function startRoom(Request $request)
    {
        $player = TriviaPlayer::where('user_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'room_id' => 'required|integer',
            'question_count' => 'nullable|integer|min:3|max:50'
        ]);

        $room = TriviaRoom::with('players')->findOrFail($validated['room_id']);

        if ($room->host_id !== $player->id) {
            return response()->json(['status' => 'error', 'message' => 'Hanya host yang bisa memulai permainan.'], 403);
        }
        if ($room->players()->count() < 2) {
            return response()->json(['status' => 'error', 'message' => 'Dibutuhkan minimal 2 pemain untuk memulai permainan.'], 400);
        }
        if ($room->status !== 'waiting') {
            return response()->json(['status' => 'error', 'message' => 'Room sudah dimulai.'], 400);
        }
        $notReadyPlayers = $room->players
            ->where('is_host', false)
            ->where('is_ready', false);

        if ($notReadyPlayers->count() > 0) {
            $names = $notReadyPlayers
                ->map(fn($p) => $p->player->user->name ?? 'Pemain #' . $p->player_id)
                ->implode(', ');

            return response()->json([
                'status' => 'error',
                'message' => 'Tidak semua pemain siap. Pemain berikut belum ready: ' . $names,
                'not_ready' => $notReadyPlayers->values(),
            ], 400);
        }

        $questionCount = $validated['question_count'] ?? 10;
        $availableCount = TriviaQuestion::where('category', $room->category)->count();
        $logicmode = str_starts_with($room->category, 'logic_mode -');
        Log::info('Logic Mode: ' . ($logicmode ? 'true' : 'false'));
        if ($availableCount < 1000) {
            // Generate pertanyaan baru pakai AI
            $generator = new AiQuestionGenerator();
            if ($logicmode) {
                $baseCategory = substr($room->category, strlen('logic_mode -'));
                $generated = $generator->generatelogicquestion($baseCategory, $questionCount);
                $questions = $generated->take($questionCount);
            } else {
                $generated = $generator->generate($room->category, $questionCount);
                $questions = $generated->take($questionCount);
            }
        } else {
            // Ambil pertanyaan acak dari DB
            $questions = TriviaQuestion::inRandomOrder()->take($questionCount)->get();
        }

        DB::transaction(function () use ($room, $questions) {
            $session = TriviaSession::create([
                'room_id' => $room->id,
                'player_id' => $room->host_id,
                'total_questions' => $questions->count(),
            ]);

            foreach ($questions as $index => $q) {
                TriviaSessionQuestion::create([
                    'session_id' => $session->id,
                    'question_id' => $q->id,
                    'order' => $index + 1,
                ]);
            }

            $room->update(['status' => 'playing']);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Permainan dimulai!',
            'room_id' => $room->id,
            'questions' => $questions->first(),
            'current_question' => 1,
            'total_questions' => $questions->count(),
            'logic_mode' => $logicmode,
        ]);
    }

    // 4️⃣ Jawab pertanyaan di room
    public function answerRoom(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }

            $player = TriviaPlayer::firstWhere('user_id', $user->id);
            if (!$player) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemain tidak ditemukan.'
                ], 404);
            }

            $validated = $request->validate([
                'room_id' => 'required|integer',
                'question_id' => 'required|integer',
                'selected_answer' => 'nullable|string'
            ]);

            if (!isset($validated['selected_answer']) || trim($validated['selected_answer']) === '') {
                $validated['selected_answer'] = 'tidak menjawab';
            }
            // Pastikan room valid
            $room = TriviaRoom::findOrFail($validated['room_id']);
            $session = $room->session;

            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Session belum dimulai.'
                ], 400);
            }
            $logicmode = str_starts_with($room->category, 'logic_mode -');
            // Ambil pertanyaan session yang belum dijawab
            $sessionQuestion = TriviaSessionQuestion::where('session_id', $session->id)
                ->where('question_id', $validated['question_id'])
                ->first();

            if (!$sessionQuestion) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pertanyaan tidak ditemukan untuk sesi ini.'
                ], 404);
            }
            $question = $sessionQuestion->question;

            if (!$question) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pertanyaan tidak ditemukan.'
                ], 404);
            }

            // Cek apakah sudah dijawab
            $existingAnswer = TriviaSessionAnswer::where('session_id', $session->id)
                ->where('question_id', $validated['question_id'])
                ->where('player_id', $player->id)
                ->first();

            if ($existingAnswer && $existingAnswer->chosen_answer !== null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pertanyaan ini sudah dijawab sebelumnya.'
                ], 400);
            }
            // ✅ Hitung durasi dinamis:

            $lastAnswer = TriviaSessionAnswer::where('session_id', $session->id)
                ->where('player_id', $player->id)
                ->orderByDesc('answered_at')
                ->first();

            if ($lastAnswer) {
                $duration = now()->diffInSeconds($lastAnswer->answered_at); // dari jawaban sebelumnya
            } else {
                $duration = now()->diffInSeconds($session->created_at); // dari awal sesi
            }
            // Cek kebenaran jawaban
            $isCorrect = strtolower(trim($validated['selected_answer'])) === strtolower(trim($question->correct_answer));

            // Simpan jawaban
            TriviaSessionAnswer::updateOrCreate(
                [
                    'session_id' => $session->id,
                    'question_id' => $validated['question_id'],
                    'player_id' => $player->id,
                ],
                [
                    'chosen_answer' => $validated['selected_answer'],
                    'is_correct' => $isCorrect,
                    'duration' => $duration,
                    'answered_at' => now(),
                ]
            );

            // Hitung ulang skor pemain di room
            $answers = TriviaSessionAnswer::where('player_id', $player->id)
                ->whereHas('session', fn($q) => $q->where('room_id', $room->id))
                ->get();

            $score = $answers->where('is_correct', true)->count();
            $totalDuration = $answers->sum('duration');

            TriviaRoomPlayer::where('room_id', $room->id)
                ->where('player_id', $player->id)
                ->update([
                    'score' => $score * 10,
                    'duration' => $totalDuration,
                ]);

            // Cari pertanyaan berikutnya
            // Ambil semua question_id yang sudah dijawab oleh pemain di session ini
            $answeredQuestionIds = TriviaSessionAnswer::where('session_id', $session->id)
                ->where('player_id', $player->id)
                ->pluck('question_id');

            // Cari pertanyaan session yang belum dijawab
            $next = TriviaSessionQuestion::where('session_id', $session->id)
                ->whereNotIn('question_id', $answeredQuestionIds)
                ->with('question')
                ->orderBy('order')
                ->first();


            if (!$next) {
                return response()->json([
                    'status' => 'success',
                    'correct_answer' => $question->correct_answer,
                    'complete' => true
                ]);
            }

            $answeredCount = $session->answers()->whereNotNull('chosen_answer')->count();
            $totalQuestions = $session->answers()->count();

            // Acak urutan jawaban
            if ($next->question && is_array($next->question->answers)) {
                $next->question->answers = collect($next->question->answers)->shuffle()->values()->toArray();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Jawaban diterima.',
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'current_score' => $score * 10,
                'next_question' => $next->question,
                'current_question' => $answeredCount + 1,
                'total_question' => $totalQuestions,
                'complete' => false,
                'logic_mode' => $logicmode,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('AnswerRoom Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses jawaban.'
            ], 500);
        }
    }

    public function finishRoom(Request $request)
    {
        $player = TriviaPlayer::where('user_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'room_id' => 'required|integer',
        ]);

        $room = TriviaRoom::with(['players.player:id,name'])->findOrFail($validated['room_id']);
        $session = $room->session;

        if (!$session) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session belum dimulai.'
            ], 400);
        }

        // Cegah double finish
        if ($room->status === 'finished') {
            return response()->json([
                'status' => 'success',
                'message' => 'Permainan telah selesai.',
                'leaderboard' => $this->leaderboardRoom($room, $session),
                'room_finished' => true
            ]);
        }

        // Ambil semua jawaban di session ini
        $answers = TriviaSessionAnswer::whereHas('session', fn($q) => $q->where('room_id', $room->id))->get();

        // Hitung skor per pemain
        $playerStats = TriviaRoomPlayer::where('room_id', $room->id)
            ->with('player:id,name')
            ->get()
            ->map(function ($rp) use ($answers) {
                $playerAnswers = $answers->where('player_id', $rp->player_id);

                $score = $playerAnswers->where('is_correct', true)->count();
                $totalDuration = $playerAnswers->sum('duration');

                // Update ke DB
                $rp->update([
                    'score' => $score * 10,
                    'duration' => $totalDuration,
                ]);

                return [
                    'player_id' => $rp->player_id,
                    'name' => $rp->player->name,
                    'score' => $score * 10,
                    'duration' => $totalDuration,
                ];
            })
            ->sortByDesc('score')
            ->values();

        // Hitung total waktu permainan
        $durationSeconds = now()->diffInSeconds($session->created_at);

        // Hitung pemain yang sudah menjawab semua pertanyaan
        $totalQuestions = TriviaSessionQuestion::where('session_id', $session->id)->count();
        $playerAnswerCount = TriviaSessionAnswer::select('player_id')
            ->where('session_id', $session->id)
            ->selectRaw('COUNT(DISTINCT question_id) as answered')
            ->groupBy('player_id')
            ->get()
            ->pluck('answered', 'player_id');

        $totalPlayers = $room->players->count();
        $playersFinished = $playerAnswerCount->filter(fn($a) => $a >= $totalQuestions)->count();

        // Timeout auto finish (contoh 2 menit sejak pertanyaan terakhir)
        $lastAnswer = TriviaSessionAnswer::where('session_id', $session->id)->latest()->first();
        $timeSinceLastAnswer = $lastAnswer ? now()->diffInSeconds($lastAnswer->answered_at) : 0;

        // Jika semua player sudah jawab atau waktu habis → room selesai
        if ($playersFinished >= $totalPlayers || $timeSinceLastAnswer > 120) {
            $room->update([
                'status' => 'finished'
            ]);
            $session->update([
                'is_complete' => true,
                'duration_seconds' => $durationSeconds,
                'finished_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => $room->status === 'finished'
                ? 'Permainan telah selesai.'
                : 'Menunggu pemain lain menyelesaikan permainan.',
            'leaderboard' => $playerStats,
            'room_finished' => $room->status === 'finished'
        ]);
    }

    public function exitRoom(Request $request)
    {
        try {
            $player = TriviaPlayer::where('user_id', $request->user()->id)->first();
            if (!$player) {
                return response()->json(['status' => 'error', 'message' => 'Pemain tidak ditemukan'], 404);
            }

            // Validasi input
            $validated = $request->validate([
                'room_id' => 'required|exists:trivia_rooms,id',
            ]);

            $room = TriviaRoom::where('id', $validated['room_id'])->first();
            if (!$room) {
                return response()->json(['status' => 'error', 'message' => 'Room tidak ditemukan'], 404);
            }

            // Cek apakah user adalah host / pembuat room
            $isHost = $room->host_id === $player->id;

            if ($isHost) {
                // Jika host keluar → room ditutup
                $room->update(['status' => 'finished']);

                // Hapus semua pemain di room
                TriviaRoomPlayer::where('room_id', $room->id)->delete();

                // Bisa juga optional: hapus semua sesi/pertanyaan aktif

                return response()->json([
                    'status' => 'success',
                    'message' => 'Room telah ditutup oleh host.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exit Room Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat keluar dari room.'
            ], 500);
        }

        // Hapus pemain dari room
        TriviaRoomPlayer::where('room_id', $room->id)
            ->where('player_id', $player->id)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Anda telah keluar dari room.',
        ]);
    }

    /**
     * Fungsi bantu untuk ambil leaderboard data tanpa duplikasi
     */
    protected function leaderboardRoom($room, $session)
    {
        $answers = TriviaSessionAnswer::whereHas('session', fn($q) => $q->where('room_id', $room->id))->get();

        return TriviaRoomPlayer::where('room_id', $room->id)
            ->with('player:id,name')
            ->get()
            ->map(function ($rp) use ($answers) {
                $playerAnswers = $answers->where('player_id', $rp->player_id);

                $score = $playerAnswers->where('is_correct', true)->count();
                $totalDuration = $playerAnswers->sum('duration');

                // Update ke DB
                $rp->update([
                    'score' => $score * 10,
                    'duration' => $totalDuration,
                ]);

                return [
                    'player_id' => $rp->player_id,
                    'name' => $rp->player->name,
                    'score' => $score * 10,
                    'duration' => $totalDuration,
                ];
            })
            ->sortBy([
                ['score', 'desc'],
                ['duration', 'asc'],
            ])
            ->values();
    }

    public function statistics()
    {
        $player = TriviaPlayer::where('user_id', request()->user()->id)
            ->first();

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Data pemain tidak ditemukan'], 404);
        }

        return response()->json([
            'status'        => 'success',
            'name'          => $player->name,
            'total_played'  => $player->total_played,
            'total_correct' => $player->total_correct,
            'total_wrong'   => $player->total_wrong,
            'highest_score' => $player->highest_score,
            'average_accuracy' => $player->average_accuracy,
        ]);
    }
}
