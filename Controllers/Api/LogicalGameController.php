<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogicQuestion;
use App\Models\LogicAnswer;
use App\Models\LogicMatch;
use App\Models\LogicMatchAnswer;
use App\Models\LogicPlayer;
use App\Models\LogicIqScale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LogicalGameController extends Controller
{
    /**
     * Start new match or resume unfinished one
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
            $player = LogicPlayer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name
            ]);

            // Cek apakah masih ada match aktif
            $active = LogicMatch::where('player_id', $player->id)
                ->where('is_complete', false)
                ->latest()
                ->with(['answers.question.answers'])
                ->first();

            if ($active) {
                // Cari pertanyaan berikutnya yang belum dijawab
                $next = $active->answers()->whereNull('answer_id')->with('question.answers')->first();
                $answeredCount = $active->answers()->whereNotNull('answer_id')->count();

                $totalQuestions = $active->answers()->count();
                if (!$next) {
                    return response()->json(['status' => 'success', 'match_id' => $active->id, 'complete' => true]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Melanjutkan Permainan!',
                    'match_id' => $active->id,
                    'question' => $next->question,
                    'current_question' => $answeredCount + 1,
                    'total_question' => $totalQuestions,
                    'complete' => false
                ]);
            }

            $questionCount = LogicQuestion::count();
            if ($questionCount < 10) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Pertanyaan di database kurang dari 10, minimal diperlukan 10 Pertanyaan untuk memulai game."
                ], 422);
            }
            DB::beginTransaction();
            try {
                // Buat match baru
                $match = LogicMatch::create([
                    'player_id' => $player->id,
                    'started_at' => now(),
                    'is_complete' => false
                ]);

                // Ambil 10 pertanyaan acak
                $questions = LogicQuestion::inRandomOrder()->take(10)->get();

                foreach ($questions as $q) {
                    LogicMatchAnswer::create([
                        'match_id' => $match->id,
                        'question_id' => $q->id,
                        'answer_id' => null,
                        'value' => null
                    ]);
                }

                DB::commit();

                // Ambil pertanyaan pertama untuk ditampilkan
                $first = $questions->first();
                $first->load('answers');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Permainan baru dimulai!',
                    'match_id' => $match->id,
                    'question' => $first,
                    'current_question' => 1,
                    'total_question' => $questions->count(),
                    'complete' => false

                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Start Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memulai permainan.'
            ], 500);
        }
    }

    /**
     * Get active match (for resume)
     */
    public function activeMatch(Request $request)
    {
        $user = $request->user();
        // Pastikan user valid
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak terautentikasi.'
            ], 401);
        }
        $player = LogicPlayer::findOrFail(['user_id' => $user->id]);
        if (!$player) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pemain tidak ditemukan.'
            ], 404);
        }
        $match = LogicMatch::where('player_id', $player->id)
            ->where('is_complete', false)
            ->with(['answers.question.answers'])
            ->first();

        if (!$match) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada match aktif.'
            ], 404);
        }

        // Cari pertanyaan berikutnya yang belum dijawab
        $next = $match->answers()->whereNull('answer_id')->with('question.answers')->first();

        if (!$next) {
            return response()->json(['status' => 'success', 'complete' => true]);
        }

        return response()->json([
            'status' => 'success',
            'match_id' => $match->id,
            'question' => $next->question,
            'complete' => false
        ]);
    }

    public function answer(Request $request)
    {
        try {
            $request->validate([
                'match_id' => 'required',
                'question_id' => 'required|string',
                'answer_id' => 'required|string'
            ]);
            $user = $request->user();
            $player = LogicPlayer::firstWhere('user_id', $user->id);
            if (!$player) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemain tidak ditemukan.'
                ], 404);
            }
            $match = LogicMatch::where('id', $request->match_id)
                ->where('player_id', $player->id)
                ->where('is_complete', false)
                ->first();

            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Match tidak valid atau sudah selesai.'], 400);
            }
            $answer = LogicAnswer::find($request->answer_id);
            if (!$answer) {
                return response()->json(['status' => 'error', 'message' => 'Jawaban tidak ditemukan.'], 404);
            }

            $matchAnswer = LogicMatchAnswer::where('match_id', $match->id)
                ->where('question_id', $request->question_id)
                ->first();

            if (!$matchAnswer) {
                return response()->json(['status' => 'error', 'message' => 'Pertanyaan tidak valid.'], 400);
            }
            if ($matchAnswer->answer_id !== null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pertanyaan ini sudah dijawab sebelumnya.'
                ], 400);
            }
            if ($answer->question_id !== $request->question_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jawaban tidak sesuai dengan pertanyaan.'
                ], 400);
            }
            if ($matchAnswer->question_id !== $request->question_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pertanyaan tidak sesuai dengan match ini.'
                ], 400);
            }
            $matchAnswer->update([
                'answer_id' => $answer->id,
                'value' => $answer->value,
                'answered_at' => now()
            ]);

            // Ambil pertanyaan berikutnya
            $next = LogicMatchAnswer::where('match_id', $match->id)
                ->whereNull('answer_id')
                ->with('question.answers')
                ->first();

            if (!$next) {
                return response()->json(['status' => 'success', 'complete' => true]);
            }
            $answeredCount = LogicMatchAnswer::where('match_id', $match->id)
                ->whereNotNull('answer_id')
                ->count();

            $totalQuestions = LogicMatchAnswer::where('match_id', $match->id)->count();
            return response()->json([
                'status' => 'success',
                'next_question' => $next->question,
                'current_question' => $answeredCount + 1,
                'total_question' => $totalQuestions,
                'complete' => false
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Answer Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menjawab pertanyaan.'
            ], 500);
        }
    }


    public function finish(Request $request)
    {
        try {
            $request->validate([
                'match_id' => 'required'
            ]);
            $user = $request->user();
            $player = LogicPlayer::firstWhere('user_id', $user->id);
            if (!$player) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemain tidak ditemukan.'
                ], 404);
            }
            if (!$request) {
                return response()->json(['status' => 'error', 'message' => 'Request tidak ditemukan.'], 404);
            }
            $match = LogicMatch::where('id', $request->match_id)
                ->where('player_id', $player->id)
                ->where('is_complete', false)
                ->first();

            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Match tidak ditemukan atau sudah selesai.'], 404);
            }

            // aman: hitung finished_at dan duration dengan pengecekan
            $finishedAt = Carbon::now();

            // pastikan started_at dalam bentuk Carbon jika ada
            $startedAt = $match->started_at ? Carbon::parse($match->started_at) : null;
            $duration = $startedAt ? $startedAt->diffInSeconds($finishedAt) : 0;
            $total = LogicMatchAnswer::where('match_id', $match->id)->sum('value');
            Log::info('Match finished', ['total' => $total, 'duration' => $duration]);

            if ($total == '20') {
                if ($duration <= 10) {
                    $total += 3;
                } elseif ($duration <= 30) {
                    $total += 2;
                } elseif ($duration <= 60) {
                    $total += 1;
                }
            }
            $scale = LogicIqScale::where('point', '<=', $total)
                ->orderByDesc('point')
                ->first();

            $match->update([
                'total_point' => $total,
                'iq_result' => $scale?->iq_value ?? 100,
                'finished_at' => now(),
                'duration_seconds' => $duration,
                'is_complete' => true
            ]);

            // Update data player
            $player->update([
                'last_point' => $total,
                'last_iq' => $scale?->iq_value ?? 100,
                'highest_point' => max($player->highest_point, $total),
                'highest_iq' => max($player->highest_iq, $scale?->iq_value ?? 100)
            ]);

            return response()->json([
                'status' => 'success',
                'total_point' => $total,
                'iq' => $scale?->iq_value ?? 100,
                'category' => $scale?->category ?? 'Normal',
                'message' => $scale?->message ?? 'Tes logika selesai.',
                'duration_seconds' => $duration
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

    public function scale()
    {
        return response()->json(LogicIqScale::orderBy('point')->get());
    }
    public function leaderboard()
    {
        return response()->json(
            LogicPlayer::select('name', 'highest_point', 'highest_iq')
                ->orderByDesc('highest_iq')
                ->limit(10)
                ->get()
        );
    }
    public function statistics()
    {
        $player = LogicPlayer::withCount('matches')
            ->where('user_id', request()->user()->id)
            ->first();

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Data pemain tidak ditemukan'], 404);
        }

        return response()->json([
            'status'        => 'success',
            'name'          => $player->name,
            'highest_iq'    => $player->highest_iq,
            'last_iq'       => $player->last_iq,
            'total_match'   => $player->matches_count,
        ]);
    }
}
