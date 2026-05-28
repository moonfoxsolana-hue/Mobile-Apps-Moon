<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    // 🔐 Register user baru
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // butuh password_confirmation
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // 🔑 Login user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau Password salah'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // 🧾 Update wallet address (hanya bisa 1x)
    public function updateWallet(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|unique:users,wallet_address',
        ]);

        $user = $request->user();

        if ($user->wallet_address) {
            return response()->json(['error' => 'Wallet already set'], 400);
        }

        $user->wallet_address = $request->wallet_address;
        $user->save();

        return response()->json(['message' => 'Wallet address updated']);
    }
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        Log::info('Logging out user ID: ' . $request->user()->id);
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
