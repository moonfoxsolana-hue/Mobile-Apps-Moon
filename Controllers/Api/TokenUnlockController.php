<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TokenUnlock;

class TokenUnlockController extends Controller
{
    public function index()
    {
        $data = TokenUnlock::all()->map(function ($item) {
            $item->percentage = $item->amount_token > 0
                ? round(($item->unlock_token / $item->amount_token) * 100, 2)
                : 0;
            return $item;
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
