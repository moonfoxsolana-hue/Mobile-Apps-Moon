<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\TarotCard;
use App\Models\TarotUserStat;
use App\Models\TarotUserReading;
use App\Services\TarotAIService;
use Carbon\Carbon;

class TarotRitualController extends Controller
{

    public function Start(Request $request)
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

            $userStat = TarotUserStat::firstOrCreate(
                ['user_id' => $user->id],
                ['name' => $user->name]
            );

            $today = Carbon::today();
            $session = TarotUserReading::firstOrCreate(
                ['user_id' => $user->id, 'is_complete' => false],
                [
                    'energy_choice' => null,
                    'cards_drawn' => [],
                    'reading_date' => null,
                    'ai_prediction' => null,
                ]
            );


            $randomCards = TarotCard::inRandomOrder()->take(10)->get(['id']); // ambil id/uuid saja

            $cardsWithOrientation = $randomCards->map(function ($card) {
                $orientation = rand(1, 100) <= 60 ? 'upright' : 'reversed';
                return [
                    'id' => $card->id,
                    'orientation' => $orientation
                ];
            });

            if ($session->ai_prediction == null) {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Melanjutkan Ritual sebelumnya, pilih kartu takdirmu hari ini...',
                        'session_id' => $session->id,
                        'cards' => $cardsWithOrientation
                    ]
                );
            }
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Ritual dimulai, pilih kartu takdirmu hari ini...',
                    'session_id' => $session->id,
                    'cards' => $cardsWithOrientation
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memulai permainan.'
            ], 500);
        }
    }


    public function pickCards(Request $request)
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
            $request->validate([
                'session_id' => 'required|uuid',
                'name' => 'required|string|max:50',
                'energy_choice' => 'required|string',
                'cards' => 'required|array|min:1|max:3',
                'cards.*.id' => 'required|uuid',
                'cards.*.orientation' => 'required|in:upright,reversed'
            ]);


            $session = TarotUserReading::where('id', $request->session_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi ritual tidak ditemukan.'
                ], 404);
            }
            // Ambil kartu random dari tarot_cards
            $cardIds = collect($request->cards)->pluck('id');
            $cards = TarotCard::whereIn('id', $cardIds)->get();

            if (!$cards->count()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kartu tidak ditemukan.'
                ], 404);
            }

            // Format dengan orientasi dari FE
            $cardsFormatted = $cards->map(function ($card) use ($request) {
                $orientation = collect($request->cards)
                    ->firstWhere('id', $card->id)['orientation'] ?? 'upright';
                return [
                    'name' => $card->name,
                    'image' => $card->image,
                    'orientation' => $orientation
                ];
            });

            [$oracleName, $oracleMessage] = $this->generateOracleMessage();

            if ($session->cards_drawn && count($session->cards_drawn) > 0 && $session->ai_prediction != null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kamu sudah memilih kartu takdirmu hari ini.'
                ], 400);
            }
            if ($session->cards_drawn && count($session->cards_drawn) > 0 && $session->ai_prediction == null) {
                // $ai_response = $this->runAiReading($user, $request->session_id, $oracleName);
                return response()->json([
                    'status' => 'success',
                    'message' => $oracleMessage,
                    'oracle' => $oracleName,
                    'session_id' => $session->id,
                    'cards' => $session->cards_drawn,
                    // 'ai_response' => $ai_response->original['message'],
                ]);
            }
            $session->update([
                'name' => $request->name,
                'energy_choice' => $request->energy_choice,
                'cards_drawn' => $cardsFormatted
            ]);
            return response()->json([
                'status' => 'success',
                'message' => $oracleMessage,
                'oracle' => $oracleName,
                'session_id' => $session->id,
                'cards' => $cardsFormatted,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message ' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memilih kartu.'
            ], 500);
        }
    }

    /**
     * Generate AI reading based on cards
     */
    public function aiReading(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|uuid|exists:tarot_user_readings,id',
                'oracle_name' => 'required|string|in:Rasi Nirmala,Sang Hyang Taya,Nusa Tirta,Sri Arkamaya,Ki Samirana,Nyai Wening,Rayi Lelana,Astra Nura,Lirathiel,Nyai Aruna,The Oracle of Nusa,Eldra Veil',
            ]);
            $user = $request->user();
            // Pastikan user valid
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }
            $session = TarotUserReading::where('id', $request->session_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi ritual tidak ditemukan.'
                ], 404);
            }
            if ($session->cards_drawn && count($session->cards_drawn) > 0 && $session->ai_prediction != null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kamu sudah memilih kartu takdirmu hari ini.'
                ], 400);
            }
            return $this->runAiReading($request->user(), $request->session_id, $request->oracle_name);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message ' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat meramal.'
            ], 500);
        }
    }

    private function runAiReading($user, $session_id, $oracleName)
    {
        try {

            // Pastikan user valid
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }


            $session = TarotUserReading::where('id', $session_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi ritual tidak ditemukan.'
                ], 404);
            }

            $today = Carbon::today();

            $userStat = TarotUserStat::where('user_id', $user->id)
                ->first();

            $name = $session->name;
            $cards = $session->cards_drawn;
            $energy = $session->energy_choice ?? 'misterius';
            $generator = new TarotAIService();
            $response = $generator->generateReading($cards, $energy, $name, $oracleName);

            $session->update([
                'reading_date' => $today,
                'ai_prediction' => $response,
                'is_complete' => true,
            ]);

            $userStat ->update([
                'total_readings' => $userStat->total_readings + 1,
                'last_reading_at' => Carbon::now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $response
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message ' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat meramal.'
            ], 500);
        }
    }

    /**
     * Get user reading history
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $history = TarotUserStat::with('readings')
            ->where('user_id', $user->id)
            ->orderByDesc('session_date')
            ->take(10)
            ->get();

        return response()->json($history);
    }

    private function generateOracleMessage(): array
    {
        $oracles = [
            "Rasi Nirmala",
            "Sang Hyang Taya",
            "Nusa Tirta",
            "Sri Arkamaya",
            "Ki Samirana",
            "Nyai Wening",
            "Rayi Lelana",
            "Astra Nura",
            "Lirathiel",
            "Nyai Aruna",
            "The Oracle of Nusa",
            "Eldra Veil",
        ];

        $messages = [
            " tersenyum samar. Takdir mulai berputar.",
            " melihat energi halus bergetar di sekelilingmu...",
            " menatap masa depan, kabut mulai menipis.",
            " menatap jauh ke dalam cermin nasibmu...",
            " berbisik pelan, ‘apa yang kau cari akan segera terungkap.’",
            " menutup matanya, lalu berkata, 'energi ini... unik sekali.'",
            " mengamati aura di sekitarmu dengan pandangan lembut.",
            " berkata, ‘ada sesuatu dari masa lalu yang belum selesai.’",
        ];

        $oracle = $oracles[array_rand($oracles)];
        $message = $oracle . $messages[array_rand($messages)];

        return [$oracle, $message];
    }
}
