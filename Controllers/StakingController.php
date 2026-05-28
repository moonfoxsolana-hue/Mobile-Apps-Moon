<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StakingType;
use App\Models\StakingDuration;
use App\Models\UserStaking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\TokenUnlock;

class StakingController extends Controller
{
    public function getTypes()
    {
        $types = StakingType::with('durations')->get();
        return response()->json($types);
    }

    public function stake(Request $request)
    {
        $request->validate([
            'type_id' => 'required|exists:staking_types,id',
            'duration_id' => 'required|exists:staking_durations,id',
        ]);

        $user = $request->user();
        $type = StakingType::findOrFail($request->type_id);
        $duration = StakingDuration::where('id', $request->duration_id)
            ->where('staking_type_id', $type->id)
            ->firstOrFail();

        // Gunakan total_token sebagai saldo utama
        if ($user->total_token < $type->amount_token) {
            return response()->json([
                'error' => 'Token tidak cukup'
                // 'error' => 'Token tidak cukup. Token Anda: ' . number_format($user->total_token, 0, '.', '') . ' token, dibutuhkan: ' . $type->amount_token . ' token.'
            ], 400);
        }

        $reward = round(($type->amount_token * ($type->apr / 100)) * ($duration->days / 365), 0);
        logger()->info('Reward calculation', [
            'amount_token' => $type->amount_token,
            'apr' => $duration->apr,
            'days' => $duration->days,
            'reward' => $reward,
        ]);
        $user->total_token -= $type->amount_token;
        $user->locked_balance += $type->amount_token;
        $user->save();

        // Simpan staking
        $staking = UserStaking::create([
            'user_id' => $user->id,
            'staking_type_id' => $type->id,
            'staking_duration_id' => $duration->id,
            'amount' => $type->amount_token,
            'expected_reward' => $reward,
            'start_date' => now(),
            'end_date' => now()->addDays($duration->days),
        ]);
        $user->logTokenHistory(
            type: 'staking',
            action: 'subtract',
            amount: $type->amount_token,
            description: 'Melakukan Staking sebesar ' . $type->amount_token . ' token'
        );

        return response()->json([
            'message' => 'Staking berhasil',
            'staking' => $staking
        ]);
    }
    public function index(Request $request)
    {
        return UserStaking::withTrashed()
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(function ($staking) {
                $staking->status = $staking->deleted_at
                    ? 'cancelled'
                    : ($staking->claimed ? 'claimed' : 'active');
                $staking->amount = number_format($staking->amount, 0, ',', '.');
                $staking->expected_reward = number_format($staking->expected_reward, 0, ',', '.');
                $staking->makeHidden(['staking_type_id', 'staking_duration_id', 'created_at', 'updated_at', 'deleted_at']);
                return $staking;
            })
            ->sortBy(function ($staking) {
                // Buat prioritas urutan: active = 0, claimed = 1, cancelled = 2
                return match ($staking->status) {
                    'active' => 0,
                    'claimed' => 1,
                    'cancelled' => 2,
                    default => 3,
                };
            })
            ->values(); // reset index agar hasilnya array 0-based lagi
    }

    public function claim($id, Request $request)
    {
        try {
            $staking = UserStaking::where('id', $id)
                ->where('user_id', $request->user()->id) // hanya milik user terkait
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Data staking tidak ditemukan.'], 404);
        }

        if (now()->lt($staking->end_date)) {
            return response()->json(['message' => 'Staking belum selesai.'], 400);
        }

        if ($staking->claimed) {
            return response()->json(['message' => 'Reward sudah di-claim.'], 400);
        }
        if ($staking->deleted_at) {
            return response()->json(['message' => 'Staking sudah dibatalkan.'], 400);
        }
        try {
            $user = $request->user();

            // Tambahkan amount dan reward ke total_token
            $totalReward = $staking->amount + $staking->expected_reward;
            $user->total_token += $totalReward;

            // Kurangi locked_balance
            $user->locked_balance -= $staking->amount;

            // Simpan perubahan
            $staking->claimed = true;
            $staking->save();
            $user->save();
            $user->logTokenHistory(
                type: 'staking',
                action: 'add',
                amount: $totalReward,
                description: 'Mendapatkan hasil reward dari Staking sebesar ' . $totalReward . ' token'
            );
            TokenUnlock::where('id', 2)->increment('unlock_token', $staking->expected_reward);
            return response()->json(['message' => 'Reward berhasil di-claim.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat proses claim.'], 500);
        }
    }

