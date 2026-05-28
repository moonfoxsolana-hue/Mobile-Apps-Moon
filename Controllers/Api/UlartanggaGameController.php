<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UlartanggaTile;
use App\Models\UlartanggaAvatar;
use App\Models\UlartanggaItem;
use App\Models\UlartanggaPlayer;
use App\Models\UlartanggaPlayerAvatar;
use App\Models\UlartanggaMatch;
use App\Models\UlartanggaMatchPlayer;
use App\Models\UlartanggaEventLog;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UlartanggaGameController extends Controller
{

    public function listMatches(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }
            $player = UlartanggaPlayer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name
            ]);
            // Ambil match yang sedang diikuti player (jika ada)
            $active = UlartanggaMatch::whereHas('matchPlayers', function ($q) use ($player) {
                $q->where('player_id', $player->id);
            })
                ->whereIn('status', ['waiting', 'playing'])
                ->with(['matchPlayers' => function ($q) {
                    $q->select('id', 'match_id', 'player_id', 'name', 'is_host', 'is_bot', 'is_ready', 'turn_order', 'position', 'is_turn', 'player_state', 'item_owned');
                }])
                ->latest()
                ->first();



            if ($active && $active->created_at <= now()->subMinutes(60)) {
                // Hapus match lama yang tidak aktif
                $active->status = 'finished';
                $active->is_complete = true;
                $active->ended_at = now();
                $active->save();
            }
            // Ambil semua match yang masih menunggu pemain
            $matches = UlartanggaMatch::select('id', 'name', 'status', 'max_players', 'created_at')
                ->withCount('matchPlayers')
                ->where('status', 'waiting')
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'status' => 'success',
                'player_id' => $player ? $player->id : null,
                'match_detail' => $active,
                'matches' => $matches,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat daftar match.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function readyPlayer(Request $request)
    {
        try {
            $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
                'is_ready' => 'required|boolean',
            ]);
            $player = UlartanggaPlayer::where('user_id', $request->user()->id)->first();

            if (!$player) {
                return response()->json(['status' => 'error', 'message' => 'Pemain tidak ditemukan'], 404);
            }
            $match = UlartanggaMatch::find($request->match_id);
            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Match tidak ditemukan'], 404);
            }
            try {
                UlartanggaMatchPlayer::where('match_id', $match->id)
                    ->where('player_id', $player->id)
                    ->update(['is_ready' => $request->is_ready]);
                if ($request->is_ready  == true) {
                    return response()->json(['status' => 'success', 'message' => 'Anda sudah siap']);
                } else {
                    return response()->json(['status' => 'success', 'message' => 'Anda tidak siap']);
                }
            } catch (\Exception $e) {
                Log::error('Ready Player Error: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat menandai siap.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Ready Player Outer Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menandai siap.'
            ], 500);
        }
    }
    public function exitPlayer(Request $request)
    {
        try {
            $user = $request->user();
            $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
            ]);
            $player = UlartanggaPlayer::where('user_id', $user->id)->first();

            if (!$player) {
                return response()->json(['status' => 'error', 'message' => 'Pemain tidak ditemukan'], 404);
            }
            $match = UlartanggaMatch::find($request->match_id);
            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Match tidak ditemukan'], 404);
            }
            try {
                $playerId = $player->id;
                UlartanggaMatchPlayer::where('match_id', $match->id)
                    ->where('player_id', $playerId)
                    ->delete();
                return response()->json(['status' => 'success', 'message' => 'Berhasil keluar dari ruangan']);
            } catch (\Exception $e) {
                Log::error('Kick Player Error: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat keluar dari ruangan.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Kick Player Outer Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat keluar dari ruangan.'
            ], 500);
        }
    }
    public function kickPlayer(Request $request)
    {
        try {
            $user = $request->user();
            $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
                'player_id' => 'required|string',
            ]);
            $player = UlartanggaPlayer::where('user_id', $user->id)->first();

            Log::info('Kicker Player ID: ' . ($player ? $player->id : 'Not Found'));
            if (!$player) {
                return response()->json(['status' => 'error', 'message' => 'Pemain tidak ditemukan'], 404);
            }
            $match = UlartanggaMatch::find($request->match_id);
            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Match tidak ditemukan'], 404);
            }
            if ($match->host_id !== $player->id) {
                return response()->json(['status' => 'error', 'message' => 'Hanya host yang bisa mengeluarkan pemain.'], 403);
            }
            try {
                $playerId = $request->player_id;
                UlartanggaMatchPlayer::where('match_id', $match->id)
                    ->where('player_id', $playerId)
                    ->delete();
                return response()->json(['status' => 'success', 'message' => 'Pemain dikeluarkan']);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat mengeluarkan pemain.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengeluarkan pemain.'
            ], 500);
        }
    }

    public function createBotPlayer(Request $request)
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
            $player = UlartanggaPlayer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name
            ]);
            $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
            ]);
            $match = UlartanggaMatch::find($request->match_id);
            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Match tidak ditemukan'], 404);
            }
            if ($match->host_id !== $player->id) {
                return response()->json(['status' => 'error', 'message' => 'Hanya host yang bisa memulai permainan.'], 403);
            }
            $matchPlayersCount = UlartanggaMatchPlayer::where('match_id', $match->id)->count();
            if ($matchPlayersCount > $match->max_players - 1) {
                return response()->json(['status' => 'error', 'message' => 'Jumlah pemain sudah maksimal, tidak bisa menambah bot'], 400);
            }
            $botNames = [
                "Pocong Lucu",
                "Kuntilanak Ceria",
                "Genderuwo Ramah",
                "Sundel Bolong Baik",
                "Tuyul Pintar",
                "Jin Pengasih",
                "Siluman Ular",
                "Hantu Penyayang",
                "Setan Penolong",
                "Makhluk Ajaib"
            ];
            // Buat bot player
            $botPlayer = UlartanggaMatchPlayer::create([
                'match_id' => $match->id,
                'player_id' => (string) Str::uuid(),
                'name' => $botNames[array_rand($botNames)],
                'is_host' => false,
                'is_bot' => true,
                'joined_at' => now(),
                'is_ready' => true,
                'player_state' => ['avatar' => '/images/asset/ulartangga/default_player_bot.png'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Bot player berhasil dibuat.',
                'bot_player_id' => $botPlayer->id
            ]);
        } catch (\Exception $e) {
            Log::error('Create Bot Player Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membuat bot player.'
            ], 500);
        }
    }

    // 1️⃣ Buat Match
    public function createMatch(Request $request)
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
            $player = UlartanggaPlayer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name
            ]);

            $active = UlartanggaMatch::where('host_id', $player->id)
                ->where('is_complete', false)
                ->latest()
                ->first();

            if ($active->status == 'finished') {
                $active->is_complete = true;
                $active->ended_at = now();
                $active->save();
            }
            if ($active->status !== 'finished' && $active->created_at > now()->subMinutes(60)) {
                return response()->json(['status' => 'error', 'message' => 'Selesaikan permainan aktif Anda terlebih dahulu.'], 400);
            }
            if ($active && $active->created_at <= now()->subMinutes(60)) {
                // Hapus match lama yang tidak aktif
                $active->status = 'finished';
                $active->is_complete = true;
                $active->ended_at = now();
                $active->save();
            }

            $request->validate([
                'name' => 'required|string',
                'max_players' => 'required|integer|min:2|max:6',
            ]);

            $match = UlartanggaMatch::create([
                'name' => $request->name,
                'host_id' => $player->id,
                'status' => 'waiting',
                'max_players' => $request->max_players,
                'current_turn_index' => null,
                'board_state' => null,
                'is_complete' => false,
                'started_at' => null,
                'ended_at' => null,
            ]);

            // Tambahkan host ke match
            UlartanggaMatchPlayer::create([
                'match_id' => $match->id,
                'player_id' => $player->id,
                'name' => $player->name,
                'is_host' => true,
                'joined_at' => now(),
                'player_state' => ['avatar' => '/images/asset/ulartangga/default_player.png'],
            ]);
            $matchDetail = $match->load(['matchPlayers' => function ($q) {
                $q->select('id', 'match_id', 'player_id', 'name', 'is_host', 'is_bot', 'is_ready', 'turn_order', 'position', 'is_turn');
            }]);
            return response()->json([
                'status' => 'success',
                'message' => 'Match berhasil dibuat',
                'player_id' => $player ? $player->id : null,
                'match_detail' => $matchDetail
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message ' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Create Match Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membuat match.'
            ], 500);
        }
    }

    // 2️⃣ Join Match
    public function joinMatch(Request $request)
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
            $player = UlartanggaPlayer::firstOrCreate(['user_id' => $user->id], [
                'name' => $user->name
            ]);

            $validated = $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
            ]);

            $match = UlartanggaMatch::where('id', $validated['match_id'])->firstOrFail();

            if ($match->status !== 'waiting') {
                return response()->json(['status' => 'error', 'message' => 'Match sudah dimulai atau selesai.'], 400);
            }

            $currentPlayers = $match->matchPlayers()->count();
            if ($currentPlayers >= $match->max_players) {
                return response()->json(['status' => 'error', 'message' => 'Match sudah penuh.'], 400);
            }

            $exists = UlartanggaMatchPlayer::where('match_id', $match->id)
                ->where('player_id', $player->id)
                ->exists();

            if (!$exists) {
                UlartanggaMatchPlayer::create([
                    'match_id' => $match->id,
                    'player_id' => $player->id,
                    'name' => $player->name,
                    'joined_at' => now(),
                    'player_state' => ['avatar' => '/images/asset/ulartangga/default_player.png'],
                ]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah bergabung di match ini.'], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil bergabung ke match.',
                'player_id' => $player ? $player->id : null,
                'match_detail' => $match->load('matchPlayers')
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message ' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Join Match Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat bergabung ke match.'
            ], 500);
        }
    }

    // 3️⃣ Host memulai permainan
    public function startMatch(Request $request)
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
            $player = UlartanggaPlayer::where('user_id', $user->id)->firstOrFail();

            $validated = $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
            ]);

            $match = UlartanggaMatch::with('matchPlayers')->findOrFail($validated['match_id']);

            if ($match->host_id !== $player->id) {
                return response()->json(['status' => 'error', 'message' => 'Hanya host yang bisa memulai permainan.'], 403);
            }
            if ($match->matchPlayers()->count() < 2) {
                return response()->json(['status' => 'error', 'message' => 'Dibutuhkan minimal 2 pemain untuk memulai permainan.'], 400);
            }
            if ($match->status !== 'waiting') {
                return response()->json(['status' => 'error', 'message' => 'Match sudah dimulai.'], 400);
            }
            $notReadyPlayers = $match->matchPlayers
                ->where('is_host', false)
                ->where('is_ready', false);

            $notReadyData = $notReadyPlayers->map(function ($p) {
                return [
                    'id' => $p->player_id,
                    'name' => $p->player->name ?? $p->name,
                    'is_ready' => $p->is_ready,
                ];
            });
            if ($notReadyPlayers->count() > 0) {
                $names = $notReadyPlayers
                    ->map(fn($p) => $p->player->user->name ?? 'Pemain #' . $p->name)
                    ->implode(', ');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak semua pemain siap. Pemain berikut belum ready: ' . $names,
                    'not_ready' => $notReadyData->values(),
                ], 400);
            }
            $players = $match->matchPlayers()->get();

            // Acak urutan pemain
            $shuffled = $players->shuffle();

            // Assign turn_order secara urut
            $turnOrder = 1;
            foreach ($shuffled as $mp) {
                $mp->turn_order = $turnOrder;
                $mp->is_turn = ($turnOrder === 1);
                $mp->is_ready = true;
                $mp->item_owned = ['perisai' => 1, 'teleport' => 1, 'dadu_ekstra' => 1];
                $mp->save();
                $turnOrder++;
            }
            // Ambil semua tile (1–100)
            $tiles = UlarTanggaTile::orderBy('number')->get();

            // Tentukan ultimate number antara 50–100
            $ultimateNumber = rand(50, 100);

            // Susun board state JSON
            $boardState = $tiles->map(function ($tile) use ($ultimateNumber) {
                return [
                    'id' => (string) $tile->id,
                    'number' => $tile->number,
                    'description' => $tile->description,
                    'effect' => $tile->effect_type,
                    'effect_target' => $tile->effect_target,
                    'reward_token' => $tile->number == $ultimateNumber ? 1000 : 100,
                ];
            });

            $match->update([
                'status' => 'playing',
                'current_turn_index' => 1,
                'board_state' => $boardState,
                'started_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Permainan dimulai!',
                'match_detail' => $match->load('matchPlayers'),
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message ' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Start Match Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memulai match.'
            ], 500);
        }
    }

    public function throwDice(Request $request)
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
            $player = UlartanggaPlayer::where('user_id', $user->id)->firstOrFail();
            $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
            ]);

            $matchId = $request->match_id;
            $match = UlartanggaMatch::with(['matchPlayers.player'])->findOrFail($matchId);
            if ($match->status !== 'playing') {
                return response()->json(['status' => 'error', 'message' => 'Match tidak dalam status bermain.'], 400);
            }

            // Ambil pemain yang sedang turn
            $currentPlayer = $match->matchPlayers()
                ->where('turn_order', $match->current_turn_index)
                ->first();

            Log::info("Current Turn Player: " . ($currentPlayer ? $currentPlayer->player_id : 'None'));
            if (!$currentPlayer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemain tidak ditemukan untuk giliran saat ini.',
                ], 404);
            }
            if ($currentPlayer->is_bot) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Giliran Bot untuk melempar dadu.',
                    'next_turn_is_bot' => $currentPlayer->is_bot ? $currentPlayer->is_bot : false,
                ], 403);
            }

            if ($currentPlayer->player_id !== $player->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bukan giliran Anda untuk melempar dadu.',
                ], 403);
            }

            // 1. Roll dadu (1–6)
            $dice = rand(1, 6);
            Log::info("Dice Rolled: " . $dice);
            // 2. Hitung posisi baru
            $newPosition = $currentPlayer->position + $dice;

            if ($newPosition > 100) {
                $newPosition = 100;
            }

            // Ambil board_state
            $boardState = collect($match->board_state);

            // Ambil tile
            $tile = $boardState->firstWhere('number', $newPosition);

            // Default
            $effect = null;
            $finalPosition = $newPosition;
            Log::info("Initial Final Position: " . $finalPosition);
            $rewardToken = $tile['reward_token'] ?? 100;
            Log::info("Reward Token on Tile: " . $rewardToken);
            // 3. Cek efek tile (snake / ladder)
            if ($tile && $tile['effect'] !== 'none') {
                $effect = $tile['effect']; // snake / ladder
                $finalPosition = (int) $tile['effect_target'];
            }

            // 4. Update posisi pemain
            $currentposition = $currentPlayer->position;
            $currentPlayer->position = $finalPosition;

            $currentPlayer->is_turn = false;
            $currentPlayer->last_roll_dice_number = $dice;
            $currentPlayer->total_dice_roll += 1;
            $currentPlayer->save();

            // 5. Kasih reward token
            $currentPlayer->increment('token_earned', $rewardToken);

            // 6. Simpan event log
            $logMessage = "Pemain {$currentPlayer->player->name} melempar dadu, berpindah ke posisi {$newPosition}.";

            if ($effect) {
                $logMessage .= " Efek **{$effect}** → pindah ke {$finalPosition}.";
            }

            $logMessage .= " Reward +{$rewardToken} MYNU.";

            UlartanggaEventLog::create([
                'match_id' => $match->id,
                'player_id' => $currentPlayer->player_id,
                'action' => 'roll_dice',
                'details' => $logMessage,
            ]);
            $matchPlayers = $match->matchPlayers()->get();
            // Jika sudah sampai 100 → menang
            if ($finalPosition == 100) {
                $currentPlayer->is_turn = false;
                $currentPlayer->save();

                $match->update([
                    'status' => 'finished',
                    'ended_at' => now(),
                ]);

                // Log event menang
                UlartanggaEventLog::create([
                    'match_id' => $match->id,
                    'action' => 'game_won',
                    'player_id' => $currentPlayer->player_id,
                    'details' => "Player **{$currentPlayer->player->name}** mencapai posisi 100 dan menang!",
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Permainan selesai! Anda pemenangannya.',
                    'dice' => $dice,
                    'final_position' => $finalPosition,
                    'match_players' => $matchPlayers,

                ]);
            }
            // 7. Lanjutkan turn ke player berikutnya
            $totalPlayers = $match->matchPlayers()->count();
            Log::info("Total Players in Match: " . $totalPlayers);

            $nextIndex = ($match->current_turn_index + 1) % $totalPlayers;
            Log::info("Calculated Next Index: " . $nextIndex);
            if ($nextIndex == 0) {
                $nextIndex = $totalPlayers;
            }
            Log::info("Final Next Index: " . $nextIndex);
            $match->update([
                'current_turn_index' => $nextIndex,
            ]);

            // Set next player turn
            $nextPlayer = $match->matchPlayers()
                ->where('turn_order', $nextIndex)
                ->first();

            if ($nextPlayer) {
                $nextPlayer->is_turn = true;
                $nextPlayer->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Dadu berhasil dilempar.',
                'player_id' => $currentPlayer->player_id,
                'player_name' => $currentPlayer->player->name,
                'dice' => $dice,
                'effect' => $effect,
                'from' => $currentposition,
                'to' => $finalPosition,
                'reward_token' => $rewardToken,
                'next_turn_index' => $nextIndex,
                'next_turn_is_bot' => $nextPlayer ? $nextPlayer->is_bot : false,
                'event_log' => $logMessage,
                'match_players' => $matchPlayers,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Throw Dice Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat melempar dadu.'
            ], 500);
        }
    }

    public function throwDiceForBot(Request $request)
    {
        try {
            $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
            ]);

            $matchId = $request->match_id;

            $match = UlartanggaMatch::with(['matchPlayers.player'])->findOrFail($matchId);
            if ($match->status !== 'playing') {
                return response()->json(['status' => 'error', 'message' => 'Match tidak dalam status bermain.'], 400);
            }

            // Ambil pemain bot yang sedang turn
            $currentPlayer = $match->matchPlayers()
                ->where('turn_order', $match->current_turn_index)
                ->where('is_bot', true)
                ->first();

            Log::info("Current Turn Bot Player: " . ($currentPlayer ? $currentPlayer->player_id : 'None'));
            if (!$currentPlayer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Belum giliran Bot saat ini.',
                ], 404);
            }

            if ($currentPlayer->player_id && $currentPlayer->is_bot === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bukan giliran bot untuk melempar dadu.',
                ], 403);
            }

            // 1. Roll dadu (1–6)
            $dice = rand(1, 6);
            Log::info("Bot Dice Rolled: " . $dice);

            // 2. Hitung posisi baru
            $newPosition = $currentPlayer->position + $dice;

            if ($newPosition > 100) {
                $newPosition = 100;
            }

            // Ambil board_state
            $boardState = collect($match->board_state);

            // Ambil tile
            $tile = $boardState->firstWhere('number', $newPosition);

            // Default
            $effect = null;
            $finalPosition = $newPosition;
            Log::info("Bot Initial Final Position: " . $finalPosition);
            $rewardToken = $tile['reward_token'] ?? 100;
            Log::info("Bot Reward Token on Tile: " . $rewardToken);

            // 3. Cek efek tile (snake / ladder)
            if ($tile && $tile['effect'] !== 'none') {
                $effect = $tile['effect']; // snake / ladder
                $finalPosition = (int) $tile['effect_target'];
            }

            // 4. Update posisi pemain bot
            $currentposition = $currentPlayer->position;
            $currentPlayer->position = $finalPosition;
            $matchPlayers = $match->matchPlayers()->get();

            // Jika sudah sampai 100 → menang
            if ($finalPosition == 100) {
                $currentPlayer->is_turn = false;
                $currentPlayer->save();

                $match->update([
                    'status' => 'finished',
                    'ended_at' => now(),
                ]);

                // Log event menang
                UlartanggaEventLog::create([
                    'match_id' => $match->id,
                    'player_id' => $currentPlayer->player_id,
                    'action' => 'game_won',
                    'details' => "Bot **{$currentPlayer->name}** mencapai tile 100 dan menang!",

                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Permainan selesai! Bot menang.',
                    'dice' => $dice,
                    'final_position' => $finalPosition,
                    'match_players' => $matchPlayers,
                ]);
            }

            $currentPlayer->is_turn = false;
            $currentPlayer->last_roll_dice_number = $dice;
            $currentPlayer->total_dice_roll += 1;
            $currentPlayer->save();

            // 5. Kasih reward token
            $currentPlayer->increment('token_earned', $rewardToken);

            // 6. Simpan event log
            $logMessage = "Bot {$currentPlayer->name} melempar dadu, berpindah ke posisi {$newPosition}.";

            if ($effect) {
                $logMessage .= " Efek **{$effect}** → pindah ke {$finalPosition}.";
            }

            $logMessage .= " Reward +{$rewardToken} MYNU.";

            UlartanggaEventLog::create([
                'match_id' => $match->id,
                'player_id' => $currentPlayer->player_id,
                'action' => 'roll_dice',
                'details' => $logMessage,
            ]);

            // 7. Lanjutkan turn ke player berikutnya
            $totalPlayers = $match->matchPlayers()->count();
            Log::info("Total Players in Match: " . $totalPlayers);

            $nextIndex = ($match->current_turn_index + 1) % $totalPlayers;
            Log::info("Calculated Next Index: " . $nextIndex);
            if ($nextIndex == 0) {
                $nextIndex = $totalPlayers;
            }
            Log::info("Final Next Index: " . $nextIndex);
            $match->update([
                'current_turn_index' => $nextIndex,
            ]);

            // Set next player turn
            $nextPlayer = $match->matchPlayers()
                ->where('turn_order', $nextIndex)
                ->first();

            if ($nextPlayer) {
                $nextPlayer->is_turn = true;
                $nextPlayer->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Dadu bot berhasil dilempar.',
                'player_id' => $currentPlayer->player_id,
                'player_name' => $currentPlayer->name,
                'dice' => $dice,
                'effect' => $effect,
                'from' => $currentposition,
                'to' => $finalPosition,
                'reward_token' => $rewardToken,
                'next_turn_index' => $nextIndex,
                'next_turn_is_bot' => $nextPlayer ? $nextPlayer->is_bot : false,
                'event_log' => $logMessage,
                'match_players' => $matchPlayers,

            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Throw Dice Bot Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat bot melempar dadu.'
            ], 500);
        }
    }

    public function checkActiveMatch(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }
            $player = UlartanggaPlayer::where('user_id', $user->id)->first();

            if (!$player) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemain tidak ditemukan.'
                ], 404);
            }

            // Cari match aktif di mana player masih tergabung
            $activeMatch = UlartanggaMatch::whereHas('matchPlayers', function ($q) use ($player) {
                $q->where('player_id', $player->id);
            })
                ->with(['matchPlayers' => function ($q) {
                    $q->select('id', 'match_id', 'player_id', 'name', 'is_host', 'is_bot', 'is_ready', 'turn_order', 'position', 'is_turn', 'player_state', 'item_owned');
                }])->whereIn('status', ['waiting', 'playing'])
                ->latest()
                ->first();
            if (!$activeMatch) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada match aktif.'
                ], 404);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Melanjutkan match aktif',
                'match_detail' => $activeMatch,
            ]);
        } catch (\Exception $e) {
            Log::error('Check Active Match Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memeriksa match aktif.'
            ], 500);
        }
    }

    public function ongoingMatch(Request $request)
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
            $player = UlartanggaPlayer::where('user_id', $user->id)->first();

            if (!$player) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemain tidak ditemukan.'
                ], 404);
            }
            $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
            ]);

            $matchId = $request->match_id;

            $match = UlartanggaMatch::select('id', 'name', 'host_id', 'status', 'max_players', 'current_turn_index', 'is_complete', 'started_at', 'ended_at')
                ->where('id', $matchId)
                ->with(['matchPlayers' => function ($q) {
                    $q->select('id', 'match_id', 'player_id', 'name', 'is_host', 'is_bot', 'is_ready', 'turn_order', 'position', 'is_turn', 'player_state', 'item_owned');
                }])->where('is_complete', false)
                ->first();

            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Tidak ada match yang sedang berlangsung untuk pemain ini.'], 404);
            }
            $lastPlayerstate = $match->matchPlayers->firstWhere('player_id', $player->id)->player_state ?? null;

            return response()->json([
                'status' => 'success',
                'message' => 'Permainan sedang berlangsung.',

                'match_detail' => $match,
                'match_players' => $match->matchPlayers,
            ]);
        } catch (\Exception $e) {
            Log::error('Ongoing Match Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data match.'
            ], 500);
        }
    }


    public function exitMatch(Request $request)
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
            $player = UlartanggaPlayer::where('user_id', $user->id)->firstOrFail();

            if (!$player) {
                return response()->json(['status' => 'error', 'message' => 'Pemain tidak ditemukan'], 404);
            }

            // Validasi input
            $validated = $request->validate([
                'match_id' => 'required|string|exists:ulartangga_matches,id',
            ]);

            $match = UlartanggaMatch::where('id', $validated['match_id'])->first();
            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Match tidak ditemukan'], 404);
            }

            // Cek apakah user adalah host / pembuat match
            $isHost = $match->host_id === $player->id;

            if ($isHost) {
                // Jika host keluar → match ditutup
                $match->update(['status' => 'cancelled', 'is_complete' => true]);

                // Hapus semua pemain di match
                UlartanggaMatchPlayer::where('match_id', $match->id)->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Match telah ditutup oleh host.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exit Match Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat keluar dari match.'
            ], 500);
        }

        // Hapus pemain dari match
        UlartanggaMatchPlayer::where('match_id', $match->id)
            ->where('player_id', $player->id)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Anda telah keluar dari match.',
        ]);
    }

    public function statistics()
    {
        $player = UlartanggaPlayer::where('user_id', request()->user()->id)
            ->first();

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Data pemain tidak ditemukan'], 404);
        }

        return response()->json([
            'status'        => 'success',
            'name'          => $player->name,
            'total_played'  => $player->total_played,
            'token_reward'  => $player->token_reward,
        ]);
    }
    public function leaderboard()
    {
        return response()->json(
            UlartanggaPlayer::select('name', 'total_played', 'token_reward')
                ->orderByDesc('total_played')
                ->limit(10)
                ->get()
        );
    }
}
