<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NgepetMatch;
use App\Models\NgepetMatchIntruder;
use App\Models\NgepetIntrudersChoice;
use App\Models\NgepetMatchGuess;
use App\Models\NgepetItem;
use App\Models\NgepetMatchItem;
use App\Models\NgepetUserAvatar;
use App\Models\User;
use App\Models\NgepetMatchHiddenItem;
use App\Models\NgepetMatchHiddenGuess;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class NgepetMatchController extends Controller
{
    public function create(Request $request)
    {
        try {
            // Validasi awal input
            $request->validate([
                'host_name' => 'required|string|max:50',
                'difficulty' => 'required|in:easy,medium,hard',
                'guess_duration_hours' => 'required|in:3,6,12,24',
                'max_intruders' => 'required|in:1,2,3,4,5',
                'token_pool' => 'required|numeric|min:100',
                'min_intruder_token' => 'nullable|integer|min:0',
                'max_intruder_token' => 'nullable|integer|min:100',
                'house_avatar_id' => 'nullable|exists:ngepet_avatars,id',
            ]);

            $user = $request->user();

            if ($request->filled('house_avatar_id')) {
                $hasAvatar = NgepetUserAvatar::where('user_id', $user->id)
                    ->where('avatar_id', $request->house_avatar_id)
                    ->whereHas('avatar', function ($q) {
                        $q->where('type', 'house');
                    })
                    ->exists();

                if (!$hasAvatar) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Avatar tidak valid atau kamu tidak memiliki avatar tersebut.'
                    ], 400);
                }
            }

            // Cek apakah sudah ada rumah aktif (status = 'open') dari user ini
            $activeMatch = NgepetMatch::where('host_user_id', $user->id)
                ->where('status', 'open')
                ->first();

            if ($activeMatch) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kamu sudah memiliki rumah yang aktif.'
                ], 400);
            }
            // Cek apakah user sedang menjadi babi
            $existingIntruder = NgepetMatchIntruder::where('user_id', $user->id)
                ->whereNotIn('status', ['end'])
                ->first();
            if ($existingIntruder) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kamu masih menjadi babi di rumah lain.'
                ], 400);
            }
            // Cek apakah user memiliki cukup token
            if ($request->token_pool > $user->total_token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token yang di input melebihi jumlah token yang kamu miliki.'
                ], 400);
            }

            // Tambahan: nama host tidak boleh sama untuk match yang masih aktif dari user yg sama
            $duplicateName = NgepetMatch::where('host_name', $request->host_name)
                ->where('status', 'open')
                ->exists();

            if ($duplicateName) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nama rumah ini sudah digunakan. Gunakan nama lain.'
                ], 400);
            }

            // Jika lolos semua, kurangi token user
            $user->total_token -= $request->token_pool;
            $user->save();

            // Buat match baru
            $match = NgepetMatch::create([
                'id' => Str::uuid(),
                'host_user_id' => $user->id,
                'host_name' => $request->host_name,
                'token_pool' => $request->token_pool,
                'total_token_pool' => $request->token_pool,
                'difficulty' => $request->difficulty,
                'guess_duration_hours' => $request->guess_duration_hours,
                'max_intruders' => $request->max_intruders,
                'status' => 'open',
                'min_intruder_token' => $request->min_intruder_token,
                'max_intruder_token' => $request->max_intruder_token,
                'house_avatar_id' => $request->house_avatar_id,
            ]);
            $items = NgepetItem::inRandomOrder()->limit(10)->get();

            foreach ($items as $item) {
                NgepetMatchItem::create([
                    'id' => Str::uuid(),
                    'match_id' => $match->id,
                    'item_id' => $item->id,
                    'name' => $item->name,
                    'image_url' => $item->image, // jika kamu menyimpan gambar
                ]);
            }
            $user->logTokenHistory(
                type: 'games',
                action: 'subtract',
                amount: $request->token_pool,
                description: 'Membuka rumah pada game "Ngepet Online" sebesar ' . $request->token_pool . ' token'
            );
            $user->logNgepetEvent(
                matchId: $match->id,
                role: 'host',
                action: 'create',
                details: 'Host membuka rumah'
                //    details: 'Host membuka rumah dengan nama ' . $request->host_name
            );
            $match->makeHidden(['host_user_id', 'created_at', 'updated_at']);
            $items->makeHidden(['id', 'created_at', 'updated_at']);
            optional($match->houseAvatar)->makeHidden(['id', 'type', 'price', 'stock', 'created_at', 'updated_at']);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil membuka rumah!',
                'id' => $match->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Create Match Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membuat match.'
            ], 500);
        }
    }

    public function storeHiddenItem(Request $request, $id)
    {
        try {
            $user = $request->user();
            //  Cek apakah match ditemukan
            $match = NgepetMatch::find($id);
            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Match tidak ditemukan'], 404);
            }
            // Validasi input item_name
            $validated = $request->validate([
                'item_name' => 'required|string',
            ]);
            // Validasi hanya host match yg boleh
            if ($match->host_user_id !== auth()->id()) {
                return response()->json(['status' => 'error', 'message' => 'Hanya host yang dapat menyembunyikan item'], 403);
            }
            // Hitung total hidden items untuk match ini
            $currentCount = NgepetMatchHiddenItem::where('match_id', $match->id)
                ->where('status', 'open')
                ->count();

            if ($currentCount >= 10) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kamu sudah banyak menyembunyikan token ke sebuah barang.'
                ], 400);
            }
            $validItems = $match->items;
            $selectedItem = $validItems->firstWhere('name', $request->item_name);

            if (!$selectedItem) {
                return response()->json(['status' => 'error', 'message' => 'barang tidak tersedia dalam match ini'], 422);
            }

            // Hitung berapa kali item ini sudah dipakai
            $itemCount = NgepetMatchHiddenItem::where('match_id', $match->id)
                ->where('item_id', $selectedItem->id)
                ->count();

            if ($itemCount >= 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'barang ini sudah terlalu banyak dipilih.'
                ], 400);
            }

            // Simpan item_id yang cocok
            $itemId = $selectedItem->id;
            $hiddenItem = NgepetMatchHiddenItem::create([
                'match_id' => $match->id,
                'user_id' => auth()->id(),
                'item_id' => $itemId,
                'status' => 'open'
            ]);

            $user->logNgepetEvent(
                matchId: $match->id,
                role: 'host',
                action: 'hidden-item',
                details: 'Host Menyembunyikan Token ke sebuah barang'
            );
            return response()->json([
                'status' => 'success',
                'message' => 'Token berhasil disembunyikan ke sebuah barang',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan internal', 'message' => $e->getMessage()], 500);
        }
    }

    public function makeGuess(Request $request, $id)
    {
        try {
            $user = $request->user();
            $request->validate([
                'match_intruder_id' => 'required|uuid',
                'item_name' => 'required|string',
            ]);
            $matchIntruderId = $request->input('match_intruder_id');

            $match = NgepetMatch::with([
                'intruders' => function ($q) use ($matchIntruderId) {
                    if ($matchIntruderId) {
                        $q->where('id', $matchIntruderId);
                    }
                }
            ])->findOrFail($id);
            if (!$match) {
                return response()->json(['status' => 'error', 'message' => 'Match tidak ditemukan'], 404);
            }
            if ($match->intruders->first()->user_id !== auth()->id()) {
                return response()->json(['status' => 'error', 'message' => 'Hanya intruders aktif yang bisa menebak'], 403);
            }
            if ($match->status === 'closed') {
                return response()->json(['status' => 'error', 'message' => 'Rumah sudah ditutup'], 403);
            }
            if ($match->intruders->first()->status !== 'wait') {
                return response()->json(['status' => 'error', 'message' => 'Intruder tidak valid untuk match ini'], 422);
            }
            $correctItem = null;

            $hiddenItemActive = NgepetMatchHiddenItem::where('match_id', $match->id)
                ->where('match_intruders_id', $request->match_intruder_id)
                ->where('status', 'open')
                ->first();

            if ($hiddenItemActive) {
                $hiddenItem = $hiddenItemActive;
            } else {
                $hiddenItem = NgepetMatchHiddenItem::where('match_id', $match->id)
                    ->where('id', $request->hidden_item_id)
                    ->where('status', 'open')
                    ->first();
            }
            if (!$hiddenItem) {
                return response()->json(['status' => 'error', 'message' => 'Token tersembunyi tidak ditemukan'], 404);
            }

            $intruder = $match->intruders->first();

            // Hitung tebakan sebelumnya
            $guessCount = NgepetMatchHiddenGuess::whereHas('matchIntruder', function ($q) use ($intruder) {
                $q->where('id', $intruder->id);
            })->where('user_id', auth()->id())->count();
            $maxGuesses = match ($match->difficulty) {
                'easy' => 5,
                'medium' => 4,
                'hard' => 3,
                default => 5,
            };
            if ($guessCount >= $maxGuesses) {
                return response()->json(['status' => 'error', 'message' => 'Kamu telah mencapai batas maksimal jumlah tebakan'], 422);
            }

            // Cek item yang tersedia di match
            $selectedItem = $match->items->firstWhere('name', $request->item_name);
            if (!$selectedItem) {
                return response()->json(['status' => 'error', 'message' => 'Item tidak tersedia di match ini'], 422);
            }

            $alreadyGuessed = NgepetMatchHiddenGuess::where('match_intruders_id', $request->match_intruder_id)
                ->where('user_id', auth()->id())
                ->where('item_id', $selectedItem->id)
                ->exists();

            if ($alreadyGuessed) {
                return response()->json(['error' => 'Sudah pernah menebak barang ini'], 422);
            }

            $correctItemId = $hiddenItem->item_id;
            $correctItemName = $match->items->firstWhere('id', $correctItemId)->name;
            $isCorrect = $correctItemId && $correctItemId === $selectedItem->id;
            $isEnd = false;

            // Simpan tebakan
            $guess = NgepetMatchHiddenGuess::create([
                'id' => Str::uuid(),
                'match_id' => $match->id,
                'user_id' => auth()->id(),
                'item_id' => $selectedItem->id,
                'match_intruders_id' => $request->match_intruder_id,
                'guess_turn' => $guessCount + 1,
                'is_correct' => $isCorrect,
                'guessed_at' => now(),
            ]);
            $hiddenItem->update(['match_intruders_id' => $request->match_intruder_id]);
            $host = $match->hostUser;
            // Jika tebakan benar
            if ($isCorrect) {
                $intruder->update([
                    'status' => 'end',
                    'result' => 'win',
                    'reward_token' => $intruder->token_pool,
                    'is_claimed_token' => '1',
                ]);
                $hiddenItem->update(['status' => 'closed', 'result' => 'lose']);

                $totalTokenForIntruder = $intruder->token_pool + $intruder->reward_token;
                // Berikan token ke user intruder
                $user->increment('total_token', $totalTokenForIntruder);


                $user->logTokenHistory(
                    type: 'games',
                    action: 'add',
                    amount: $totalTokenForIntruder,
                    description: 'Berhasil mencuri sebanyak ' . $totalTokenForIntruder . ' token dari game "Ngepet Online" '
                );
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'intruder',
                    action: 'guess',
                    details: 'Mencari token di barang ' . $request->item_name . '. → Mendapatkan token!'
                );
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'system',
                    action: 'guess',
                    details: 'Babi (' . $intruder->intruder_name . ') berhasil menemukan token dan kabur!',
                    createdAt: now()->addSeconds(2)
                );
                $match->decrement('token_pool', $intruder->token_pool);
                if ($match->token_pool <= 0) {
                    $match->update([
                        'status' => 'closed',
                        'closed_at' => now()
                    ]);
                    $user->logNgepetEvent(
                        matchId: $match->id,
                        role: 'system',
                        action: 'closed',
                        details: 'Menutup rumah karena token habis',
                        createdAt: now()->addSeconds(3)

                    );
                    if ($match->host_reward_token > 0) {
                        $host->increment('total_token', $match->host_reward_token);
                        $host->logTokenHistory(
                            type: 'games',
                            action: 'add',
                            amount: $match->host_reward_token,
                            description: 'Berhasil menebak babi dari game "Ngepet Online" dan mendapatkan sebanyak  ' . $match->host_reward_token . ' token'
                        );
                    }
                }
                $isEnd = true;
            }
            //jika tebakan salah
            if (!$isCorrect && ($guessCount + 1) < $maxGuesses) {
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'intruder',
                    action: 'guess',
                    details: 'Mencari token di barang ' . $request->item_name . '. → Tidak ada token!'
                );
            }
            // Jika salah dan sudah mencapai batas tebakan
            if (!$isCorrect && ($guessCount + 1) >= $maxGuesses) {
                $intruder->update([
                    'status' => 'end',
                    'result' => 'lose',
                ]);
                $hiddenItem->update(['status' => 'closed', 'result' => 'win']);

                // Berikan token ke host
                $match->increment('host_reward_token', $intruder->token_pool);

                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'intruder',
                    action: 'guess',
                    details: 'Mencari token di barang ' . $request->item_name . '. → Tidak ada token!'
                );
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'system',
                    action: 'guess',
                    details: 'Babi (' . $intruder->intruder_name . ') gagal menemukan token dan terbunuh oleh penjaga rumah!',
                    createdAt: now()->addSeconds(2)

                );
                $correctItem = $correctItemName;
                $isEnd = true;
            }

            return response()->json([
                'status' => 'guess_submitted',
                'is_correct' => $isCorrect,
                'is_end' => $isEnd,
                'answer_item' => $correctItem,
            ]);


            $result = NgepetMatchHiddenGuess::makeGuess(
                $validated['hidden_item_id'],
                $validated['intruder_match_id'],
                $validated['item_name'],
                $match->difficulty
            );

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Match tidak ditemukan'], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan internal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function join(Request $request, $id)
    {
        try {
            $match = NgepetMatch::with('intruders')->findOrFail($id);
            $user = $request->user();

            $hasOpenHouse = NgepetMatch::where('host_user_id', $user->id)
                ->where('status', '!=', 'closed')
                ->exists();

            if ($hasOpenHouse) {
                return response()->json(['error' => 'Kamu sedang membuka rumah.'], 403);
            }

            if ($match->host_user_id === $user->id) {
                return response()->json(['error' => 'Tidak bisa masuk rumah sendiri'], 403);
            }

            if ($match->status === 'closed') {
                return response()->json(['error' => 'Rumah sudah ditutup'], 403);
            }

            $activeIntrudersCount = $match->intruders->whereIn('status', ['join', 'wait'])->count();
            if ($activeIntrudersCount >= $match->max_intruders) {
                return response()->json(['error' => 'Match sudah penuh'], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'token_amount' => 'required|numeric|min:100',
                'avatar_id' => 'nullable|exists:ngepet_avatars,id',
            ]);

            if ($request->filled('avatar_id')) {
                $hasAvatar = NgepetUserAvatar::where('user_id', $user->id)
                    ->where('avatar_id', $request->avatar_id)
                    ->whereHas('avatar', function ($q) {
                        $q->where('type', 'player');
                    })
                    ->exists();

                if (!$hasAvatar) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Avatar tidak valid atau kamu tidak memiliki avatar tersebut.',

                    ], 400);
                }
            }
            $name_intruders = $validated['name'];
            $tokenAmount = $validated['token_amount'];

            if ($user->total_token < $tokenAmount) {
                return response()->json(['error' => 'Token tidak cukup'], 403);
            }

            if ($match->token_pool <= 0) {
                return response()->json(['error' => 'Token dirumah ini sudah habis'], 403);
            }

            $existingIntruder = NgepetMatchIntruder::where('user_id', $user->id)
                ->whereNotIn('status', ['end'])
                ->first();
            if ($existingIntruder) {
                return response()->json(['error' => 'Kamu masih menjadi babi di rumah lain'], 403);
            }

            $totalIntruderPayout = $match->intruders
                ->whereIn('status', ['join', 'wait'])
                ->sum('token_pool');

            $calculatedTotalPayout = $totalIntruderPayout + $tokenAmount;

            if ($calculatedTotalPayout > $match->token_pool) {
                return response()->json(['error' => 'Token dirumah ini tidak cukup jika semua babi menang.'], 403);
            }

            if ($tokenAmount < $match->min_intruder_token) {
                return response()->json(['error' => "Token minimal adalah {$match->min_intruder_token}"], 400);
            }

            if ($match->max_intruder_token > 0 && $tokenAmount > $match->max_intruder_token) {
                return response()->json(['error' => "Token maksimal adalah {$match->max_intruder_token}"], 400);
            }

            $intruder = NgepetMatchIntruder::create([
                'id' => Str::uuid(),
                'match_id' => $match->id,
                'user_id' => $user->id,
                'intruder_name' => $name_intruders,
                'avatar_id' => $request->avatar_id,
                'token_pool' => $tokenAmount,
                'intruders_at' => now(),
                'guess_deadline' => now()->addHours($match->guess_duration_hours),
            ]);

            $user->decrement('total_token', $tokenAmount);
            $user->logTokenHistory(
                type: 'games',
                action: 'subtract',
                amount: $tokenAmount,
                description: 'Bermain game "Ngepet Online" sebesar ' . $tokenAmount . ' token'
            );
            $user->logNgepetEvent(
                matchId: $match->id,
                role: 'intruder',
                action: 'join',
                details: 'Seekor Babi (' . $name_intruders . ') Telah masuk ke rumah dan akan mencuri ' . $tokenAmount . ' token'
            );
            $intruder->makeHidden(['user_id', 'created_at', 'updated_at']);
            return response()->json(['success' => 'Berhasil masuk kedalam rumah']);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Match tidak ditemukan'], 404);
        } catch (\Throwable $e) {
            Log::error('Join Match Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan. Coba lagi nanti.'], 500);
        }
    }

    public function submitChoice(Request $request, $id)
    {
        try {
            $user = $request->user();
            // 1. Cek apakah match ditemukan
            $match = NgepetMatch::find($id);
            if (!$match) {
                return response()->json(['error' => 'Match tidak ditemukan'], 404);
            }

            // 2. Ambil intruder berdasarkan user login
            $intruder = NgepetMatchIntruder::where('user_id', auth()->id())
                ->where('match_id', $id)
                ->whereIn('status', ['join', 'wait'])
                ->latest('created_at')
                ->first();

            if (!$intruder) {
                return response()->json(['error' => 'Kamu bukan bagian dari match ini'], 403);
            }

            // 3. Validasi status intruder harus "join"
            if ($intruder->status !== 'join') {
                return response()->json(['error' => 'Menunggu pemilik rumah menebak.'], 403);
            }

            // 4. Validasi apakah sudah pernah memilih item
            if ($intruder->is_pick_choice) {
                return response()->json(['error' => 'Kamu sudah memilih item sebelumnya'], 422);
            }
            if (!$intruder->is_pick_choice && now()->greaterThan($intruder->created_at->addMinutes(60))) {
                $intruder->update(['status' => 'end', 'result' => 'lose']);
                $match->increment('host_reward_token', $intruder->token_pool);
                $intruder->user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'system',
                    action: 'idle',
                    details: 'Babi (' . $intruder->intruder_name . ') tidak memilih barang dan kehilangan ' . $intruder->token_pool . ' token'
                );
                return response()->json(['success' => 'Kamu kalah karena tidak memilih barang dalam waktu yang di tentukan'], 403);
            }

            // 5. Validasi input item_name
            $request->validate([
                'item_name' => 'required|string',
            ]);

            // Cek apakah user sudah memilih item primary di match ini
            $existingChoice = NgepetIntrudersChoice::where('match_intruders_id', $match->id)
                ->where('user_id', auth()->id())
                ->where('type_choice', 'primary')
                ->exists();

            if ($existingChoice) {
                return response()->json(['error' => 'Kamu sudah memilih barang utama untuk match ini.'], 422);
            }

            // 6. Validasi apakah item_name tersedia di match
            $validItems = $match->items; // Asumsi relasi: $match->items()
            $selectedItem = $validItems->firstWhere('name', $request->item_name);

            if (!$selectedItem) {
                return response()->json(['error' => 'barang tidak tersedia dalam match ini'], 422);
            }

            // Simpan item_id yang cocok
            $itemId = $selectedItem->id;

            // 7. Simpan pilihan
            $intruder->update([
                'is_pick_choice' => 1,
                'status' => 'wait'
            ]);

            NgepetIntrudersChoice::create([
                'id' => Str::uuid(),
                'match_intruders_id' => $intruder->id,
                'user_id' => auth()->id(),
                'item_id' => $itemId,
            ]);
            $user->logNgepetEvent(
                matchId: $match->id,
                role: 'intruder',
                action: 'submit-choice',
                details: 'Babi (' . $intruder->intruder_name . ') Telah bersembunyi ke sebuah barang'
            );
            return response()->json(['success' => 'Barang telah dipilih']);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan internal', 'message' => $e->getMessage()], 500);
        }
    }

    public function guess(Request $request, $id)
    {
        try {
            $user = $request->user();
            $match = NgepetMatch::findOrFail($id);
            $correctItem = null;
            if ($match->host_user_id !== auth()->id()) {
                return response()->json(['error' => 'Hanya host yang bisa menebak'], 403);
            }
            if ($match->status === 'closed') {
                return response()->json(['error' => 'Rumah sudah ditutup'], 403);
            }

            $request->validate([
                'match_intruder_id' => 'required|uuid',
                'item_name' => 'required|string',
            ]);

            $intruder = NgepetMatchIntruder::with(['choices', 'avatar'])
                ->where('status', 'wait')
                ->where('id', $request->match_intruder_id)
                ->firstOrFail();
            if ($intruder->match_id !== $match->id) {
                return response()->json(['error' => 'Intruder tidak valid untuk match ini'], 422);
            }
            if ($intruder->guess_deadline && now()->greaterThan($intruder->guess_deadline)) {
                // Tandai intruder menang
                $intruder->update([
                    'status' => 'end',
                    'result' => 'win',
                    'guessed_by_host' => '0',
                    'reward_token' => $intruder->token_pool,
                    'is_claimed_token' => '1',
                ]);

                // Berikan token ke intruder (token_pool + reward_token)
                $totalTokenForIntruder = $intruder->token_pool + $intruder->reward_token;
                User::where('id', $intruder->user_id)->increment('total_token', $totalTokenForIntruder);

                // Log token intruder
                $intruder->user->logTokenHistory(
                    type: 'games',
                    action: 'add',
                    amount: $totalTokenForIntruder,
                    description: 'Berhasil mencuri sebanyak ' . $totalTokenForIntruder . ' token dari game "Ngepet Online" '
                );

                // Event log
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'system',
                    action: 'guess',
                    details: 'Babi (' . $intruder->intruder_name . ') berhasil mencuri ' . $intruder->token_pool . ' token karena waktu habis'
                );

                // Kurangi pool & tutup rumah jika habis
                $match->decrement('token_pool', $intruder->token_pool);
                if ($match->token_pool <= 0) {
                    $match->update(['status' => 'closed', 'closed_at' => now()]);
                    $user->logNgepetEvent(
                        matchId: $match->id,
                        role: 'system',
                        action: 'closed',
                        details: 'Menutup rumah karena token habis'
                    );
                }

                // Jika host punya reward, cairkan
                if ($match->host_reward_token > 0) {
                    $user->increment('total_token', $match->host_reward_token);
                    $user->logTokenHistory(
                        type: 'games',
                        action: 'add',
                        amount: $match->host_reward_token,
                        description: 'Berhasil menebak babi dari game "Ngepet Online" dan mendapatkan sebanyak  ' . $match->host_reward_token . ' token'
                    );
                }

                return response()->json([
                    'error' => 'Waktu menebak sudah habis. Babi berhasil mencuri token.'
                ], 422);
            }
            // Ambil pilihan berdasarkan match_id dan user_id
            $choices = NgepetIntrudersChoice::where('match_intruders_id', $intruder->id)
                ->where('user_id', $intruder->user_id)
                ->get();

            // Hitung tebakan sebelumnya
            $guessCount = NgepetMatchGuess::whereHas('matchIntruder', function ($q) use ($intruder) {
                $q->where('id', $intruder->id);
            })->where('user_id', auth()->id())->count();

            $maxGuesses = match ($match->difficulty) {
                'easy' => 5,
                'medium' => 4,
                'hard' => 3,
                default => 5,
            };

            if ($guessCount >= $maxGuesses) {
                return response()->json(['error' => 'Kamu telah mencapai batas maksimal jumlah tebakan'], 422);
            }

            // Cek item yang tersedia di match
            $selectedItem = $match->items->firstWhere('name', $request->item_name);
            if (!$selectedItem) {
                return response()->json(['error' => 'Item tidak tersedia di match ini'], 422);
            }

            $alreadyGuessed = NgepetMatchGuess::where('match_intruder_id', $request->match_intruder_id)
                ->where('user_id', auth()->id())
                ->where('item_id', $selectedItem->id)
                ->exists();

            if ($alreadyGuessed) {
                return response()->json(['error' => 'Sudah pernah menebak barang ini'], 422);
            }

            $correctItemId = optional($intruder->choices()->where('type_choice', 'primary')->first())->item_id;
            $correctItemName = $match->items->firstWhere('id', $correctItemId)->name;
            $isCorrect = $correctItemId && $correctItemId === $selectedItem->id;
            $isEnd = false;

            // Simpan tebakan
            $guess = NgepetMatchGuess::create([
                'id' => Str::uuid(),
                'match_intruder_id' => $request->match_intruder_id,
                'user_id' => auth()->id(),
                'item_id' => $selectedItem->id,
                'guess_turn' => $guessCount + 1,
                'is_correct' => $isCorrect,
                'guessed_at' => now(),
            ]);
            //jika tebakan salah
            if (!$isCorrect && ($guessCount + 1) < $maxGuesses) {
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'host',
                    action: 'guess',
                    details: 'Menebak barang ' . $request->item_name . '. → Salah'
                );
            }
            // Jika tebakan benar
            if ($isCorrect) {
                $intruder->update([
                    'status' => 'end',
                    'result' => 'lose',
                    'guessed_by_host' => '1',
                ]);
                $match->increment('host_reward_token', $intruder->token_pool);
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'host',
                    action: 'guess',
                    details: 'Menebak barang ' . $request->item_name . '. → Benar'
                );
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'system',
                    action: 'guess',
                    details: 'Babi (' . $intruder->intruder_name . ') terbunuh dan ia kehilangan ' . $intruder->token_pool . ' token',
                    createdAt: now()->addSeconds(2)
                );
                $isEnd = true;
            }

            // Jika salah dan sudah mencapai batas tebakan
            if (!$isCorrect && ($guessCount + 1) >= $maxGuesses) {
                $intruder->update([
                    'status' => 'end',
                    'result' => 'win',
                    'guessed_by_host' => '1',
                    'reward_token' => $intruder->token_pool,
                    'is_claimed_token' => '1',
                ]);
                $totalTokenForIntruder = $intruder->token_pool + $intruder->reward_token;
                // Berikan token ke user intruder
                User::where('id', $intruder->user_id)
                    ->increment('total_token', $totalTokenForIntruder);
                $intruder->user->logTokenHistory(
                    type: 'games',
                    action: 'add',
                    amount: $totalTokenForIntruder,
                    description: 'Berhasil mencuri sebanyak ' . $totalTokenForIntruder . ' token dari game "Ngepet Online" '
                );
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'host',
                    action: 'guess',
                    details: 'Menebak barang ' . $request->item_name . '. → Salah'
                );
                $user->logNgepetEvent(
                    matchId: $match->id,
                    role: 'system',
                    action: 'guess',
                    details: 'Babi (' . $intruder->intruder_name . ') berhasil mencuri ' . $intruder->token_pool . ' token dan kabur',
                    createdAt: now()->addSeconds(2)

                );

                $match->decrement('token_pool', $intruder->token_pool);
                if ($match->token_pool <= 0) {
                    $match->update([
                        'status' => 'closed',
                        'closed_at' => now()
                    ]);
                    $user->logNgepetEvent(
                        matchId: $match->id,
                        role: 'system',
                        action: 'closed',
                        details: 'Menutup rumah karena token habis',
                        createdAt: now()->addSeconds(3)

                    );
                }
                if ($match->host_reward_token > 0) {
                    $user->increment('total_token', $match->host_reward_token);
                    $user->logTokenHistory(
                        type: 'games',
                        action: 'add',
                        amount: $match->host_reward_token,
                        description: 'Berhasil menebak babi dari game "Ngepet Online" dan mendapatkan sebanyak  ' . $match->host_reward_token . ' token'
                    );
                }
                $correctItem = $correctItemName;
                $isEnd = true;
            }

            return response()->json([
                'status' => 'guess_submitted',
                'is_correct' => $isCorrect,
                'is_end' => $isEnd,
                'answer_item' => $correctItem,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Match tidak ditemukan'], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan internal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function claimIntruderVictory(Request $request)
    {
        try {
            $request->validate([
                'match_intruder_id' => 'required|uuid|exists:ngepet_match_intruders,id',
            ]);

            $user = $request->user();

            $intruder = NgepetMatchIntruder::with(['match'])
                ->where('id', $request->match_intruder_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if (!$intruder) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda bukan intruder pada match ini.'
                ], 403);
            }

            if ($intruder->result !== 'draw') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hasil match Anda sudah ditentukan.'
                ], 400);
            }
            $makeguess = NgepetMatchHiddenItem::where('match_id', $intruder->match_id)
                ->where('match_intruders_id', $request->match_intruder_id)
                ->where('status', 'open')
                ->first();

            // Pastikan deadline sudah lewat
            if ($intruder->guess_deadline && now()->lessThan($intruder->guess_deadline)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Waktu klaim kemenangan belum tiba.'
                ], 400);
            }
            $match = $intruder->match;
            $host = $match->hostUser;
            if ($intruder->guess_deadline && now()->greaterThan($intruder->guess_deadline)) {
                if ($makeguess) {
                    $makeguess->update(['status' => 'closed', 'result' => 'win']);
                    $intruder->update([
                        'status' => 'end',
                        'result' => 'lose',
                    ]);
                    $match->increment('host_reward_token', $intruder->token_pool);
                    $user->logNgepetEvent(
                        matchId: $match->id,
                        role: 'system',
                        action: 'guess',
                        details: 'Babi (' . $intruder->intruder_name . ') gagal menemukan token dan terbunuh oleh penjaga rumah!',
                        createdAt: now()->addSeconds(1)
                    );
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda kalah karena tidak menemukan token sebelum waktu habis.'
                    ]);
                }
                // Tandai intruder menang
                $intruder->update([
                    'status' => 'end',
                    'result' => 'win',
                    'reward_token' => $intruder->token_pool,
                    'is_claimed_token' => '1',
                ]);

                // Berikan token ke intruder (token_pool + reward_token)
                $totalTokenForIntruder = $intruder->token_pool + $intruder->reward_token;
                $user->increment('total_token', $totalTokenForIntruder);

                // Log token intruder
                $user->logTokenHistory(
                    type: 'games',
                    action: 'add',
                    amount: $totalTokenForIntruder,
                    description: 'Berhasil mencuri sebanyak ' . $totalTokenForIntruder . ' token dari game "Ngepet Online" ',
                );

                // Event log
                $user->logNgepetEvent(
                    matchId: $intruder->match_id,
                    role: 'system',
                    action: 'claim',
                    details: 'Babi (' . $intruder->intruder_name . ') berhasil mencuri ' . $intruder->token_pool . ' token karena host tidak menebak',
                    createdAt: now()->addSeconds(2)
                );

                // Kurangi pool & tutup rumah jika habis
                $match->decrement('token_pool', $intruder->token_pool);
                if ($match->token_pool <= 0) {
                    $match->update(['status' => 'closed', 'closed_at' => now()]);
                    $user->logNgepetEvent(
                        matchId: $intruder->match->id,
                        role: 'system',
                        action: 'closed',
                        details: 'Menutup rumah karena token habis',
                        createdAt: now()->addSeconds(3)
                    );
                }

                // Jika host punya reward, cairkan
                if ($match->host_reward_token > 0) {
                    $host->increment('total_token', $match->host_reward_token);
                    $host->logTokenHistory(
                        type: 'games',
                        action: 'add',
                        amount: $match->host_reward_token,
                        description: 'Berhasil menebak babi dari game "Ngepet Online" dan mendapatkan sebanyak  ' . $intruder->match->host_reward_token . ' token'
                    );
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Kemenangan berhasil diklaim.'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Match tidak ditemukan'], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan internal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function close(Request $request, $id)
    {
        try {
            $match = NgepetMatch::with(['intruders'])->findOrFail($id);
            $user = $request->user();

            // Validasi bahwa hanya host yang bisa menutup
            if ($match->host_user_id !== auth()->id()) {
                return response()->json(['error' => 'Hanya host yang bisa menutup match'], 403);
            }

            // Cek jika sudah ditutup
            if ($match->status === 'closed') {
                return response()->json(['error' => 'Match sudah ditutup'], 422);
            }

            // Validasi intruders aktif
            $hasWaitingIntruders = $match->intruders->contains(fn($intruder) => $intruder->status === 'wait');
            if ($hasWaitingIntruders) {
                return response()->json([
                    'error' => 'Tidak bisa menutup match, masih ada intruders yang sudah memilih barang'
                ], 422);
            }

            $intrudersJoin = $match->intruders->where('status', 'join');
            foreach ($intrudersJoin as $intruder) {
                if ($intruder->token_pool > 0) {
                    $intruder->update([
                        'status' => 'end',
                        'is_claimed_token' => 1
                    ]);
                    $intruder->user->increment('total_token', $intruder->token_pool);
                    $intruder->user->logTokenHistory(
                        type: 'games',
                        action: 'add',
                        amount: $intruder->token_pool,
                        description: 'Mengembalikan token dari game "Ngepet Online" karena match ditutup sebelum memilih barang'
                    );
                }
            }

            $match->status = 'closed';
            $match->closed_at = now();

            $totalearntoken = $match->token_pool + $match->host_reward_token;

            $match->save();
            $user->increment('total_token', $totalearntoken);
            $user->logTokenHistory(
                type: 'games',
                action: 'add',
                amount: $totalearntoken,
                description: 'Menutup rumah pada game "Ngepet Online" dan mendapatkan sebanyak ' . $totalearntoken . ' token'
            );
            $user->logNgepetEvent(
                matchId: $match->id,
                role: 'host',
                action: 'closed',
                details: 'Menutup rumah'
            );
            return response()->json([
                'status' => 'match_closed',
                'message' => 'Anda mengumpulkan token sebanyak ' . $totalearntoken,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Match tidak ditemukan'], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan internal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $match = NgepetMatch::with([
                'items' => function ($q) {
                    $q->select('id', 'match_id', 'name', 'image_url');
                },
                'intruders' => function ($q) {
                    $q->select('id', 'match_id', 'user_id', 'intruder_name', 'avatar_id', 'status', 'intruders_at', 'guess_deadline', 'is_pick_choice', 'result', 'token_pool', 'created_at')
                        ->with(['avatar:id,name,image_url']);
                },
                'houseAvatar' => function ($q) {
                    $q->select('id', 'name', 'image_url', 'tier');
                },
                'hiddenItems' => function ($q) {
                    $q->select('id', 'match_id', 'status', 'result')
                        ->where('status', 'open');
                },
                'events' => function ($q) {
                    $q->select('id', 'match_id', 'role', 'details', 'created_at');
                }
            ])->withCount([
                'intruders as intruders_count' => function ($q) {
                    $q->where('status', '!=', 'end');
                }
            ])
                ->withCount([
                    'hiddenItems as hidden_tokens_count' => function ($q) {
                        $q->where('status', '=', 'open');
                    }
                ])
                ->findOrFail($id)
                ->makeHidden(['host_user_id', 'created_at', 'updated_at']);
            $match->items->makeHidden(['id', 'match_id']);
            $match->intruders->makeHidden(['match_id', 'user_id']);
            optional($match->houseAvatar)->makeHidden(['id']);
            optional($match->events)->makeHidden(['id', 'match_id']);

            $intruders = $match->intruders;
            foreach ($intruders as $intruder) {
                if ($intruder && $intruder->status === 'join' && !$intruder->is_pick_choice && now()->greaterThan($intruder->guess_deadline)) {
                    $intruder->update(['status' => 'end', 'result' => 'lose']);
                    $match->increment('host_reward_token', $intruder->token_pool);
                    $intruder->user->logNgepetEvent(
                        matchId: $match->id,
                        role: 'system',
                        action: 'idle',
                        details: 'Babi (' . $intruder->intruder_name . ') tidak memilih barang dan kehilangan ' . $intruder->token_pool . ' token'
                    );
                }
            }
            // return response()->json($match);
            return response()->json([
                'match' => $match
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Match tidak ditemukan'], 404);
        } catch (\Throwable $e) {
            Log::error('Show Match Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan. Coba lagi nanti.'], 500);
        }
    }


    public function listOpenMatches(): JsonResponse
    {
        $matches = NgepetMatch::select('id', 'host_name', 'house_avatar_id', 'token_pool', 'total_token_pool', 'min_intruder_token', 'max_intruder_token', 'difficulty', 'guess_duration_hours', 'max_intruders', 'status', 'created_at')
            ->where('status', 'open')
            ->withCount([
                'intruders as intruders_count' => function ($q) {
                    $q->where('status', '!=', 'end');
                }
            ])
            ->with([
                'houseAvatar:id,name,image_url,tier'
            ])
            ->orderByDesc('house_avatar_id')
            ->get();

        return response()->json([
            'status' => 'success',
            'matches' => $matches
        ]);
    }

    public function myActiveMatch(Request $request)
    {
        $userId = auth()->id();
        $user = $request->user();

        // Cari match aktif sebagai host
        $hostMatch = DB::table('ngepet_matches as m')
            ->select(
                'm.id as match_id',
                DB::raw("'host' as role"),
                'm.status',
                'm.token_pool',
                'm.host_name',
                DB::raw("(SELECT COUNT(*) FROM ngepet_match_intruders WHERE match_id = m.id and status != 'end') as intruders_count"),
                'm.max_intruders'
            )
            ->where('m.host_user_id', $userId)
            ->whereIn('m.status', ['open'])
            ->first();

        // Cari match aktif sebagai intruder
        $intruderMatch = DB::table('ngepet_match_intruders as i')
            ->join('ngepet_matches as m', 'm.id', '=', 'i.match_id')
            ->select(
                'm.id as match_id',
                DB::raw("'intruder' as role"),
                'm.status',
                'm.token_pool',
                'm.host_name',
                DB::raw("(SELECT COUNT(*) FROM ngepet_match_intruders WHERE match_id = m.id and status != 'end') as intruders_count"),
                'm.max_intruders',
                'i.id as intruder_match_id'
            )
            ->where('i.user_id', $userId)
            ->whereIn('i.status', ['wait', 'join'])
            ->first();

        $activeMatch = $hostMatch ?? $intruderMatch;

        if (!$activeMatch) {
            return response()->json([
                'status' => 'success',
                'data' => null,
                'token' => number_format($user->total_token, 0, ',', '')

            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activeMatch,
            'token' => number_format($user->total_token, 0, ',', '')
        ]);
    }
    public function historyMatches(Request $request)
    {
        $userId = auth()->id();

        $matches = DB::table('ngepet_matches as m')
            ->leftJoin('ngepet_match_intruders as i', function ($join) use ($userId) {
                $join->on('i.match_id', '=', 'm.id')
                    ->where('i.user_id', '=', $userId);
            })
            ->select(
                'm.id as match_id',
                'm.host_name',
                'm.status',
                DB::raw("
            CASE 
                WHEN m.host_user_id = {$userId} THEN 'host'
                WHEN i.user_id IS NOT NULL THEN 'intruder'
                ELSE 'spectator'
            END as role
        "),
                DB::raw("
            CASE 
                WHEN m.host_user_id = {$userId} THEN
                    CASE
                        WHEN (m.token_pool + m.host_reward_token) > m.total_token_pool THEN 'win'
                        ELSE 'lose'
                    END
                WHEN i.user_id = {$userId} AND i.result = 'win' THEN 'win'
                WHEN i.user_id = {$userId} AND i.result = 'lose' THEN 'lose'
                ELSE NULL
            END as match_result
        "),
                DB::raw("
            CASE 
                WHEN m.host_user_id = {$userId} THEN m.created_at
                WHEN i.user_id = {$userId} THEN i.created_at
                ELSE m.created_at
            END as created_at
        ")
            )
            ->where(function ($q) use ($userId) {
                $q->where(function ($q2) use ($userId) {
                    $q2->where('m.host_user_id', $userId)
                        ->where('m.status', 'closed');
                })
                    ->orWhere(function ($q2) use ($userId) {
                        $q2->where('i.user_id', $userId)
                            ->where('i.status', 'end');
                    });
            })
            ->orderByDesc(DB::raw("
        CASE 
            WHEN m.host_user_id = {$userId} THEN m.created_at
            WHEN i.user_id = {$userId} THEN i.created_at
            ELSE m.created_at
        END
    "))
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $matches
        ]);
    }

    public function leaderboardTopHouses()
    {
        $topHouses = NgepetMatch::select('id', 'host_user_id', 'host_name', 'total_token_pool', 'house_avatar_id')
            ->where('status', 'closed')
            ->orderByDesc('total_token_pool')
            ->limit(10)
            ->with([
                'houseAvatar:id,name,image_url,tier'
            ])
            ->get()
            ->map(function ($house) {
                return [
                    'match_id'   => $house->id,
                    'host_name'  => $house->host_name,
                    'avatar_id' => $house->house_avatar_id,
                    'avatar' => $house->houseAvatar,
                    'token_pool' => $house->total_token_pool,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $topHouses
        ]);
    }
    public function leaderboardHostWinrate()
    {
        $hosts = DB::table('ngepet_match_intruders as i')
            ->join('ngepet_matches as m', 'm.id', '=', 'i.match_id')
            ->join('users as u', 'u.id', '=', 'm.host_user_id')
            ->select(
                'm.host_user_id',
                'u.name as host_name',
                DB::raw('COUNT(i.id) as total_intruder_games'),
                DB::raw("SUM(CASE WHEN i.result = 'lose' THEN 1 ELSE 0 END) as total_wins"),
                DB::raw("ROUND(SUM(CASE WHEN i.result = 'lose' THEN 1 ELSE 0 END) / COUNT(i.id) * 100, 2) as winrate_percentage")
            )
            ->where('m.status', 'closed')
            ->groupBy('m.host_user_id', 'u.name')
            ->orderByDesc('winrate_percentage')
            ->limit(10)
            ->get();



        return response()->json([
            'status' => 'success',
            'data' => $hosts
        ]);
    }


    public function leaderboardIntruderWinrate()
    {
        $leaderboard = DB::table('ngepet_match_intruders as i')
            ->join('ngepet_matches as m', 'm.id', '=', 'i.match_id')
            ->join('users as u', 'u.id', '=', 'i.user_id')
            ->select(
                'u.name as intruder_name',
                DB::raw('COUNT(i.id) as total_games'),
                DB::raw("SUM(CASE WHEN i.result = 'win' THEN 1 ELSE 0 END) as total_wins"),
                DB::raw("ROUND(SUM(CASE WHEN i.result = 'win' THEN 1 ELSE 0 END) / COUNT(i.id) * 100, 2) as win_rate")
            )
            ->where('m.status', 'closed')
            ->groupBy('i.user_id', 'u.name')
            ->havingRaw('COUNT(i.id) >= 10')
            ->orderByDesc('win_rate')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $leaderboard
        ]);
    }


    #tutup fungsi controller    
}
