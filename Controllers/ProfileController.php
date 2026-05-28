<?php

namespace App\Http\Controllers;

use App\Models\UserTokenHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'wallet_address' => $request->user()->wallet_address,
            'total_token' => number_format($request->user()->total_token, 0, ',', '.'),
            'has_claimed' => $request->user()->has_claimed,
            'locked_balance' => number_format(($request->user()->locked_balance ?? 0), 0, ',', '.'),
        ]);
    }
    public function tokenHistory(Request $request)
    {
        $user = $request->user();

        $histories = UserTokenHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'type', 'amount', 'action', 'description', 'created_at'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'amount' => number_format($item->amount, 0, ',', '.'),
                    'action' => $item->action,
                    'description' => $item->description,
                    'created_at' =>  Carbon::parse($item->created_at)->format('d-m-Y H:i'),
                ];
            });

        return response()->json($histories);
    }
}
