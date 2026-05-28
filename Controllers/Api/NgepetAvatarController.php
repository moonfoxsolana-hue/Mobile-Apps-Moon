<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NgepetAvatar;
use App\Models\NgepetAvatarPurchase;
use App\Models\NgepetUserAvatar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NgepetAvatarController extends Controller
{
    public function shoplist(Request $request)
    {
        try {
            $user = $request->user();

            // Ambil semua avatar
            $avatars = NgepetAvatar::select('id', 'name', 'image_url', 'price', 'stock', 'tier')->get();

            // Ambil daftar avatar yang sudah dimiliki user
            $ownedAvatarIds = NgepetUserAvatar::where('user_id', $user->id)
                ->pluck('avatar_id')
                ->toArray();

            // Tambahkan properti "own" ke setiap item
            $avatars = $avatars->map(function ($avatar) use ($ownedAvatarIds) {
                $avatar->own = in_array($avatar->id, $ownedAvatarIds) ? 1 : 0;
                return $avatar;
            });

            return response()->json([
                'status' => 'success',
                'data' => $avatars
            ]);
        } catch (\Exception $e) {
            Log::error('Shoplist Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat daftar avatar.'
            ], 500);
        }
    }

    public function buy(Request $request, $id)
    {
        $user = $request->user();

        // 1. Ambil data avatar
        $avatar = NgepetAvatar::find($id);
        if (! $avatar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Avatar tidak ditemukan.'
            ], 404);
        }

        // 2. Cek stok
        if ($avatar->stock <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok avatar ini sudah habis.'
            ], 400);
        }
        // 3. Cek kepemilikan avatar
        $alreadyOwned = NgepetUserAvatar::where('user_id', $user->id)
            ->where('avatar_id', $avatar->id)
            ->exists();
        if ($alreadyOwned) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu sudah memiliki avatar ini.'
            ], 400);
        }

        // 4. Cek saldo token user
        if ($user->total_token < $avatar->price) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token kamu tidak cukup untuk membeli avatar ini.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // 5. Catat pembelian di ngepet_avatar_purchases
            NgepetAvatarPurchase::create([
                'user_id' => $user->id,
                'avatar_id' => $avatar->id,
                'price_paid' => $avatar->price,
                'purchased_at' => now()
            ]);

            // 6. Tambah ke tabel kepemilikan avatar
            NgepetUserAvatar::create([
                'user_id' => $user->id,
                'avatar_id' => $avatar->id,
                'is_equipped' => false
            ]);

            // 7. Kurangi token user
            $user->decrement('total_token', $avatar->price);

            // 8. Kurangi stok avatar
            $avatar->decrement('stock');
            $user->logTokenHistory(
                type: 'games',
                action: 'subtract',
                amount: $avatar->price,
                description: 'Pembelian avatar game "Ngepet Online".'
            );
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Avatar berhasil dibeli.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membeli avatar.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function myavatars()
    {
        $user = auth()->user();

        $avatars = NgepetUserAvatar::with('avatar:id,name,image_url,tier,type')
            ->where('user_id', $user->id)
            ->select('id', 'avatar_id', 'is_equipped')
            ->get()
            ->makeHidden(['avatar_id']);

        return response()->json([
            'status' => 'success',
            'data' => $avatars
        ]);
    }
    public function equip(Request $request, $id)
    {
        $user = auth()->user();

        // 1. Cek kepemilikan avatar
        $userAvatar = NgepetUserAvatar::where('avatar_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$userAvatar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Avatar tidak ditemukan atau bukan milik kamu.'
            ], 404);
        }

        // 2. Reset semua avatar jadi tidak terpakai
        NgepetUserAvatar::where('user_id', $user->id)
            ->update(['is_equipped' => false]);

        // 3. Set avatar terpilih jadi terpakai
        $userAvatar->update(['is_equipped' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Avatar berhasil dipakai.'
        ]);
    }
}