    //     public function cancel($id, Request $request)
    //     {
    //         try {
    //             $staking = UserStaking::where('id', $id)
    //                 ->where('user_id', $request->user()->id)
    //                 ->firstOrFail();
    //         } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //             return response()->json(['message' => 'Data staking tidak ditemukan.'], 404);
    //         }

    //         if ($staking->deleted_at) {
    //             return response()->json(['message' => 'Staking sudah dibatalkan.'], 400);
    //         }

    //         if ($staking->claimed) {
    //             return response()->json(['message' => 'Staking sudah di-claim dan tidak bisa dibatalkan.'], 400);
    //         }

    //         DB::beginTransaction();
    //         try {

    //             $user = $staking->user;

    //             if ($user->locked_balance < $staking->amount) {
    //                 throw new \Exception('Saldo terkunci tidak mencukupi.');
    //             }

    //             $user->locked_balance -= $staking->amount;
    //             $user->total_token += $staking->amount;
    //             $user->save();

    //             $staking->delete();

    //             DB::commit();
    //             $user->logTokenHistory(
    //                 type: 'staking',
    //                 action: 'add',
    //                 amount: $staking->amount,
    //                 description: 'Melakukan cancel Staking sebesar ' . $staking->amount . ' token'
    //             );
    //             return response()->json(['message' => 'Staking berhasil dibatalkan.']);
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'message' => 'Gagal membatalkan staking.',
    //                 'error' => $e->getMessage()
    //             ], 500);
    //         }
    //     }
    // 
    public function cancel($id, Request $request)
    {
        try {
            $staking = UserStaking::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Data staking tidak ditemukan.'], 404);
        }

        if ($staking->deleted_at) {
            return response()->json(['message' => 'Staking sudah dibatalkan.'], 400);
        }

        if ($staking->claimed) {
            return response()->json(['message' => 'Staking sudah di-claim dan tidak bisa dibatalkan.'], 400);
        }

        DB::beginTransaction();
        try {
            $user = $staking->user;

            if ($user->locked_balance < $staking->amount) {
                throw new \Exception('Saldo terkunci tidak mencukupi.');
            }

            $today = now();
            $startDate = \Carbon\Carbon::parse($staking->start_date);
            $endDate = \Carbon\Carbon::parse($staking->end_date);

            $totalDays = $startDate->diffInDays($endDate);
            $stakedDays = $startDate->diffInDays($today);

            // Hitung reward proporsional jika staking minimal 1 hari
            $reward = 0;
            if ($stakedDays >= 1 && $totalDays > 0) {
                $reward = ($staking->expected_reward / $totalDays) * $stakedDays;
            }

            // Update user balance
            $user->locked_balance -= $staking->amount;
            $user->total_token += $staking->amount + $reward;
            $user->save();

            // Soft delete staking
            $staking->delete();

            // History pengembalian token staking
            $user->logTokenHistory(
                type: 'staking',
                action: 'add',
                amount: $staking->amount,
                description: 'Cancel staking: pengembalian ' . $staking->amount . ' token.'
            );

            // History reward proporsional (jika ada)
            if ($reward > 0) {
                $user->logTokenHistory(
                    type: 'staking',
                    action: 'add',
                    amount: $reward,
                    description: 'Reward proporsional karena cancel staking setelah ' . $stakedDays . ' hari.'
                );
            }
            TokenUnlock::where('id', 2)->increment('unlock_token', $reward);

            DB::commit();
            return response()->json([
                'message' => 'Staking berhasil dibatalkan.',
                'reward_received' => round($reward, 3)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal membatalkan staking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
