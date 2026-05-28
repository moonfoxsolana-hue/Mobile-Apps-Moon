<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AirdropClaim;
use App\Models\ClaimCode;
use App\Models\TokenUnlock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AirdropController extends Controller
{
    public function claim(Request $request)
    {
        // ✅ Validasi input
        $request->validate([
            //'wallet_address' => 'required|string|max:255',
            'wallet_address' => [
                'required',
                'string',
                'max:255',
                'regex:/^[1-9A-HJ-NP-Za-km-z]{32,44}$/'
            ],
        ]);

        $user = $request->user();

        // ✅ Cek apakah sudah pernah klaim airdrop pertama
        $alreadyClaimed = AirdropClaim::where('user_id', $user->id)
            ->where('is_first', true)
            ->exists();

        if ($alreadyClaimed) {
            return response()->json([
                'message' => 'Kamu sudah klaim airdrop pertama.'
            ], 403);
        }

        // ✅ Cek apakah wallet sudah digunakan user lain
        $walletUsed = User::where('wallet_address', $request->wallet_address)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($walletUsed) {
            return response()->json([
                'message' => 'Wallet address sudah digunakan oleh pengguna lain.'
            ], 409);
        }

        // ✅ Cek jika user sudah punya wallet, tidak boleh diganti
        if ($user->wallet_address && $user->wallet_address !== $request->wallet_address) {
            return response()->json([
                'message' => 'Kamu sudah mengatur wallet sebelumnya dan tidak bisa diganti.'
            ], 422);
        }

        // ✅ Simpan wallet jika belum ada
        if (!$user->wallet_address) {
            $user->wallet_address = $request->wallet_address;
        }

        // ✅ Pastikan kolom token tidak null
        if (is_null($user->total_token)) {
            $user->total_token = 0;
        }

        // 🎁 Tambahkan token ke user
        $amount = 1000; // jumlah token untuk klaim pertama
        $user->total_token += $amount;
        $user->save();

        // 📜 Catat klaim pertama ke tabel airdrop_claims
        AirdropClaim::create([
            'user_id' => $user->id,
            'wallet_address' => $request->wallet_address,
            'amount' => $amount,
            'claimed_at' => now(),
            'is_first' => true
        ]);

        // 📈 Tambahkan ke progress token unlock
        TokenUnlock::where('id', 1)
            ->increment('unlock_token', $amount);

        // Catat history
        $user->logTokenHistory(
            type: 'airdrop',
            action: 'add',
            amount: $amount,
            description: 'Mendapatkan Airdrop sebesar ' . $amount . ' token'
        );

        return response()->json([
            'message' => 'Klaim pertama berhasil!',
            'amount' => $amount
        ]);
    }

    public function claimWithCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $user = $request->user();

        // ❌ Cek apakah sudah klaim hari ini
        $todayClaim = AirdropClaim::where('user_id', $user->id)
            ->whereDate('claimed_at', now()->toDateString())
            ->exists();

        if ($todayClaim) {
            return response()->json(['message' => 'Kamu sudah klaim hari ini.'], 403);
        }

        // 🔍 Cari kode klaim
        $claimCode = ClaimCode::where('code', $request->code)->first();

        if (! $claimCode) {
            return response()->json(['message' => 'Kode klaim tidak valid.'], 404);
        }

        if ($claimCode->claimed >= $claimCode->quota) {
            return response()->json(['message' => 'Kuota kode klaim ini telah habis.'], 403);
        }

        // 🔒 Transaksi aman
        DB::transaction(function () use ($user, $claimCode) {
            // ➕ Tambahkan token ke user
            $user->total_token = ($user->total_token ?? 0) + $claimCode->amount;
            $user->save();

            // 🧾 Simpan riwayat klaim
            AirdropClaim::create([
                'user_id' => $user->id,
                'wallet_address' => $user->wallet_address,
                'amount' => $claimCode->amount,
                'claimed_at' => now(),
                'is_first' => false
            ]);

            // 📌 Update jumlah klaim kode
            $claimCode->increment('claimed');

            // 📈 Tambah ke unlock progress
            TokenUnlock::where('id', 1)->increment('unlock_token', $claimCode->amount);
        });
                $user->logTokenHistory(
            type: 'airdrop',
            action: 'add',
            amount: $claimCode->amount,
            description: 'Mendapatkan Airdrop sebesar ' . $claimCode->amount . ' token'
        );

        return response()->json([
            'message' => 'Klaim berhasil!',
            'amount' => $claimCode->amount
        ]);
    }
}
