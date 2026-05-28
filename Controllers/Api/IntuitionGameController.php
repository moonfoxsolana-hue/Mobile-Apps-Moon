<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IntuitionItem;
use App\Models\IntuitionPlayer;
use App\Models\IntuitionMatch;
use App\Models\IntuitionMatchAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class IntuitionGameController extends Controller
{
    // Mulai permainan baru
    public function start()
    {
        $user = Auth::user();

        // Pastikan user valid
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak terautentikasi.'
            ], 401);
        }

        // Pastikan player profile sudah ada atau buat baru
        $player = IntuitionPlayer::firstOrCreate(['user_id' => $user->id], [
            'token_reward' => 0,
            'level' => 1
        ]);

        // Cek apakah sudah ada match aktif
        $existingMatch = IntuitionMatch::where('player_id', $player->id)
            ->where('completed', '0')
            ->latest()
            ->first();

        if ($existingMatch) {
            // Lanjutkan match lama
            return response()->json([
                'status' => 'success',
                'message' => 'Melanjutkan match yang sedang berjalan.',
                'match_id' => $existingMatch->id,
                'current_round' => $existingMatch->current_round ?? 0,
                'total_rounds' => 10
            ]);
        }

        // Pastikan ada minimal 10 item di master
        $itemCount = IntuitionItem::count();
        if ($itemCount < 10) {
            return response()->json([
                'status' => 'error',
                'message' => "Item di database kurang dari 10, minimal diperlukan 10 item untuk memulai game."
            ], 422);
        }

        // Ambil 10 item acak sebagai jawaban benar
        $correctItems = IntuitionItem::inRandomOrder()->limit(10)->pluck('id')->toArray();

        // Buat match baru
        $match = IntuitionMatch::create([
            'id' => (string) Str::uuid(),
            'player_id' => $player->id,
            'correct_items' => json_encode($correctItems),
            'current_round' => 0,
            'completed' => '0',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Match baru dimulai!',
            'match_id' => $match->id,
            'current_round' => 0,
            'total_rounds' => 10
        ]);
    }


    // Ambil item untuk ronde saat ini
    public function getRoundItems($matchId)
    {
        $match = IntuitionMatch::findOrFail($matchId);
        $round = $match->current_round;
        $correctItems = json_decode($match->correct_items, true);

        if ($round >= count($correctItems)) {
            return response()->json(['message' => 'Match selesai'], 400);
        }

        $correctItem = $correctItems[$round];
        $otherItems = IntuitionItem::whereNotIn('id', [$correctItem])
            ->inRandomOrder()
            ->limit(2)
            ->pluck('id')
            ->toArray();

        $options = collect([$correctItem, ...$otherItems])
            ->shuffle()
            ->map(fn($id) => IntuitionItem::find($id));

        return response()->json([
            'round' => $round + 1,
            'options' => $options,
        ]);
    }

    // Submit jawaban player
    public function submitAnswer(Request $request, $matchId)
    {
        $user = Auth::user();
        $match = IntuitionMatch::with('player')->findOrFail($matchId);

        if ($match->player->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($match->completed) {
            return response()->json(['message' => 'Match sudah selesai'], 400);
        }

        $correctItems = json_decode($match->correct_items, true);
        $round = $match->current_round;

        if ($round >= count($correctItems)) {
            return response()->json(['message' => 'Semua ronde telah selesai'], 400);
        }

        $chosenItemId = $request->input('chosen_item_id');
        $correctItemId = $correctItems[$round];
        $isCorrect = $chosenItemId == $correctItemId;

        IntuitionMatchAnswer::create([
            'id' => (string) Str::uuid(),
            'match_id' => $match->id,
            'round_number' => $round + 1,
            'chosen_item_id' => $chosenItemId,
            'is_correct' => $isCorrect,
        ]);

        // Update ronde dan status match
        $match->current_round++;
        if ($match->current_round >= $match->rounds) {
            $match->completed = true;
        }
        $match->save();

        // Update statistik player
        $player = $match->player;
        $player->total_played += 1;
        if ($isCorrect) {
            $player->total_correct += 1;
            $player->token_reward += 10;
        }
        $player->level = 1 + intdiv($player->total_correct * 10, 100);
        $player->save();

        return response()->json([
            'correct' => $isCorrect,
            'correct_item_id' => $correctItemId,
            'match_completed' => $match->completed,
            'next_round' => $match->current_round + 1,
        ]);
    }
    public function leaderboard()
    {
        $leaders = IntuitionPlayer::with('user:id,name')
            ->orderByDesc('total_correct')
            ->take(10)
            ->get()
            ->map(function ($player) {
                return [
                    'name' => $player->user->name ?? 'Unknown',
                    'total_played' => $player->total_played,
                    'total_correct' => $player->total_correct,
                    'level' => $player->level,   
                ];
            });

        return response()->json($leaders);
    }
    public function statistics()
    {
        $player = IntuitionPlayer::where('user_id', request()->user()->id)
            ->first();

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Data pemain tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'total_played' => $player->total_played,
            'total_correct' => $player->total_correct,
            'level' => $player->level,
            'token_reward' => $player->token_reward,
        ]);
    }
}
